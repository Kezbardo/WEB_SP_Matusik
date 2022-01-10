<?php
// nacteni souboru s funkcemi loginu (pracuje se session)
require_once("MyLogin.php");
$login = new MyLogin();
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
<nav class="py-2 bg-light border-bottom">
    <div class="container d-flex flex-wrap">
        <ul class="nav me-auto">
            <li class="nav-item"><a href="index.php" class="nav-link link-dark px-2 active" aria-current="page">Home</a></li>
            <li class="nav-item"><a href="clanky.php" class="nav-link link-dark px-2">Clanky</a></li>
            <li class="nav-item"><a href="#" class="nav-link link-dark px-2">Recenze</a></li>
            <li class="nav-item"><a href="#" class="nav-link link-dark px-2">FAQs</a></li>
            <li class="nav-item"><a href="#" class="nav-link link-dark px-2">About</a></li>
        </ul>

        <?php
        if(!$login->isUserLogged()) {
            ?>
            <ul class="nav">
                <li class="nav-item"><a href="login.php" class="nav-link link-dark px-2">Login</a></li>
                <li class="nav-item"><a href="#" class="nav-link link-dark px-2">Sign up</a></li>
            </ul>
            <?php
        } else {
            ?>
            <div class="dropdown text-end">
                <a href="#" class="nav-link link-dark text-decoration-none dropdown-toggle" id="dropdownUser1" data-bs-toggle="dropdown" aria-expanded="false">
                    <?php
                    $userInfo = $login->getUserInfo();
                    echo $userInfo["user_name"] . " (<b>" . getRoleFromID($userInfo["user_role_id"]) . "</b>)";
                    ?>
                </a>
                <ul class="dropdown-menu text-small" aria-labelledby="dropdownUser1" style="">
                    <li><a class="dropdown-item" href="#">New project...</a></li>
                    <li><a class="dropdown-item" href="uzivatele.php">Uzivatele</a></li>
                    <li><a class="dropdown-item" href="#">Profile</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li>
                        <form method="POST">
                            <button type="submit" name="action" value="logout" class="dropdown-item">
                                Sign out
                            </button>
                        </form>
                    </li>
                </ul>
            </div>
            <?php
        }
        ?>
    </div>
</nav>