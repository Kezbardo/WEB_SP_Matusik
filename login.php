<?php
// nacteni souboru s funkcemi loginu (pracuje se session)
require_once("MyLogin.php");
$login = new MyLogin;

// zpracovani odeslanych formularu - mam akci?
if(isset($_POST["action"])){
    // mam pozadavek na login ?
    if($_POST["action"] == "login") {
        // mam co ulozit?
        if (isset($_POST["jmeno"]) && $_POST["jmeno"] != "" && isset($_POST["heslo"]) && $_POST["heslo"] != "") {
            // prihlasim uzivatele
            $login->login($_POST["jmeno"], $_POST["heslo"]);
        } else {
            echo "Chyba: Nebylo zadáno jméno ci heslo uživatele.<br>";
        }
    }
    // mam pozadavek na logout?
    else if($_POST["action"] == "logout"){
        // odhlasim uzivatele
        $login->logout();
    }
    // mam pozadavek na registraci?
    else if($_POST["action"] == "register") {
        if (isset($_POST["jmeno"]) && $_POST["jmeno"] != "" && isset($_POST["heslo"]) && $_POST["heslo"] != ""
            && isset($_POST["prijmeni"]) && $_POST["prijmeni"] != ""&& isset($_POST["mail"]) && $_POST["mail"] != "") {
            $login->register($_POST["mail"], $_POST["heslo"], $_POST["jmeno"], $_POST["prijmeni"]);
        }
    }
    // neznamy pozadavek
    else {
        echo "<script>alert('Chyba: Nebyla rozpoznána požadovaná akce.');</script>";
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login</title>
</head>
<body>
<nav class="py-2 bg-light border-bottom">
    <div class="container d-flex flex-wrap">
        <ul class="nav me-auto">
            <li class="nav-item"><a href="#" class="nav-link link-dark px-2 active" aria-current="page">Home</a></li>
            <li class="nav-item"><a href="#" class="nav-link link-dark px-2">Features</a></li>
            <li class="nav-item"><a href="#" class="nav-link link-dark px-2">Pricing</a></li>
            <li class="nav-item"><a href="#" class="nav-link link-dark px-2">FAQs</a></li>
            <li class="nav-item"><a href="#" class="nav-link link-dark px-2">About</a></li>
        </ul>

        <?php
        if(!$login->isUserLogged()) {
            ?>
            <ul class="nav">
                <li class="nav-item"><a href="#" class="nav-link link-dark px-2">Login</a></li>
                <li class="nav-item"><a href="#" class="nav-link link-dark px-2">Sign up</a></li>
            </ul>
            <?php
        } else {
            ?>
            <div class="dropdown text-end">
                <a href="#" class="d-block link-dark text-decoration-none dropdown-toggle" id="dropdownUser1" data-bs-toggle="dropdown" aria-expanded="false">
                    <img src="https://github.com/mdo.png" alt="mdo" width="32" height="32" class="rounded-circle">
                </a>
                <ul class="dropdown-menu text-small" aria-labelledby="dropdownUser1" style="">
                    <li><a class="dropdown-item" href="#">New project...</a></li>
                    <li><a class="dropdown-item" href="#">Settings</a></li>
                    <li><a class="dropdown-item" href="#">Profile</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item" href="#">Sign out</a></li>
                </ul>
            </div>
            <?php
        }
        ?>
    </div>
</nav>
<?php
if(!$login->isUserLogged()) {
    ?>
    yinz need to lawg inn
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
    yinz wanna register first?
    <form method="POST">
        <fieldset>
            <legend>Registrace noveho uživatele</legend>
            <label> mail
                <input type="text" name="mail" placeholder="-- zadejte mail --">
            </label>

            <label> pass
                <input type="text" name="heslo" placeholder="-- zadejte heslo --">
            </label>
            <label> name
                <input type="text" name="jmeno" placeholder="-- zadejte jméno --">
            </label>

            <label> surn
                <input type="text" name="prijmeni" placeholder="-- zadejte heslo --">
            </label>
            <button type="submit" name="action" value="register">
                Přihlásit uživatele
            </button>
        </fieldset>
    </form>
    <?php
} else {
    ?>
    @<?=$login->getUserName()?> yinz logged in and iss lit fam
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