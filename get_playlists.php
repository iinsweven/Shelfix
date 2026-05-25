<?php
session_start();
include "db.php";

if(!isset($_SESSION['user_id'])){
    echo json_encode([]);
    exit();
}

$user_id = $_SESSION['user_id'];

$sql = "SELECT DISTINCT playlist_name FROM playlists WHERE user_id='$user_id' ORDER BY playlist_name";
$result = mysqli_query($conn, $sql);

$playlists = [];
while($row = mysqli_fetch_assoc($result)){
    $playlists[] = $row['playlist_name'];
}

header('Content-Type: application/json');
echo json_encode($playlists);
?>
