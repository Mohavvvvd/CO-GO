<?php
include("../config/connect.php");

$equipementDb = [];

$req = $conx->prepare('SELECT nomEquipment FROM equipement');
$req->execute();
$result = $req->get_result();

while ($row = $result->fetch_assoc()) {
    $nbEquipement = $conx->prepare('SELECT nbEquipement FROM equipement WHERE nomEquipment = ?');
    $nbEquipement->bind_param('s', $row['nomEquipment']);
    $nbEquipement->execute();

    $nbResult = $nbEquipement->get_result();
    $nbRow = $nbResult->fetch_assoc();
    
    if ($nbRow) {
        $equipementDb[$row['nomEquipment']] = $nbRow['nbEquipement'];
    }
}

$listEquipement = json_encode($equipementDb);

echo $listEquipement;

error_log($listEquipement);
?>
