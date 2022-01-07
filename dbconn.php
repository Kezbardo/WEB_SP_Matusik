<?php
function openCon()
{
    $servername = "students.kiv.zcu.cz";
    $username = "db1_vyuka";
    $password = "db1_vyuka";
    $dbname = "db1_vyuka";

    try {
        $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
        // set the PDO error mode to exception
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $conn;
    } catch(PDOException $e) {
        echo "Connection failed: " . $e->getMessage();
    }
    return null;
}

function closeCon()
{
    $conn = null;
}
?>
