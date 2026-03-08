<?php

header("Content-Type: application/json");

require_once "db.php";

$method = $_SERVER['REQUEST_METHOD'];

switch($method){

/* =========================
   GET DATA
========================= */
case 'GET':

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
";

$conditions = [];
$params = [];
$index = 1;

/* filter id */
if(isset($_GET['id'])){
    $conditions[] = "characters.id = $".$index;
    $params[] = intval($_GET['id']);
    $index++;
}

/* filter element */
if(isset($_GET['element'])){
    $conditions[] = "elements.element_name = $".$index;
    $params[] = $_GET['element'];
    $index++;
}

/* filter region */
if(isset($_GET['region'])){
    $conditions[] = "regions.region_name = $".$index;
    $params[] = $_GET['region'];
    $index++;
}

/* filter rarity */
if(isset($_GET['rarity'])){
    $conditions[] = "rarities.rarity_name = $".$index;
    $params[] = $_GET['rarity'];
    $index++;
}

if(!empty($conditions)){
    $query .= " WHERE ".implode(" AND ",$conditions);
}

$query .= " ORDER BY characters.id";

/* limit data */
if(isset($_GET['limit'])){
    $limit = intval($_GET['limit']);
    $query .= " LIMIT ".$limit;
}

$result = pg_query_params($conn,$query,$params);

$data = [];

while($row = pg_fetch_assoc($result)){
    $data[] = $row;
}

echo json_encode($data,JSON_PRETTY_PRINT);

break;


/* =========================
   INSERT DATA
========================= */
case 'POST':

$input = json_decode(file_get_contents("php://input"),true);

if(!isset($input['character_name'])){
    echo json_encode([
        "status"=>400,
        "message"=>"Data tidak lengkap"
    ],JSON_PRETTY_PRINT);
    exit;
}

$query = "
INSERT INTO characters
(character_name,rarity_id,region_id,element_id,weapon_id,constellation)
VALUES ($1,$2,$3,$4,$5,$6)
";

$result = pg_query_params($conn,$query,[
    $input['character_name'],
    $input['rarity_id'],
    $input['region_id'],
    $input['element_id'],
    $input['weapon_id'],
    $input['constellation']
]);

if($result){
    echo json_encode([
        "status"=>200,
        "message"=>"Character berhasil ditambahkan"
    ],JSON_PRETTY_PRINT);
}else{
    echo json_encode([
        "status"=>500,
        "message"=>"Gagal menambahkan data"
    ],JSON_PRETTY_PRINT);
}

break;


/* =========================
   UPDATE DATA
========================= */
case 'PUT':

$input = json_decode(file_get_contents("php://input"),true);

if(!isset($input['id'])){
    echo json_encode([
        "status"=>400,
        "message"=>"ID tidak ditemukan"
    ],JSON_PRETTY_PRINT);
    exit;
}

$query = "
UPDATE characters SET
character_name=$1,
rarity_id=$2,
region_id=$3,
element_id=$4,
weapon_id=$5,
constellation=$6
WHERE id=$7
";

$result = pg_query_params($conn,$query,[
    $input['character_name'],
    $input['rarity_id'],
    $input['region_id'],
    $input['element_id'],
    $input['weapon_id'],
    $input['constellation'],
    $input['id']
]);

if(pg_affected_rows($result) > 0){
    echo json_encode([
        "status"=>200,
        "message"=>"Character berhasil diupdate"
    ],JSON_PRETTY_PRINT);
}else{
    echo json_encode([
        "status"=>404,
        "message"=>"Data tidak ditemukan"
    ],JSON_PRETTY_PRINT);
}

break;


/* =========================
   DELETE DATA
========================= */
case 'DELETE':

if(isset($_GET['id'])){
    $id = intval($_GET['id']);
}else{
    $input = json_decode(file_get_contents("php://input"),true);
    $id = intval($input['id'] ?? 0);
}

if(!$id){
    echo json_encode([
        "status"=>400,
        "message"=>"ID tidak ditemukan"
    ],JSON_PRETTY_PRINT);
    exit;
}

$query = "DELETE FROM characters WHERE id=$1";

$result = pg_query_params($conn,$query,[$id]);

if(pg_affected_rows($result) > 0){
    echo json_encode([
        "status"=>200,
        "message"=>"Character berhasil dihapus"
    ],JSON_PRETTY_PRINT);
}else{
    echo json_encode([
        "status"=>404,
        "message"=>"Data tidak ditemukan"
    ],JSON_PRETTY_PRINT);
}

break;

}

?>
