<?php

$host = "localhost";
$db_user = "USERNAME";
$db_pass = "PASSWORD";
$db_name = "DATABASE_NAME";

$conn = new mysqli($host, $db_user, $db_pass, $db_name);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

?>