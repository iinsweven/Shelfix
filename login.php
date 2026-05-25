<?php

session_start();

include "db.php";

$user = $_POST['user'];
$password = $_POST['password'];

$sql = "SELECT * FROM users
WHERE username='$user'
OR email='$user'";

$result = mysqli_query($conn, $sql);

if (mysqli_num_rows($result) > 0) {

    $row = mysqli_fetch_assoc($result);

    if ($password == $row['password']) {
        $_SESSION['user_id'] = $row['id'];
        $_SESSION['username'] = $row['username'];

        header("Location: home.php");
        exit();
    } else {

        echo "
        <!DOCTYPE html>
        <html>
        <head>
        <title>Wrong Password</title>

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

            backdrop-filter:blur(10px);

            box-shadow:0 0 20px rgba(255,255,255,0.1);
        }

        .error-box h1{
            color:#ff4d4d;

            margin-bottom:10px;
        }

        .error-box p{
            color:#d3d3d3;

            margin-bottom:25px;
        }

        .error-box a{
            text-decoration:none;

            background:white;

            color:black;

            padding:12px 25px;

            border-radius:10px;

            font-weight:bold;
        }

        </style>
        </head>

        <body>

        <div class='error-box'>

        <h1>Wrong Password ❌</h1>

        <p>
        The password you entered is incorrect.
        </p>

        <a href='index.php'>
        Try Again
        </a>

        </div>

        </body>
        </html>
        ";
    }
} else {

    echo "
    <!DOCTYPE html>
    <html>
    <head>
    <title>User Not Found</title>

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

        backdrop-filter:blur(10px);

        box-shadow:0 0 20px rgba(255,255,255,0.1);
    }

    .error-box h1{
        color:#ff4d4d;

        margin-bottom:10px;
    }

    .error-box p{
        color:#d3d3d3;

        margin-bottom:25px;
    }

    .error-box a{
        text-decoration:none;

        background:white;

        color:black;

        padding:12px 25px;

        border-radius:10px;

        font-weight:bold;
    }

    </style>
    </head>

    <body>

    <div class='error-box'>

    <h1>User Not Found ❌</h1>

    <p>
    No account exists with this username or email.
    </p>

    <a href='index.php'>
    Go Back
    </a>

    </div>

    </body>
    </html>
    ";
}
