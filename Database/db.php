<?php

// Author: Noor Abdulkhaleq Alkhames

$servername = "localhost";
$username = "root";
$password = ""; 
$dbname = "itech_store";

$conn = new mysqli($servername, $username, $password, $dbname, 3306); 

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>