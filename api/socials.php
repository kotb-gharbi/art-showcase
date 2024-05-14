<?php
require_once("connexion.php");

if (isset($_GET["id"])){
    $id = $_GET["id"];

    $sql = "SELECT Github, Linkedin, Facebook, Instagram FROM socials WHERE ArtistID = :id";

    $statement = $connexion->prepare($sql);
    $statement->bindParam(":id", $id);
    $statement->execute();
    
    if ($statement) {
        $result = $statement->fetch(PDO::FETCH_ASSOC);
        echo json_encode($result);
    } else {
        echo "Failed to execute the SQL statement.";
    }
}
?>
