<!DOCTYPE HTML>
<html lang="de">
<head>
    <title>Login</title>
    <meta charset="UTF-8">
    <link rel="stylesheet" type="text/css" href="../style/loginStyle.css">
</head>
<body>
<div class="login">
    <h1 style="text-align: center;">Login</h1>

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

        <button class="submitbutton" type="submit" name="submit">Login</button>
    </form>

    <p>Du hast noch keinen Account? <a href="register.php">Registrieren</a></p>
</div>

<?php
if (isset($_POST["submit"])) {
    try {
        $mysql = new PDO("mysql:host=127.0.0.1;dbname=contactbook", "root", "toor");
    } catch (PDOException $e) {
        echo "SQL-Error: " . $e->getMessage();
    }

    $stmt = $mysql->prepare("SELECT * FROM users WHERE USERNAME = :user");
    $stmt->bindParam(":user", $_POST["username"]);
    $stmt->execute();

    if ($stmt->rowCount() == 0) {
        echo "<p style='color: red; text-align: center;'>
            Bei deinem Login ist ein Fehler aufgetreten. Überprüfe Nutzernamen und Passwort.
        </p>";
        exit;
    }

    $row = $stmt->fetch();

    if (!password_verify($_POST["password"], $row["PASSWORD"])) {
        echo "<p style='color: red; text-align: center;'>
            Dein Passwort ist falsch. Bitte gib das korrekte Passwort ein.
        </p>";
        exit;
    }

    session_start();
    $_SESSION["username"] = $row["USERNAME"];
    header("Location: index.php");
}
?>

</body>
</html>