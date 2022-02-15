<?php
// SET HEADER
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: access");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Allow-Credentials: true");
header("Content-Type: application/json; charset=UTF-8");

// INCLUDING DATABASE AND MAKING OBJECT
require 'database.php';
$db_connection = new Database();
$conn = $db_connection->dbConnection();

// CHECK GET ID PARAMETER OR NOT
if (isset($_GET['id'])) {
    //IF HAS ID PARAMETER
    $releve_id = filter_var($_GET['id'], FILTER_VALIDATE_INT, [
        'options' => [
            'default' => 'all_posts',
            'min_range' => 1
        ]
    ]);
} else {
    $releve_id = 'all_posts';
}

// MAKE SQL QUERY
// IF GET POSTS ID, THEN SHOW POSTS BY ID OTHERWISE SHOW ALL POSTS
$sql = is_numeric($releve_id) ? "SELECT * FROM `sensor_data` WHERE id='$releve_id'" : "SELECT * FROM `sensor_data`";

$stmt = $conn->prepare($sql);

$stmt->execute();

//CHECK WHETHER THERE IS ANY POST IN OUR DATABASE
if ($stmt->rowCount() > 0) {
    // CREATE POSTS ARRAY
    $releves_array = [];

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {

        $releves_data = [
            'id' => $row['id'],
            'temperature' => html_entity_decode($row['temperature']),
            'humidity' => $row['humidity']

        ];
        // PUSH POST DATA IN OUR $posts_array ARRAY
        array_push($releves_array, $releves_data);
    }
    //SHOW POST/POSTS IN JSON FORMAT
    echo json_encode($releves_array);
} else {
    //IF THER IS NO POST IN OUR DATABASE
    echo json_encode(['message' => 'Aucune donnée disponible']);
}
