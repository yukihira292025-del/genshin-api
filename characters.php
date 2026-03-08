<?php

header("Content-Type: application/json");

require_once "db.php";

$method = $_SERVER['REQUEST_METHOD'];

if ($method == 'GET') {

    if (isset($_GET['id'])) {

        $id = $_GET['id'];

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
        JOIN rarities ON characters.rarity_id = rarities.id
        JOIN regions ON characters.region_id = regions.id
        JOIN elements ON characters.element_id = elements.id
        JOIN weapons ON characters.weapon_id = weapons.id
        WHERE characters.id = $id
        ";

        $result = pg_query($conn, $query);
        $data = pg_fetch_assoc($result);

        echo json_encode($data);

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
        JOIN rarities ON characters.rarity_id = rarities.id
        JOIN regions ON characters.region_id = regions.id
        JOIN elements ON characters.element_id = elements.id
        JOIN weapons ON characters.weapon_id = weapons.id
        ORDER BY characters.id
        ";

        $result = pg_query($conn, $query);

        $data = [];

        while ($row = pg_fetch_assoc($result)) {
            $data[] = $row;
        }

        echo json_encode($data);
    }

}

elseif ($method == 'POST') {

    if (!isset($_POST['character_name'])) {
        echo json_encode([
            "status" => 400,
            "message" => "Data tidak lengkap"
        ]);
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
        ]);

    } else {

        echo json_encode([
            "status" => 500,
            "message" => "Gagal menambahkan data"
        ]);
    }

}

elseif ($method == 'PUT') {

    parse_str(file_get_contents("php://input"), $_PUT);

    if (!isset($_PUT['id'])) {
        echo json_encode([
            "status" => 400,
            "message" => "ID tidak ditemukan"
        ]);
        exit;
    }

    $id = $_PUT['id'];
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
            ]);

        } else {

            echo json_encode([
                "status" => 404,
                "message" => "Data tidak ditemukan atau tidak berubah"
            ]);
        }

    } else {

        echo json_encode([
            "status" => 500,
            "message" => "Query update gagal"
        ]);
    }

}

elseif ($method == 'DELETE') {

    if (isset($_GET['id'])) {

        $id = $_GET['id'];

    } else {

        parse_str(file_get_contents("php://input"), $_DELETE);
        $id = $_DELETE['id'] ?? null;

    }

    if (!$id) {

        echo json_encode([
            "status" => 400,
            "message" => "ID tidak ditemukan"
        ]);
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
            ]);

        } else {

            echo json_encode([
                "status" => 404,
                "message" => "Data tidak ditemukan"
            ]);
        }

    } else {

        echo json_encode([
            "status" => 500,
            "message" => "Gagal menghapus data"
        ]);
    }

}

?>