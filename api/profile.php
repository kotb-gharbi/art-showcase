<?php
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

require_once("connexion.php");
require_once("vendor/autoload.php"); 


$headers = apache_request_headers();
if (isset($headers)) {
    $jwtbear = $headers['Authorization'];
    $jwtbear = explode(' ', $jwtbear);
    $jwt= $jwtbear[1];
}

$decoded = JWT::decode($jwt, new Key("bc34968d319ad9363f9642f6c567f9b119c979e2431e544421101aa6c9fe95a1",'HS256'));
    $usernm = $decoded ->data->username;
    $stmt = $connexion->prepare('SELECT ArtistID from artist WHERE username=:username');
    $stmt->bindParam(':username', $usernm);
    $stmt->execute();
    $artistID=$stmt->fetchColumn();

// Getting input info from profile
$json_data = json_decode(file_get_contents('php://input'), true); 
$username = $json_data['Username'] ?? null ;
$headline = $json_data['Headline'] ?? null;
$description = $json_data['Description'] ?? null;
$pfpURL = $json_data['pfpURL'] ?? null;
$categories = $json_data["categories"] ?? null;
$public = $json_data["public"] ?? null;

// updating everything but categs
try {
    $stmt = $connexion->prepare("UPDATE Artist SET username = :Username, Description = :description, Headline = :headline, pfpURL = :pfpURL, public = :public WHERE ArtistId = :userID");
    $stmt->bindParam(':Username', $username);
    $stmt->bindParam(':userID', $userID);
    $stmt->bindParam(':description', $description);
    $stmt->bindParam(':headline', $headline);
    $stmt->bindParam(':pfpURL', $pfpURL);
    $stmt->bindParam(':public', $public);
    $resultat = $stmt->execute();
    echo json_encode(array('success' => true, 'message' => 'Profile updated successfully.'));
} catch (PDOException $e) {
    echo json_encode(array('success' => false, 'message' => 'PDO Exception: ' . $e->getMessage()));
}

// categ updating
if ($categories) {
    insertCategoriesForUser($userID, $categories);
}

function getCategoryID($categoryName) {
    global $connexion;
    $stmt = $connexion->prepare("SELECT CategoryID FROM Category WHERE Name = :categoryname");
    $stmt->bindParam(':categoryname', $categoryName);
    $stmt->execute();
    $categoryID = $stmt->fetchColumn();
    return $categoryID;
}

function insertCategoriesForUser($userID, $categories) {
    global $connexion;
    $stmt = $connexion->prepare("INSERT INTO usercategory (userID, categoryID) VALUES (:userID, :categoryID)");
    $stmt->bindParam(':userID', $userID);
    foreach ($categories as $categoryName) {
        $categoryID = getCategoryID($categoryName);
        $stmt->bindParam(':categoryID', $categoryID);
        $stmt->execute();
    }
}
?>
