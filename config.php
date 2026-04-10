<?php
session_start(); // start sessie voor winkelmand

$DB_HOST = "localhost";   // voor XAMPP
$DB_USER = "root";        // standaard XAMPP user
$DB_PASS = "";            // standaard leeg
$DB_NAME = "init";        // jouw database naam

$conn = new mysqli($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME);

if ($conn->connect_error) {
    die("Database connectie mislukt: " . $conn->connect_error);
}
