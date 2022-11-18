<?php
session_start();

if ($_GET["logout"]) {
    session_destroy();
    header("Location: login.php");
    exit;
}

if (!isset($_SESSION["username"])) {
    header("Location: login.php");
    exit;
}

?>

<!DOCTYPE HTML>
<html lang="de">
<head>
    <title>Kontakte</title>
    <meta charset="UTF-8">
    <link rel="icon" href="../icon/appicon.svg">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@100&family=Rubik+Bubbles&family=Source+Sans+Pro:wght@300&display=swap"
          rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="../style/indexStyle.css">
</head>
<body>

<div class="menubar">
    <h1>Kontakte</h1>
    <div class="name">
        <?php
        $username = $_SESSION["username"];
        $userLetter = substr($username, 0, 1);

        echo "<div class='avatar'>$userLetter</div>";
        echo "
        <div>
            <div>$username</div>
            <div><a href='?logout=true'>Abmelden</a></div>
        </div>
        ";
        ?>
    </div>
</div>

<div class="main">

    <div class="menu">
        <a href="index.php?page=start"><img src="../icon/start.svg" alt="start">Start</a>
        <a href="index.php?page=contacts"><img src="../icon/profile.svg" alt="profile">Kontakte</a>
        <a href="index.php?page=addcontact"><img src="../icon/addprofile.svg" alt="addprofile">Kontakt hinzufügen</a>
        <a href="index.php?page=impressum"><img src="../icon/impressum.svg" alt="impressum">Impressum</a>
    </div>

    <div class="content">
        <?php
        $page = $_GET["page"];

        try {
            $mysql = new PDO("mysql:host=127.0.0.1;dbname=contactbook", "root", "toor");
        } catch (PDOException $e) {
            echo "SQL-Error: " . $e->getMessage();
        }

        $tableName = "user_" . $_SESSION["username"];

        // create table if not exists
        $stmt = $mysql->prepare("CREATE TABLE IF NOT EXISTS `:tableName` (CONTACT varchar(255) UNIQUE, PHONE varchar(255))");
        $stmt->bindParam(":tableName", $tableName);
        $stmt->execute();

        if (isset($_GET["remove"])) {
            $remove = $_GET["remove"];

            $stmt = $mysql->prepare("DELETE FROM `:tableName` WHERE CONTACT = :contact");
            $stmt->bindParam(":tableName", $tableName);
            $stmt->bindParam(":contact", $remove);
            $stmt->execute();

            header("Location: index.php?page=contacts");
        }

        if (isset($_POST["name"]) && isset($_POST["phone"])) {
            if ($_POST["name"] == "" || $_POST["phone"] == "") {
                echo "Bitte gib einen Namen und eine Telefonnummer an!";
                exit;
            }

            // check if contact already exists
            $stmt = $mysql->prepare("SELECT * FROM `:tableName` WHERE CONTACT = :contact");
            $stmt->bindParam(":tableName", $tableName);
            $stmt->bindParam(":contact", $_POST["name"]);
            $stmt->execute();

            if ($stmt->rowCount() != 0) {
                echo "Es existiert bereits ein Kontakt mit diesem Namen.";
                exit;
            }

            // add contact
            $stmt = $mysql->prepare("INSERT INTO `:tableName` (CONTACT, PHONE) VALUES (:contact, :phone)");
            $stmt->bindParam(":tableName", $tableName);
            $stmt->bindParam(":contact", $_POST["name"]);
            $stmt->bindParam(":phone", $_POST["phone"]);
            $stmt->execute();

            echo "Dein Kontakt <b>" . $_POST["name"] . "</b> wurde erfolgreich hinzugefügt!";
            header("Location: index.php?page=contacts");
        }

        switch ($page) {
            case "contacts":
                // open personal profile
                echo "
                    <h1>Deine Kontakte</h1>
                ";

                $tableName = "user_" . $_SESSION["username"];

                $stmt = $mysql->prepare("SELECT * FROM `:tableName`");
                $stmt->bindParam(":tableName", $tableName);
                $stmt->execute();

                $contacts = $stmt->fetchAll();

                foreach ($contacts as $contactEntry) {
                    $name = $contactEntry["CONTACT"];
                    $phone = $contactEntry["PHONE"];

                    echo "
                        <div class='contact'>
                            <div style='display: flex; align-items: center;'>
                                <img class='profilePicture' src='../icon/profilePicture.png' alt='profilePcture'>
                                <div class='contactProperties'>
                                    <b>$name</b><br>
                                    $phone
                                </div>
                            </div>
                            
                            <div style='align-items: center;'>
                                <div><a href='tel: $phone'><button class='callButton'>Anrufen</button></a></div>
                                <div><a href='?page=contacts&remove=$name'><button class='removeButton'>Entfernen</button></a></div>
                            </div>
                        </div>
                    ";
                }
                break;

            case "addcontact":
                echo "
                    <h1>Kontakt hinzufügen</h1>
                    <h3>Bitte gib die Kontaktdaten der Person ein, die du hinzufügen möchtest:</h3>
                    
                    <form method='post'>
                        <input class='field' placeholder='Name' name='name'>
                        <input class='field' placeholder='Handynummer' name='phone'>
                            
                        <button class='submitbutton' type='submit'>Kontakt hinzufügen</button>
                    </form>
                ";
                break;

            case "impressum":
                // open start page by default
                echo "
                    <h1>Impressum</h1>
                    <h3>Es ist noch kein Impressum vorhanden :(</h3>
                ";
                break;

            default:
                // open start page by default
                echo "
                    <p style='text-decoration: underline; font-size: 50px; text-align: center;'>Herzlich Willkommen!</p>
                    <h2>Mithilfe dieser Seite kannst du deine Kontakte speichern!</h2>
                ";
                break;
        }
        ?>
    </div>

</div>
</body>
</html>