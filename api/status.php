<?php

require_once("connexion.php");

$json_data = json_decode(file_get_contents('php://input'), true); 

if(isset($json_data['msg']) && isset($json_data['reqid'])) {

    $msg = $json_data['msg'];
    $reqid = $json_data['reqid'];

    try {
        $sql = "UPDATE comrequest SET status = :msg WHERE reqid = :reqid";
        $statement = $connexion->prepare($sql);
        $statement->bindParam(':msg', $msg);
        $statement->bindParam(':reqid', $reqid);
        $statement->execute();


        echo json_encode(array("result" => true));
    } catch (PDOException $e) {

        echo json_encode(array("result" => "failed to update"));
    }
} else {

    echo json_encode(array("result" => "missing data"));
}
?>
