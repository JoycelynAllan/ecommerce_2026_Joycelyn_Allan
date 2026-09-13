<?php

$host = "localhost";
$db_user = "joycelyn.allan";
$db_pass = "Jalla@123";
$db_name = "ecommerce_2026A_joycelyn_allan";

$conn = new mysqli($host, $db_user, $db_pass, $db_name);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

?>