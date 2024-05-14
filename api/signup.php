<?php

require_once("connexion.php");

$json_data = json_decode(file_get_contents('php://input'), true); 
    $username = $json_data['Username'];
    $email = $json_data['Email'];
    $password = $json_data['Password'];
    $type = $json_data['type'];

    if ($username !== null && $email !== null && $password !== null && $type !== null) {
        $stmt_check1 = $connexion->prepare("SELECT COUNT(*) AS count FROM user WHERE username = :username");
        $stmt_check2 = $connexion->prepare("SELECT COUNT(*) AS count FROM user WHERE email = :email");

        $stmt_check1->bindParam(':username', $username);
        $stmt_check2->bindParam(':email', $email);

        $stmt_check1->execute();
        $stmt_check2->execute();

        $result_check1 = $stmt_check1->fetch(PDO::FETCH_ASSOC);
        $result_check2 = $stmt_check2->fetch(PDO::FETCH_ASSOC);

        if ($result_check1['count'] > 0) {
            $msg = ["result" => "Username already used"];
            echo json_encode($msg);
            exit;
        }else if($result_check2['count']>0){
            $msg = ["result" => "Email already used"];
            echo json_encode($msg);
            exit;
        }else {
            insertUser($username, $email, $password, $type);
            $msg = ["result" => "User added successfully"];
            echo json_encode($msg);
                    
        }
    }


function insertUser($username, $email, $password, $type)
{
    global $connexion;
    $stmt = $connexion->prepare("INSERT INTO user (username, email, password, type) VALUES (:username, :email, :password, :type)");
    $stmt->bindParam(':username', $username);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':password', $password);
    $stmt->bindParam(':type', $type);
    $resultat = $stmt->execute();
    if ($resultat) {
        $pfp = 'https://cdn.discordapp.com/attachments/677566433445543956/1235690260793655396/default_pfp.jpg?ex=663549bc&is=6633f83c&hm=5145584262feb6e647561688f6fd13787083ad567ba97a0b0bf27f3e817b1ef4&';
        if ($type == 'A') {
            $stmt_artist = $connexion->prepare("INSERT INTO artist (username, Description,  Headline, pfpURL, Rating, public) VALUES (:username, NULL, NULL, :pfp, NULL,'Y')");
            $stmt_artist->bindParam(':username', $username);
            $stmt_artist->bindParam(':pfp', $pfp);
            $stmt_artist->execute();
        } elseif ($type == 'C') {
            $stmt_client = $connexion->prepare("INSERT INTO client (username,pfpURL) VALUES (:username,:pfp)");
            $stmt_client->bindParam(':username', $username);
            $stmt_client->bindParam(':pfp', $pfp);
            $stmt_client->execute();
        }
    }

}
?>