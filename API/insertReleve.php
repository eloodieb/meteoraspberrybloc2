<?php
// SET HEADER
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: access");
header("Access-Control-Allow-Methods: POST");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// INCLUDING DATABASE AND MAKING OBJECT
require 'database.php';
$db_connection = new Database();
$conn = $db_connection->dbConnection();

// GET DATA FORM REQUEST
$data = json_decode(file_get_contents("php://input"));

//CREATE MESSAGE ARRAY AND SET EMPTY
$msg['message'] = '';

// CHECK IF RECEIVED DATA FROM THE REQUEST
if (isset($data->temperature) && isset($data->humidity)) {
    // CHECK DATA VALUE IS EMPTY OR NOT
    if (!empty($data->temperature) && !empty($data->humidity)) {

        $insert_query = "INSERT INTO `sensor_data`(temperature,humidity) VALUES(:temperature,:humidity)";

        $insert_stmt = $conn->prepare($insert_query);
        // DATA BINDING
        $insert_stmt->bindValue(':temperature', htmlspecialchars(strip_tags($data->temperature)), PDO::PARAM_INT);
        $insert_stmt->bindValue(':humidity', htmlspecialchars(strip_tags($data->humidity)), PDO::PARAM_INT);

        if ($insert_stmt->execute()) {
            $msg['message'] = 'Donnée insérée avec succès';
        } else {
            $msg['message'] = 'Erreur d\'insertion des données';
        }
    } else {
        $msg['message'] = 'Oops! veuillez remplir les champs vides';
    }
} else {
    $msg['message'] = 'Remplissez tout les champs | temperature, humidity';
}
//ECHO DATA IN JSON FORMAT
echo json_encode($msg);
