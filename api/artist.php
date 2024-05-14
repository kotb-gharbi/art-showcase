<?php


require_once("connexion.php");



if (isset($_GET['username'])) {
    $username = $_GET['username'];
}else if(isset($_GET['id'])){
    $id = $_GET['id'];
    $stmt = $connexion->prepare('SELECT username from artist WHERE ArtistID=:id');
    $stmt->bindParam(':id', $id);
    $stmt->execute();
    $username = $stmt->fetchColumn();
}
    $sql = "SELECT 
                a.Username,
                a.Description,
                a.Headline,
                a.pfpURL,
                a.Rating,
                a.public,
                u.email,
                GROUP_CONCAT(DISTINCT i.imageURL) AS images,
                GROUP_CONCAT(DISTINCT c.Name) AS categories
            FROM artist a 
            LEFT JOIN image i ON a.ArtistId = i.artistid
            LEFT JOIN artistcategory ac ON a.artistid = ac.artistid
            LEFT JOIN category c ON ac.categoryid = c.categoryid
            LEFT JOIN user u ON a.Username = u.Username
            WHERE a.Username = :username
            GROUP BY a.ArtistID";
    $stmt = $connexion->prepare($sql);
    $stmt->bindParam(':username', $username);
    $stmt->execute();
    $artist = $stmt->fetch(PDO::FETCH_ASSOC);
    if (empty($artist['images'])) {
        $artist['images'] = null;
    } else {
        $artist['images'] = explode(',', $artist['images']);
    }
    if (empty($artist['categories'])) {
        $artist['categories'] = null;
    } else {
        $artist['categories'] = explode(',', $artist['categories']);
    }
    echo json_encode($artist);
   

?>