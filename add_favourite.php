<?php
session_start();
include "db.php";

$user_id = $_SESSION["user_id"];
$title = $_POST["title"];

// Check if already favourited
$check = "SELECT id FROM favorites WHERE user_id='$user_id' AND media_title='" . mysqli_real_escape_string($conn, $title) . "'";
$result = mysqli_query($conn, $check);

if(mysqli_num_rows($result) > 0){
    echo "already added";
} else {
    $sql = "INSERT INTO favorites(user_id, media_title) VALUES('$user_id','" . mysqli_real_escape_string($conn, $title) . "')";
    mysqli_query($conn, $sql);
    echo "success";
}
?>