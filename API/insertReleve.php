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
if (isset($data->temperature) && isset($data->humidite) && isset($data->id_sonde)) {
    // CHECK DATA VALUE IS EMPTY OR NOT
    if (!empty($data->temperature) && !empty($data->humidite) && !empty($data->id_sonde)) {

        $insert_query = "INSERT INTO `releves`(temperature,humidite,id_sonde, created_at) VALUES(:temperature,:humidite,:id_sonde,:created_at)";

        $insert_stmt = $conn->prepare($insert_query);
        // DATA BINDING
        $insert_stmt->bindValue(':temperature', htmlspecialchars(strip_tags($data->temperature)), PDO::PARAM_INT);
        $insert_stmt->bindValue(':humidite', htmlspecialchars(strip_tags($data->humidite)), PDO::PARAM_INT);
        $insert_stmt->bindValue(':id_sonde', htmlspecialchars(strip_tags($data->id_sonde)), PDO::PARAM_INT);
        $insert_stmt->bindValue(':created_at', date("d-m-Y H:i:s"));

        if ($insert_stmt->execute()) {
            $msg['message'] = 'Data Inserted Successfully';
        } else {
            $msg['message'] = 'Data not Inserted';
        }
    } else {
        $msg['message'] = 'Oops! empty field detected. Please fill all the fields';
    }
} else {
    $msg['message'] = 'Please fill all the fields | temperature, humidite, id_sonde';
}
//ECHO DATA IN JSON FORMAT
echo json_encode($msg);
