<?php

header("Content-Type: application/json");

require_once "db.php";

$method = $_SERVER['REQUEST_METHOD'];

if ($method == 'GET') {

    if (isset($_GET['id'])) {

        $id = intval($_GET['id']);

        $query = "
        SELECT 
        characters.id,
        characters.character_name,
        rarities.rarity_name,
        regions.region_name,
        elements.element_name,
        weapons.weapon_name,
        characters.constellation
        FROM characters
        LEFT JOIN rarities ON characters.rarity_id = rarities.id
        LEFT JOIN regions ON characters.region_id = regions.id
        LEFT JOIN elements ON characters.element_id = elements.id
        LEFT JOIN weapons ON characters.weapon_id = weapons.id
        WHERE characters.id = $id
        ";

        $result = pg_query($conn, $query);
        $data = pg_fetch_assoc($result);

        echo json_encode($data, JSON_PRETTY_PRINT);

    } else {

        $query = "
        SELECT 
        characters.id,
        characters.character_name,
        rarities.rarity_name,
        regions.region_name,
        elements.element_name,
        weapons.weapon_name,
        characters.constellation
        FROM characters
        LEFT JOIN rarities ON characters.rarity_id = rarities.id
        LEFT JOIN regions ON characters.region_id = regions.id
        LEFT JOIN elements ON characters.element_id = elements.id
        LEFT JOIN weapons ON characters.weapon_id = weapons.id
        ORDER BY characters.id
        ";

        $result = pg_query($conn, $query);

        $data = [];

        while ($row = pg_fetch_assoc($result)) {
            $data[] = $row;
        }

        echo json_encode($data, JSON_PRETTY_PRINT);
    }

}

elseif ($method == 'POST') {

    if (!isset($_POST['character_name'])) {

        echo json_encode([
            "status" => 400,
            "message" => "Data tidak lengkap"
        ], JSON_PRETTY_PRINT);

        exit;
    }

    $character_name = $_POST['character_name'];
    $rarity_id = $_POST['rarity_id'];
    $region_id = $_POST['region_id'];
    $element_id = $_POST['element_id'];
    $weapon_id = $_POST['weapon_id'];
    $constellation = $_POST['constellation'];

    $query = "
    INSERT INTO characters 
    (character_name, rarity_id, region_id, element_id, weapon_id, constellation)
    VALUES 
    ('$character_name','$rarity_id','$region_id','$element_id','$weapon_id','$constellation')
    ";

    $result = pg_query($conn, $query);

    if ($result) {

        echo json_encode([
            "status" => 200,
            "message" => "Character berhasil ditambahkan"
        ], JSON_PRETTY_PRINT);

    } else {

        echo json_encode([
            "status" => 500,
            "message" => "Gagal menambahkan data"
        ], JSON_PRETTY_PRINT);
    }

}

elseif ($method == 'PUT') {

    parse_str(file_get_contents("php://input"), $_PUT);

    if (!isset($_PUT['id'])) {

        echo json_encode([
            "status" => 400,
            "message" => "ID tidak ditemukan"
        ], JSON_PRETTY_PRINT);

        exit;
    }

    $id = intval($_PUT['id']);

    $character_name = $_PUT['character_name'];
    $rarity_id = $_PUT['rarity_id'];
    $region_id = $_PUT['region_id'];
    $element_id = $_PUT['element_id'];
    $weapon_id = $_PUT['weapon_id'];
    $constellation = $_PUT['constellation'];

    $query = "
    UPDATE characters SET
    character_name = '$character_name',
    rarity_id = '$rarity_id',
    region_id = '$region_id',
    element_id = '$element_id',
    weapon_id = '$weapon_id',
    constellation = '$constellation'
    WHERE id = $id
    ";

    $result = pg_query($conn, $query);

    if ($result) {

        $rows = pg_affected_rows($result);

        if ($rows > 0) {

            echo json_encode([
                "status" => 200,
                "message" => "Character berhasil diupdate"
            ], JSON_PRETTY_PRINT);

        } else {

            echo json_encode([
                "status" => 404,
                "message" => "Data tidak ditemukan atau tidak berubah"
            ], JSON_PRETTY_PRINT);
        }

    } else {

        echo json_encode([
            "status" => 500,
            "message" => "Query update gagal"
        ], JSON_PRETTY_PRINT);
    }

}

elseif ($method == 'DELETE') {

    if (isset($_GET['id'])) {

        $id = intval($_GET['id']);

    } else {

        parse_str(file_get_contents("php://input"), $_DELETE);
        $id = intval($_DELETE['id'] ?? 0);

    }

    if (!$id) {

        echo json_encode([
            "status" => 400,
            "message" => "ID tidak ditemukan"
        ], JSON_PRETTY_PRINT);

        exit;
    }

    $query = "DELETE FROM characters WHERE id = $id";

    $result = pg_query($conn, $query);

    if ($result) {

        $rows = pg_affected_rows($result);

        if ($rows > 0) {

            echo json_encode([
                "status" => 200,
                "message" => "Character berhasil dihapus"
            ], JSON_PRETTY_PRINT);

        } else {

            echo json_encode([
                "status" => 404,
                "message" => "Data tidak ditemukan"
            ], JSON_PRETTY_PRINT);
        }

    } else {

        echo json_encode([
            "status" => 500,
            "message" => "Gagal menghapus data"
        ], JSON_PRETTY_PRINT);
    }

}

?>
