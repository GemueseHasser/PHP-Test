<!DOCTYPE HTML>
<html lang="de">
<head>
    <title>Registrieren</title>
    <meta charset="UTF-8">
    <link rel="icon" href="../icon/appicon.svg">
    <link rel="stylesheet" type="text/css" href="../style/registerStyle.css">
</head>
<body>
<div class="register">
    <h1 style="text-align: center;">Registrieren</h1>

    <form method="post">
        <div>
            <label>
                <input name="username" placeholder="Benutzername">
            </label>
        </div>
        <div>
            <label>
                <input type="password" name="password" placeholder="Passwort">
            </label>
        </div>
        <div>
            <label>
                <input type="password" name="passwordConfirm" placeholder="Passwort wiederholen">
            </label>
        </div>

        <button class="submitbutton" type="submit" name="submit">Registrieren</button>
    </form>

    <p>Du hast schon einen Account? <a href="login.php">Anmelden</a></p>
</div>

<?php
require 'mysql.php';

if (isset($_POST["submit"])) {

    if (!isset($mysql)) {
        echo "Es ist ein Fehler bei der Verbindung zur Datenbank aufgetreten.";
        exit;
    }

    $stmt = $mysql->prepare("SELECT * FROM users WHERE USERNAME = :user");
    $stmt->bindParam(":user", $_POST["username"]);
    $stmt->execute();

    if ($stmt->rowCount() != 0) {
        echo "<p style='color: red; text-align: center;'>
            Dieser Nutzername ist bereits vergeben. Bitte wähle einen anderen.
        </p>";
        exit;
    }

    if ($_POST["password"] != $_POST["passwordConfirm"]) {
        echo "<p style='color: red; text-align: center;'>
            Bitte überprüfe deine Passwörter. Die Passwörter stimmen nicht überein.
        </p>";
        exit;
    }

    $stmt = $mysql->prepare("INSERT INTO users (USERNAME, PASSWORD) VALUES (:user, :password)");
    $stmt->bindParam(":user", $_POST["username"]);
    $hash = password_hash($_POST["password"], PASSWORD_BCRYPT);
    $stmt->bindParam(":password", $hash);
    $stmt->execute();

    session_start();
    $_SESSION["username"] = $_POST["username"];
    header("Location: index.php");
}
?>

</body>
</html>