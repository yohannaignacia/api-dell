<?php

header("Content-Type: application/json");

$host = "localhost";
$user = "root";
$pass = "";
$db   = "dell_xps_store";

$conn = new mysqli(
    $host,
    $user,
    $pass,
    $db
);

if ($conn->connect_error) {

    die(
        json_encode([
            "success" => false,
            "message" => "Koneksi database gagal"
        ])
    );

}

$conn->set_charset("utf8mb4");