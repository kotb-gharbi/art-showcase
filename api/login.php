<?php
use Firebase\JWT\JWT;

require_once("connexion.php");
require_once("vendor/autoload.php"); 

//json data
$json_data = json_decode(file_get_contents('php://input'), true); 

if (!empty($json_data['Username']) && !empty($json_data['Password'])) {
    $username = $json_data['Username'];
    $password = $json_data['Password'];
    // retrieve user data
    $stmt = $connexion->prepare("SELECT * FROM user WHERE Username = :username");
    $stmt->bindParam(':username', $username);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    // compare passwords
    if ($user) {
        $stored_pw = $user['Password'];
        if ($password === $stored_pw) {
            auth($username, $password);
            
        } else {
            echo json_encode(array("result" => "Password incorrect"));
        }
    } else {
        echo json_encode(array("result" => "Username is incorrect"));
    }
} else {
    echo json_encode(array("result" => "Missing username or password"));
}
function auth($username, $password)
{                  
    // jwt token generation
    global $connexion;              
    $secret_key = "bc34968d319ad9363f9642f6c567f9b119c979e2431e544421101aa6c9fe95a1"; 
    $issuer_claim = "localhost"; 
    $audience_claim = "localhost"; 
    $issuedat_claim = time(); 
    $notbefore_claim = $issuedat_claim + 10; 
    $expire_claim = $issuedat_claim + 36000; 

    $stmt = $connexion->prepare("SELECT type FROM user WHERE Username = :username");
    $stmt->bindParam(':username', $username);
    $stmt->execute();
    $type = $stmt->fetch(PDO::FETCH_ASSOC);

    $token = array(
        "iss" => $issuer_claim,
        "aud" => $audience_claim,
        "iat" => $issuedat_claim,
        "nbf" => $notbefore_claim,
        "exp" => $expire_claim,
        "data" => array(
            "username" => $username,
            "id" => NULL,
            "type" => $type['type']
        )
    );
    if ($type['type'] === "A") {
        $artist_id_stmt = $connexion->prepare("SELECT ArtistId FROM artist WHERE Username = :username");
        $artist_id_stmt->bindParam(':username', $username);
        $artist_id_stmt->execute();
        $artist_id = $artist_id_stmt->fetch(PDO::FETCH_ASSOC);
        $token['data']['id'] = $artist_id['ArtistId'];
    } elseif ($type['type'] === "C") {
        $client_id_stmt = $connexion->prepare("SELECT ClientId FROM client WHERE Username = :username");
        $client_id_stmt->bindParam(':username', $username);
        $client_id_stmt->execute();
        $client_id = $client_id_stmt->fetch(PDO::FETCH_ASSOC);
        $token['data']['id'] = $client_id['ClientId'];
    }

    $jwt = JWT::encode($token, $secret_key ,'HS256');
    echo json_encode(array("result" => true, "jwt" => $jwt));
} 

            
?>