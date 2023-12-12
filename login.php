<?php
ob_start();
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
        <div class="link-group">
        <a id="registerLink" href="registratie.php">Geen account? Registreer hier</a>
        </div>
        <input type="submit" name="submitButton" id="submitButton">
    </form>
</div>
</body>
</html>

<?php
    if (isset($_POST['submitButton'])) {
        $email = $_POST['email'];
        $password = $_POST['password'];

        $account = checkLogin($email, $password, $databaseConnection);

        if ($account == NULL) {
            print ("verkeerde gegevens");
        } else {
            $userID = $account[0]['userID'];

            $_SESSION['activeUser'] = getCustomerByAccountID($userID, $databaseConnection);
            header("location: index.php");
        }
    }
    ob_end_flush();
?>

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
        margin-top: 2%;
        margin-left: 30%;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    }

    .link-group {
        margin-bottom: 20px;
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

    #submitButton {
        background-color: #566472;
        color: white;
        padding: 10px;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        width: 100%;
    }

    #submitButton:hover {
        background-color:#424e5d;
    }
</style>
