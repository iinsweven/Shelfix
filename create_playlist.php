<?php
// create_playlist.php
session_start();
include "db.php";

if(!isset($_SESSION['user_id'])) { exit("login required"); }

$user_id = $_SESSION['user_id'];
$playlist = $_POST['playlist'];

// Check if already exists for this user
$check = "SELECT * FROM playlists WHERE user_id='$user_id' AND playlist_name='$playlist' LIMIT 1";
$result = mysqli_query($conn, $check);

if(mysqli_num_rows($result) > 0){
    echo "already exists";
} else {
    // Insert a placeholder row so the playlist is persisted
    $insert = "INSERT INTO playlists(user_id, media_title, playlist_name)
               VALUES('$user_id', '__empty__', '$playlist')";
    mysqli_query($conn, $insert) ? print("created") : print(mysqli_error($conn));
}
?>