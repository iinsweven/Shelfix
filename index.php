<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shelfix</title>

    <link rel="stylesheet" href="style.css">
</head>

<body class="login-page">
    <div class="header">
        <div class="logo-section">
            <img src="logoshelfix.png" alt="Shelfix Logo" class="logoh">
            <h1>Shelfix</h1>
        </div>
        <div class="searchbutton">
            <input type="text" placeholder="seach.." class="search-input">
            <button class="search-btn">Search</button>
            <div id="search-results"></div>
        </div>

    </div>

    <div class="login-container">
        <div class="login-logo">
            <img src="logoshelfix.png" alt="Shelfix Logo" class="logo">

            <h1>SHELFIX</h1>
        </div>

        <p class="tagline">Your Personal Media Journal</p>

        <form action="login.php" method="POST">

            <input type="text"
                placeholder="Enter Username or Email"
                class="input-box"
                name="user" required>

            <input type="password"
                placeholder="Enter Password"
                class="input-box"
                name="password" required>

            <button class="login-btn" type="submit">
                Login
            </button>

        </form>
        <a href="register.php" class="create-account">
            Create Account
        </a>
    </div>
    
<script src="script.js"></script>
</body>

</html>