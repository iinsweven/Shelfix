<?php

session_start();
include "db.php";

$user_id = $_SESSION['user_id'];
$title = $_POST['title'];
$review = $_POST['review'];

$sql = "INSERT INTO reviews(user_id, media_title, review)
VALUES('$user_id','$title','$review')";

mysqli_query($conn,$sql);

echo "success";

?>