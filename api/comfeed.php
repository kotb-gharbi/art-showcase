<?php
use Firebase\JWT\JWT;
use Firebase\JWT\Key;


require_once("connexion.php");
require_once("vendor/autoload.php"); 


if(isset($_GET['id']) && isset($_GET['username']) && isset($_GET['type'])) {
    $id = $_GET['id'];
    $username = $_GET['username'];
    $type = $_GET['type'];


    if ($type === 'A') {
            $sql = "SELECT
                client.username AS username,
                comrequest.description,
                comrequest.price,
                comrequest.status,
                comrequest.reqid
            FROM comrequest
            JOIN comissiona ON comissiona.comID = comrequest.comID
            JOIN artist ON artist.artistID = comissiona.artistID
            JOIN client ON client.clientID = comrequest.clientID
            WHERE artist.artistID = :artistID AND comrequest.status != 'Cancelled'";
            $stmt = $connexion->prepare($sql);
            $stmt->bindParam(':artistID', $id);
            $stmt->execute();
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } elseif ($type === 'C') {
            $sql = "SELECT
                comrequest.reqid,
                comrequest.description,
                comrequest.price,
                comrequest.status,
                artist.username AS username
            FROM comrequest
            JOIN comissiona ON comissiona.comID = comrequest.comID
            JOIN artist ON artist.artistID = comissiona.artistID
            WHERE comrequest.clientID = :clientID AND comrequest.status != 'Cancelled'";
            $stmt = $connexion->prepare($sql);
            $stmt->bindParam(':clientID', $id);
            $stmt->execute();
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    echo json_encode($result);

} else {
    echo json_encode(array("result" => "Missing required parameters"));
}



?>