<!DOCTYPE html>
<html>

<head>
<link rel="stylesheet" href="style.css">
</head>

<body class="create-account-page">

<div class="login-container">

<div class="login-logo">
    <img src="logoshelfix.png" class="logo">
    <h1>SHELFIX</h1>
</div>

<p class="tagline">
Create Your Account
</p>

<form action="register_process.php" method="POST">

<input type="text"
name="username"
placeholder="Username"
class="input-box" required>

<input type="email"
name="email"
placeholder="Email"
class="input-box" required>

<input type="password"
name="password"
placeholder="Password"
class="input-box" required>

<button class="login-btn">
Create Account
</button>

</form>

</div>

</body>
</html>