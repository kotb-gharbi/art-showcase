<?php


require_once("connexion.php");


$sql = "SELECT 
            a.Username,
            a.Description,
            a.Headline,
            a.pfpURL,
            GROUP_CONCAT(DISTINCT JSON_OBJECT('url', i.imageURL, 'title', i.Title)) AS images,
            GROUP_CONCAT(DISTINCT c.Name) AS categories
        FROM artist a 
        LEFT JOIN image i ON a.ArtistId = i.artistid
        LEFT JOIN artistcategory ac ON a.artistid = ac.artistid
        LEFT JOIN category c ON ac.categoryid = c.categoryid
        WHERE a.public = 'Y'
        GROUP BY a.ArtistId";
$stmt = $connexion->prepare($sql);

$stmt->execute();

$artists = $stmt->fetchAll(PDO::FETCH_ASSOC);

if ($artists) {
    foreach ($artists as &$artist) {
        $artist['images'] = json_decode('[' . $artist['images'] . ']', true);
        $artist['categories'] = explode(',', $artist['categories']);
    }
    echo json_encode($artists);
} else {
    echo json_encode(array("message" => "No artists found"));
}
?>