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
if (isset($data->nom) && isset($data->lieu)) {
    // CHECK DATA VALUE IS EMPTY OR NOT
    if (!empty($data->nom) && !empty($data->lieu)) {

        $insert_query = "INSERT INTO `sonde`(nom,lieu, created_at) VALUES(:nom,:lieu,:created_at)";

        $insert_stmt = $conn->prepare($insert_query);
        // DATA BINDING
        $insert_stmt->bindValue(':nom', htmlspecialchars(strip_tags($data->nom)), PDO::PARAM_STR);
        $insert_stmt->bindValue(':lieu', htmlspecialchars(strip_tags($data->lieu)), PDO::PARAM_STR);
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
    $msg['message'] = 'Please fill all the fields | nom, lieu';
}
//ECHO DATA IN JSON FORMAT
echo json_encode($msg);
