<?php 
include("../config/connect.php");
$req = "SELECT typeLocal, NbLocal FROM local WHERE typeLocal IN ('public', 'private', 'meeting')";
$stmt = $conx->prepare($req);
$stmt->execute();
$result = $stmt->get_result();

$publicRooms = 0;
$privateRooms = 0;
$meetingRooms = 0;

while ($row = $result->fetch_assoc()) {
    switch ($row['typeLocal']) {
        case 'public':
            $publicRooms = $row['NbLocal'];
            break;
        case 'private':
            $privateRooms = $row['NbLocal'];
            break;
        case 'meeting':
            $meetingRooms = $row['NbLocal'];
            break;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Search for Available Equipment</title>
  <!-- Consolidated CSS links -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
  <style>
    body {
      display: flex;
      justify-content: center;
      align-items: center;
      flex-direction: column;
      background: linear-gradient(135deg, #f0f4f8, #d9e2ec);
      min-height: 100vh;
      width: 99vw;
      padding: 100PX;
    }
    .container{
      display: flex;
      justify-content: center;
      align-items: center;
      flex-direction: column;
      margin: auto;
    }
    h2 {
      color: #49A7FF;
    }
    .btn {
      background-color: #49A7FF;
      color: #ffffff;
    }
    .btn:hover {
      background-color: #0056b3;
    }
    .card {
      display: flex;
      justify-content: center;
      align-items: center;
      flex-direction: column;
      width: 80vw;
      margin: auto;
      transition: transform 0.3s ease, box-shadow 0.3s ease;
    }
    .card:hover {
      transform: scale(1.02);
      box-shadow: 0 0 30px rgba(0, 0, 0, 0.1);
    }
    .form-control:focus {
      box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
      border-color: #80bdff;
    }
    .stat-card {
      border-radius: 10px;
      padding: 25px 20px;
      margin: 10px;
      box-shadow: 0 4px 8px rgba(0,0,0,0.1);
      text-align: center;
      transition: transform 0.3s ease;
      height: 200px;
      margin: 15px auto;
      width: 200px;
    }
    .stat-card:hover {
      transform: translateY(-5px);
    }
    .stat-title {
      font-size: 1.1rem;
      color: #6c757d;
      margin-bottom: 10px;
    }
    .stat-value {
      font-size: 2.2rem;
      font-weight: bold;
      color: #343a40;
      margin: 10px 0;
    }
    .stat-icon {
      font-size: 2.5rem;
      margin-bottom: 15px;
      color: #0d6efd;
    }
    .available {
      color: #28a745;
      font-size: 20px;
    }
    .not-available {
      color: #dc3545;
      font-size: 20px;
    }
    @media (max-width: 768px) {
    body{
      padding:20px;
    }
  }
  </style>
</head>
<body>

<div class="container">
  <div class="card shadow-lg rounded-4 p-4 bg-white">
    <h2 class="mb-4 text-center">
      <i class="bi bi-search"></i> Find Available Equipment
    </h2>
    <div class="container mt-5">
      <div class="row">
          <!-- Public Rooms Card -->
          <div class="col-md-4">
              <div class="stat-card bg-white">
                  <i class="bi bi-door-open stat-icon"></i>
                  <div class="stat-title">Public rooms</div>
                  <div class="stat-value <?php echo ($publicRooms > 0) ? 'available' : 'not-available'; ?>">
                      <?php echo ($publicRooms > 0) ? $publicRooms . ' Available' : 'Not available'; ?>
                  </div>
              </div>
          </div>
          
          <!-- Private Rooms Card -->
          <div class="col-md-4">
              <div class="stat-card bg-white">
                  <i class="bi bi-lock-fill stat-icon"></i>
                  <div class="stat-title">Private rooms</div>
                  <div class="stat-value <?php echo ($privateRooms > 0) ? 'available' : 'not-available'; ?>">
                      <?php echo ($privateRooms > 0) ? $privateRooms . ' Available' : 'Not available'; ?>
                  </div>
              </div>
          </div>
          
          <!-- Meeting Rooms Card -->
          <div class="col-md-4">
              <div class="stat-card bg-white">
                  <i class="bi bi-people-fill stat-icon"></i>
                  <div class="stat-title">Meeting rooms</div>
                  <div class="stat-value <?php echo ($meetingRooms > 0) ? 'available' : 'not-available'; ?>">
                      <?php echo ($meetingRooms > 0) ? $meetingRooms . ' Available' : 'Not available'; ?>
                  </div>
              </div>
          </div>
      </div>
    </div>
    <form id="reservationForm">
      <div class="mb-3">
        <label for="desiredStart" class="form-label">
          <i class="bi bi-calendar-event"></i> Desired Date and Time
        </label>
        <input type="datetime-local" id="desiredStart" name="desiredStart" class="form-control" required>
      </div>

      <div class="mb-3">
        <label for="requiredEquipment" class="form-label">
          <i class="bi bi-tools"></i> Equipment Needed (comma-separated)
        </label>
        <input type="text" id="requiredEquipment" name="requiredEquipment" class="form-control" placeholder="monitor, whiteboard" required>
      </div>

      <button type="submit" class="btn w-100">
        <i class="bi bi-search"></i> Search
      </button>
    </form>

    <div id="result" class="mt-4"></div>
  </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="../js/sugggest.js"></script>
</body>
</html>