<?php
$servername = 'localhost';
$username = 'root';
$password = '@Yucel-38';

try {
    $db = new PDO("mysql:host=$servername;dbname=projetsqp", $username, $password);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    $db->beginTransaction();

    // ajouter un utilisateur 
    if (isset($_POST["add"])) {
        $id = $_POST["add"];
        $nom = $_POST["Nom"];
        $prenom = $_POST["Prénom"];
        $mail = $_POST["Mail"];
        $cp = $_POST["CodePostal"];
        $requeteSQLAdd = $db->prepare("INSERT INTO user(Nom, Prénom, Mail, CodePostal) VALUE ('$nom', '$prenom', '$mail', '$cp')");
        
        $regex = regex($nom,$prenom,$mail,$cp);

                if ($regex === true) {
                    $requeteSQLAdd->execute();   
                }else{
                    echo $regex;
                }
    }


    // modifier un utilisateur 
    if (isset($_POST["update"])) {
        $id = $_POST["update"];
        $nom = $_POST["Nom"];
        $prenom = $_POST["Prénom"];
        $mail = $_POST["Mail"];
        $cp = $_POST["CodePostal"];
        $requeteSQLUpdate = $db->prepare("UPDATE user SET Nom='$nom', Prénom='$prenom', Mail='$mail', CodePostal='$cp' WHERE ID=$id");
        $requeteSQLUpdate->execute();
    }
    // supprimer un utilisateur 
    if (isset($_POST["Supprimer"])) {
        $id = $_POST["Supprimer"];
        $requeteSQLDelete = $db->prepare("DELETE FROM user WHERE ID=:id ");
        $requeteSQLDelete->execute(["id"=> $id]);
        header("refresh:0");
    }
    $requeteSQL = $db->prepare("SELECT * FROM user");
    $requeteSQL->execute();

    $tableauRequete = $requeteSQL->fetchAll();
    $db->commit();
    // var_dump($tableauRequete);
    $db = null;
} catch (PDOException $e) {
    echo "Erreur : " . $e->getMessage();

    $db->rollback();
}

function regex($nom,$prenom,$mail,$cp)  {
    $regexNom ="/^[A-zÀ-ÿ\-]*$/";
    $regexPrenom ="/^[A-zÀ-ÿ\-]*$/";
    $regexMail ="/^[A-zÀ-ÿ0-9]*@[a-z]*\.[a-z]{2,5}$/";
    $regexCp ="/^[0-9]{5}$/";
    if(!preg_match($regexNom,$nom)) {
        return "Veuillez rentrer un Nom valide";
    }
    if(!preg_match($regexPrenom,$prenom)) {
        return "Veuillez rentrer un Prénom valide";
    }
    if(!preg_match($regexMail,$mail)) {
        return "Veuillez rentrer un Mail valide";
    }
    if(!preg_match($regexCp,$cp)) {
        return "Veuillez rentrer un CodePostal valide";
    }
    return true;
}


?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./asset/css/style.css">
    <title>CRUD</title>
</head>

<body>
    <header>
        <h1>CRUD</h1>
    </header>
    <table>
        <tr>
            <th>Nom</th>
            <th>Prénom</th>
            <th>Mail</th>
            <th>CodePostal</th>
        </tr>

        <?php
        foreach ($tableauRequete as $line) {
            echo "<tr> <form method='POST'>";
            foreach ($line as $key => $values) {
                if ($key != "ID") {
                    if (isset($_POST["Modifier"])) {
                        if ($_POST["Modifier"] == $line["ID"]) {
                            echo "<td><input name='$key' value='$values' type='text'></td>";
                        } else {
                            echo "<td>$values</td>";
                        }
                    } else {
                        echo "<td>$values</td>";
                    }
                }

            }
            if (isset($_POST["Modifier"])) {
                if ($_POST["Modifier"] == $line["ID"]) {
                    echo "<td class='border'><button class='modifier' type='submit' name='update' value='$line[ID]'><img src='./asset/images/jaccepte.png'alt='modifier'></button></td>";
                } else {
                    echo "<td class='border'><button class='modifier' type='submit' name='Modifier' value='$line[ID]'><img src='./asset/images/crayon.png'alt='modifier'></button></td>";
                }
            } else {
                echo "<td class='border'><button class='modifier type='submit' name='Modifier' value='$line[ID]'><img src='./asset/images/crayon.png'alt='modifier'></button></td>";

            }
            echo "<td class='border'>
                <button class='modifier' type='submit' name='Supprimer' value='$line[ID]'><img src='./asset/images/effacer.png'alt='supprimer'></button>
            </td>";
            echo "</form> </tr>";
        } ?>

    </table>
    <h2>Ajouter un utilisateur </h2>
    <form method="POST">
        <div class="form">
            <div class="">
                <label for="Nom">Nom : </label>
                <input type="text" name="Nom">
            </div>
            <div>
                <label for="Prenom">Prénom : </label>
                <input type="text" name="Prénom">
            </div>
            <div>
                <label for="Mail">E-mail : </label>
                <input type="text" name="Mail">
            </div>
            <div>
                <label for="Cp">Code-Postal : </label>
                <input type="text" name="CodePostal">
            </div>
            <button class="add" type="submit" name="add" value="add">Ajouter</button>
        </div>
    </form>
</body>

</html>
