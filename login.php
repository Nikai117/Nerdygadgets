<?php
include __DIR__ . "/header.php";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>Login Form</title>
</head>
<body>
<div class="login-container">
    <h2>Login</h2>
    <form action="login.php" method="post">
        <div class="form-group">
            <label for="username">Email:</label>
            <input type="text" id="email" name="email" required>
        </div>
        <div class="form-group">
            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required>
        </div>
        <button type="submit">Login</button>
    </form>
</div>
</body>
</html>

<style>
    body {
        font-family: Arial, sans-serif;
        display: flex;
        align-items: center;
        justify-content: center;
        height: 100vh;
        margin: 0;
    }

    .login-container {
        width: 30%;
        padding: 20px;
        border: 1px solid #ccc;
        border-radius: 5px;
        margin-left: 30%;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    }

    .form-group {
        margin-bottom: 15px;
    }

    label {
        display: block;
        margin-bottom: 5px;
    }

    input {
        width: 100%;
        padding: 8px;
        box-sizing: border-box;
    }

    button {
        background-color: #4caf50;
        color: white;
        padding: 10px;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        width: 100%;
    }

    button:hover {
        background-color: #45a049;
    }
</style>
