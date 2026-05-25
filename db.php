<?php

$conn = mysqli_connect(
    "localhost",
    "root",
    "",
    "shelfixdb"
);

if(!$conn){
    die("Database Connection Failed");
}

?>