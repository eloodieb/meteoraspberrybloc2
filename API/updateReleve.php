<?php
// SET HEADER
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: access");
header("Access-Control-Allow-Methods: PUT");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// INCLUDING DATABASE AND MAKING OBJECT
require 'database.php';
$db_connection = new Database();
$conn = $db_connection->dbConnection();

// GET DATA FORM REQUEST
$data = json_decode(file_get_contents("php://input"));

//CHECKING, IF ID AVAILABLE ON $data
if (isset($data->id)) {

    $msg['message'] = '';
    $releve_id = $data->id;

    //GET POST BY ID FROM DATABASE
    $get_releve = "SELECT * FROM `sensor_data` WHERE id=:sensor_data";
    $get_stmt = $conn->prepare($get_releve);
    $get_stmt->bindValue(':id', $releve_id, PDO::PARAM_INT);
    $get_stmt->execute();

    //CHECK WHETHER THERE IS ANY POST IN OUR DATABASE
    if ($get_stmt->rowCount() > 0) {

        // FETCH POST FROM DATBASE 
        $row = $get_stmt->fetch(PDO::FETCH_ASSOC);

        // CHECK, IF NEW UPDATE REQUEST DATA IS AVAILABLE THEN SET IT OTHERWISE SET OLD DATA
        $releve_temperature = isset($data->temperature) ? $data->temperature : $row['temperature'];
        $releve_humidity = isset($data->humidity) ? $data->humidity : $row['humidity'];

        $update_query = "UPDATE `sensor_data` SET temperature = :temperature, humidity = :humidity
        WHERE id = :id";

        $update_stmt = $conn->prepare($update_query);

        // DATA BINDING AND REMOVE SPECIAL CHARS AND REMOVE TAGS
        $update_stmt->bindValue(':temperature', htmlspecialchars(strip_tags($releve_temperature)), PDO::PARAM_INT);
        $update_stmt->bindValue(':humidity', htmlspecialchars(strip_tags($releve_humidity)), PDO::PARAM_INT);
        $update_stmt->bindValue(':id', $releve_id, PDO::PARAM_INT);

        if ($update_stmt->execute()) {
            $msg['message'] = 'Data updated successfully';
        } else {
            $msg['message'] = 'data not updated';
        }
    } else {
        $msg['message'] = 'Invlid ID';
    }

    echo json_encode($msg);
} else {
    echo 'pas de id';
}
