<?php

header("Content-Type: application/json");

/*
Mengambil koneksi database dari Railway
Railway menyediakan DATABASE_URL otomatis
*/

$database_url = getenv("DATABASE_URL");

$conn = pg_connect($database_url);

if (!$conn) {
    echo json_encode([
        "status" => 500,
        "message" => "Koneksi database gagal"
    ], JSON_PRETTY_PRINT);
    exit;
}

?>
