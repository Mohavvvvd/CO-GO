<?php
$servername = "localhost";  
$username = "root";
$password = "";
$dbname = "co_go";


$conx = new mysqli($servername, $username, $password, $dbname);

if ($conx->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>