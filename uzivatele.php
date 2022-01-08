<?php
// nacteni souboru s funkcemi loginu (pracuje se session)
require_once("MyLogin.php");
$login = new MyLogin();
// mam pozadavek na ban ?
if(isset($_POST["unban"])) {
    unbanUser($_POST["unban"]);
}
    // mam pozadavek na unban?
else if (isset($_POST["ban"])) {
    banUser($_POST["ban"]);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Uzivatele</title>
    <script src="js/button.js"></script>
</head>
<body>
<?php include "./header.php" ?>

<?php
if(!$login->isUserLogged()) {
    ?>
    yinz need to lawg inn
    Konference je pouze pro prihlasene cleny
    <?php
} else {
    ?>
    yinz logged in and iss lit fam

    Sprava uzivatelu

    <table class="table">
        <thead>
        <tr>
            <th scope="col">ID</th>
            <th scope="col">E-mail</th>
            <th scope="col">Jmeno</th>
            <th scope="col">Role</th>
            <th scope="col">Povoleni</th>
        </tr>
        </thead>
        <tbody>
        <?php
        switch ($login->getUserInfo()["user_role_id"]) {
            case 1: //ADMIN
                foreach (getAllUsers() as $user) {
                    echo "<tr>";
                    echo "<th scope=\"row\">". $user["user_id"] ."</th>";
                    echo "<td>" . $user["user_email"] .  "</td>";
                    echo "<td>" . $user["user_name"] .  "</td>";
                    echo "<td>" . getRoleFromID($user["user_role_id"]) .  "</td>";
                    echo "<td>";
                    echo "<form method=\"POST\">";
                    if ($user["user_email"] != $login->getUserInfo()["user_email"]) {
                        if ($user["user_not_banned"] == 1) {
                            echo "<button type=\"submit\" class=\"btn btn-success\" onclick='return confirm(\"Unban this user?\")' name=\"unban\" value=\"" . $user["user_id"] .  "\">Unban</button>";
                        } else {
                            echo "<button type=\"submit\" class=\"btn btn-danger\" onclick='return confirm(\"Ban this user?\")' name=\"ban\" value=\"" . $user["user_id"] .  "\">Ban</button>";
                        }
                    } else {
                        echo ($user["user_not_banned"] == 0 ? "Not Banned": "Banned");
                    }
                    echo "</form>";
                    echo "</td>";
                    echo "</tr>";
                }
                break;
            case 2: //MOD
                foreach (getAllUsers() as $user) {
                    echo "<tr>";
                    echo "<th scope=\"row\">". $user["user_id"] ."</th>";
                    echo "<td>" . $user["user_email"] .  "</td>";
                    echo "<td>" . $user["user_name"] .  "</td>";
                    echo "<td>" . getRoleFromID($user["user_role_id"]) .  "</td>";
                    echo "<td>" . ($user["user_not_banned"] == 0 ? "Not Banned": "Banned") .  "</td>";
                    echo "</tr>";
                }
                break;
            default: //RECENZENT + AUTOR
                foreach (getAllUsers() as $user) {
                    echo "<tr>";
                    echo "<th scope=\"row\">". $user["user_id"] ."</th>";
                    echo "<td>" . $user["user_email"] .  "</td>";
                    echo "<td>" . $user["user_name"] .  "</td>";
                    echo "<td>" . getRoleFromID($user["user_role_id"]) .  "</td>";
                    echo "<td>" . ($user["user_not_banned"] == 0 ? "Not Banned": "Banned") .  "</td>";
                    echo "</tr>";
                }
                break;
        }
        ?>
        </tbody>
    </table>

    <?php
    $conn = openCon();
}
?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>
</body>
</html>