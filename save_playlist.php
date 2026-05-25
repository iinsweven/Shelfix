<?php

session_start();

include "db.php";

if(!isset($_SESSION['user_id'])){
    exit("login required");
}

$user_id = $_SESSION['user_id'];

$title = $_POST['title'];
$playlist = $_POST['playlist'];

$check = "SELECT * FROM playlists
WHERE user_id='$user_id'
AND media_title='$title'
AND playlist_name='$playlist'";

$result = mysqli_query($conn,$check);

if(mysqli_num_rows($result) > 0){

    echo "already exists";

}
else{

    $insert = "INSERT INTO playlists(user_id, media_title, playlist_name)
    VALUES('$user_id','$title','$playlist')";

    if(mysqli_query($conn,$insert)){

        echo "saved";

    }
    else{

        echo mysqli_error($conn);

    }

}

?>