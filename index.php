<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


require_once("myAutoloader.inc.php");
require_once("settings.inc.php");

$app = new \app\ApplicationStart();
$app->appStart();
?>

<?php
//UNDER THIS SHOULD BE REMOVED IG?
// nacteni souboru s funkcemi loginu (pracuje se session)
/*require_once("MyLogin.php");
$login = new MyLogin();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Hlavni Stranka</title>
</head>
<body>
<?php include "./header.php" ?>
<?php
    if(!$login->isUserLogged()) {
?>
        Konference je pouze pro prihlasene cleny
<?php
    } else {
?>
        <h2>Homepage</h2>
<?php
    }
?>
<br>
O konferenci
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>
</body>
</html>*/
?>