<?php

include "db.php";

$username = $_POST['username'];
$email = $_POST['email'];
$password = $_POST['password'];

$check = "SELECT * FROM users
WHERE username='$username'
OR email='$email'";

$result = mysqli_query($conn,$check);

if(mysqli_num_rows($result) > 0){

    echo "
    <!DOCTYPE html>
    <html>
    <head>
    <title>Account Exists</title>

    <style>

    body{
        margin:0;
        padding:0;

        height:100vh;

        display:flex;
        justify-content:center;
        align-items:center;

        font-family:Arial,sans-serif;

        background:
        linear-gradient(
        rgba(0,0,0,0.7),
        rgba(0,0,0,0.7)),
        url('bgm1.jpg');

        background-size:cover;
        background-position:center;
    }

    .error-box{
        background:rgba(0,0,0,0.75);

        padding:40px;

        border-radius:20px;

        text-align:center;

        color:white;

        width:350px;
    }

    .error-box h1{
        color:#ff4d4d;
    }

    .error-box a{
        text-decoration:none;

        background:white;

        color:black;

        padding:12px 25px;

        border-radius:10px;
    }

    </style>
    </head>

    <body>

    <div class='error-box'>

    <h1>Account Already Exists ❌</h1>

    <p>
    Username or Email is already registered.
    </p>

    <a href='register.php'>
    Try Again
    </a>

    </div>

    </body>
    </html>
    ";

}
else{

    $sql = "INSERT INTO users(username,email,password)
    VALUES('$username','$email','$password')";

    if(mysqli_query($conn,$sql)){

        echo "
        <!DOCTYPE html>
        <html>

        <head>
        <title>Success</title>

        <style>

        body{
            margin:0;
            padding:0;

            height:100vh;

            display:flex;
            justify-content:center;
            align-items:center;

            background:
            linear-gradient(
            rgba(0,0,0,0.7),
            rgba(0,0,0,0.7)),
            url('bgm1.jpg');

            background-size:cover;
            background-position:center;

            font-family:Arial;
        }

        .success-box{
            background:rgba(0,0,0,0.75);

            padding:40px;

            border-radius:20px;

            text-align:center;

            color:white;

            width:350px;
        }

        .success-box h1{
            color:#4dff88;
        }

        .success-box a{
            text-decoration:none;

            background:white;

            color:black;

            padding:12px 25px;

            border-radius:10px;
        }

        </style>
        </head>

        <body>

        <div class='success-box'>

        <h1>Account Created ✅</h1>

        <p>
        Welcome to Shelfix.
        </p>

        <a href='home.php'>
        Enter Shelfix
        </a>

        </div>

        </body>
        </html>
        ";

    }
    else{
        echo "Database Error";
    }

}

?>