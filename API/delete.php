<?php
// SET HEADER
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: access");
header("Access-Control-Allow-Methods: DELETE");
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
    // YOU CAN REMOVE THIS QUERY AND PERFORM ONLY DELETE QUERY
    $check_releve = "SELECT * FROM `sensor_data` WHERE id=:releve_id";
    $check_releve_stmt = $conn->prepare($check_releve);
    $check_releve_stmt->bindValue(':releve_id', $releve_id, PDO::PARAM_INT);
    $check_releve_stmt->execute();

    //CHECK WHETHER THERE IS ANY POST IN OUR DATABASE
    if ($check_releve_stmt->rowCount() > 0) {

        //DELETE POST BY ID FROM DATABASE
        $dcheck_releve = "DELETE FROM `sensor_data` WHERE id=:releve_id";
        $dcheck_releve_stmt = $conn->prepare($dcheck_releve);
        $dcheck_releve_stmt->bindValue(':releve_id', $releve_id, PDO::PARAM_INT);

        if ($dcheck_releve_stmt->execute()) {
            $msg['message'] = 'Post Deleted Successfully';
        } else {
            $msg['message'] = 'Post Not Deleted';
        }
    } else {
        $msg['message'] = 'Invlid ID';
    }
    // ECHO MESSAGE IN JSON FORMAT
    echo json_encode($msg);
}
