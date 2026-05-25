<?php

session_start();
include "db.php";

$user_id = $_SESSION['user_id'];
$title = $_POST['title'];
$rating = $_POST['rating'];

$sql = "INSERT INTO ratings(user_id, media_title, rating)
VALUES('$user_id','$title','$rating')";

mysqli_query($conn,$sql);

echo "success";

?>