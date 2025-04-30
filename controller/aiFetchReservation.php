<?php
// controller/aiFetchReservation.php
include("../config/connect.php");
session_start();

header("Content-Type: application/json");
error_log("Fetching available reservation…");

function sendResponse(int $status, array $data) {
    http_response_code($status);
    echo json_encode($data);
    exit;
}

// 1) Only POST
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    sendResponse(405, ['message' => 'Method not allowed']);
}

// 2) Parse & validate JSON
$body = file_get_contents("php://input");
$data = json_decode($body, true);
if (
    !isset($data['desired_start'], $data['required_equipment']) 
    || !is_array($data['required_equipment'])
) {
    sendResponse(400, ['message' => 'Missing or invalid input']);
}

$desiredStart = $data['desired_start'];
$requiredEquipment = array_map('trim', $data['required_equipment']);

// Validate required equipment is not empty
if (empty($requiredEquipment)) {
    sendResponse(400, [
        'message' => 'At least one equipment item is required',
        'available_equipment' => getAvailableEquipmentList($conx)
    ]);
}

// 3) Stock check
$ph = implode(',', array_fill(0, count($requiredEquipment), '?'));
$sql = "SELECT nomEquipment, nbEquipement FROM equipement WHERE nomEquipment IN ($ph)";
$stmt = $conx->prepare($sql);
$stmt->bind_param(str_repeat('s', count($requiredEquipment)), ...$requiredEquipment);
$stmt->execute();
$res = $stmt->get_result();

$stock = [];
while ($r = $res->fetch_assoc()) {
    $stock[strtolower($r['nomEquipment'])] = (int) $r['nbEquipement'];
}
$stmt->close();

// 4) Partition into available / unavailable
$available = [];
$unavailable = [];
foreach ($requiredEquipment as $eq) {
    $k = strtolower($eq);
    if (isset($stock[$k])) {
        if ($stock[$k] > 0) {
            $available[] = $eq;
        } else {
            $unavailable[] = $eq;
        }
    } else {
        $unavailable[] = $eq;
    }
}

// Handle case where all equipment is available
if (empty($unavailable)) {
    sendResponse(200, [
        'available_equipment' => $available,
        'unavailable_equipment' => [],
        'message' => 'All requested equipment is available immediately',
        'immediate_availability' => true
    ]);
}

// 5) Fetch reservations only related to unavailable equipment
$reservations = [];
if (!empty($unavailable)) {
    $conds = [];
    $params = [];
    foreach ($unavailable as $eq) {
        $conds[] = "equipement LIKE ?";
        $params[] = "%$eq%";
    }
    $where = implode(' OR ', $conds);

    $query = "SELECT * FROM reservation WHERE $where";
    $stmt = $conx->prepare($query);

    if ($stmt === false) {
        sendResponse(500, ['message' => 'Database error: ' . $conx->error]);
    }

    $stmt->bind_param(str_repeat('s', count($params)), ...$params);
    $stmt->execute();
    $res = $stmt->get_result();

    while ($row = $res->fetch_assoc()) {
        $eqs = json_decode($row['equipement'], true);
        if (is_array($eqs)) {
            if (array_keys($eqs) !== range(0, count($eqs) - 1)) {
                $eqs = array_keys($eqs);
            }
        } else {
            $eqs = explode(',', $row['equipement']);
        }
        $eqs = array_map(fn($i) => trim(strtolower($i)), $eqs);

        $reservations[] = [
            'user_id' => (string) $row['idUser'],
            'resource_id' => (int) $row['numLocal'],
            'start' => $row['startDate'],
            'end' => $row['endDate'],
            'occupied_equipment' => $eqs
        ];
    }
    $stmt->close();
}

// NEW: Handle case where items are unavailable and not in any reservations
if (!empty($unavailable) && empty($reservations)) {
    sendResponse(200, [
        'available_equipment' => $available,
        'unavailable_equipment' => $unavailable,
        'message' => 'The following items are not currently available: ' . 
                    implode(', ', $unavailable) . 
                    '. Please check back later.',
        'immediate_availability' => false,
        'next_possibility' => null
    ]);
}

// 6) Call FastAPI
$payload = json_encode([
    'desired_start' => $desiredStart,
    'required_equipment' => $unavailable,
    'reservations' => $reservations
]);
error_log("DEBUG → Payload to Python: $payload");

$ch = curl_init("http://localhost:8000/available");
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST => true,
    CURLOPT_POSTFIELDS => $payload,
    CURLOPT_HTTPHEADER => [
        "Content-Type: application/json",
        "Content-Length: " . strlen($payload)
    ],
]);
$response = curl_exec($ch);
$status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$error = curl_error($ch);
curl_close($ch);

if ($error) {
    sendResponse(500, ['message' => "API error: $error"]);
}

// Decode safely
$apiData = json_decode($response, true);
if (json_last_error() !== JSON_ERROR_NONE) {
    sendResponse(500, ['message' => 'Invalid JSON response from API']);
}

error_log('API Response: ' . json_encode($apiData));

// 7a) If Python returned a reservation
if ($status === 200 && isset($apiData['user_id'])) {
    sendResponse(200, [
        'user_id' => $apiData['user_id'],
        'resource_id' => $apiData['resource_id'],
        'start' => $apiData['start'],
        'end' => $apiData['end'],
        'occupied_equipment' => $apiData['occupied_equipment']
    ]);
}

// 7b) If Python returned next_availability
if ($status === 200 && isset($apiData['next_availability'])) {
    sendResponse(200, [
        'available_equipment' => $available,
        'unavailable_equipment' => $unavailable,
        'next_availability' => $apiData['next_availability']
    ]);
}

// 8) Fallback
sendResponse(404, ['message' => 'No available reservation found']);

function getAvailableEquipmentList($conx) {
    $result = $conx->query("SELECT nomEquipment FROM equipement WHERE nbEquipement > 0");
    return $result->fetch_all(MYSQLI_COLUMN, 0);
}
?>