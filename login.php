<?php
// nacteni souboru s funkcemi loginu (pracuje se session)
require_once("MyLogin.php");
$login = new MyLogin;
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
    <link href="css/css2.css" rel="stylesheet">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login</title>
    <script src="js/login.js"></script>
</head>
<body>
<?php include "./header.php" ?>

<?php
if(!$login->isUserLogged()) {
    ?>
        Prihlaseni
    <form method="POST">
        <fieldset>
            <legend>Přihlášení uživatele</legend>
            <label> mail
                <input type="text" name="jmeno" placeholder="-- zadejte jméno --">
            </label>

            <label> pass
                <input type="text" name="heslo" placeholder="-- zadejte heslo --">
            </label>
            <button type="submit" name="action" value="login">
                Přihlásit uživatele
            </button>
        </fieldset>
    </form>
    Registrace
    <form method="POST">
        <fieldset>
            <legend>Registrace noveho uživatele</legend>
            <label> mail
                <input type="text" name="mail" placeholder="-- zadejte mail --">
            </label>

            <label> pass
                <input type="text" name="heslo" placeholder="-- zadejte heslo --" id="pass1">
            </label>
            <label> pass2
                <input type="text" name="heslo2" placeholder="-- zadejte heslo jeste jednou --" id="pass2">
            </label>
            <label> name
                <input type="text" name="jmeno" placeholder="-- zadejte jméno --">
            </label>

            <label> surn
                <input type="text" name="prijmeni" placeholder="-- zadejte heslo --">
            </label>
            <button type="submit" name="action" value="register" onclick="return checkFields()">
                Přihlásit uživatele
            </button>
        </fieldset>
    </form>
    <?php
} else {
    ?>
    @<?=$login->getUserName()?>
    <form method="POST">
        <fieldset>
            <legend>Odhlášení uživatele</legend>
            <button type="submit" name="action" value="logout">
                Odhlásit uživatele
            </button>
        </fieldset>
    </form>
    <?php
}
?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>
</body>
</html>