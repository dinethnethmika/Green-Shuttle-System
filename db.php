<?php
$servername = "sql213.infinityfree.com";
$username = "if0_40678300";
$password = ""; 
$dbname = "if0_40678300_shuttle";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
session_start();
?>