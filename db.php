<?php

$host = "aws-1-ap-southeast-1.pooler.supabase.com";
$port = "5432";
$dbname = "postgres";
$user = "postgres.tfcsajqhjityosvvvgdz";
$password = "Nurhasanah1";

$conn = pg_connect(
"host=$host port=$port dbname=$dbname user=$user password=$password"
);

if (!$conn) {
    echo json_encode([
        "status" => 500,
        "message" => "Koneksi database gagal"
    ]);
    exit;
}

?>