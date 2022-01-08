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
        return null;
    }
}

function getUserInfo(string $userMail) {
    $conn = openCon();
    if ($conn != null) {
        $stmt = $conn->prepare(sprintf("SELECT `user_id`,`user_email`,`user_name`,`user_role_id`,`user_not_banned` 
                                        FROM `matusik_users` WHERE `user_email`=\"%s\"", $userMail));
        $stmt->execute();
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $result = $stmt->fetch();
        $conn = null;
        return $result;
    }
    return "DB connect failure!";
}

function getUserName(string $userMail)
{
    $conn = openCon();
    if ($conn != null) {
        $stmt = $conn->prepare(sprintf("SELECT `user_name` FROM `matusik_users` WHERE `user_email`=\"%s\"", $userMail));
        $stmt->execute();
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $result = $stmt->fetch();
        $conn = null;
        return $result["user_name"];
    }
    return "DB connect failure!";
}

function getUserPass(string $userMail)
{
    $conn = openCon();
    if ($conn != null) {
        $stmt = $conn->prepare(sprintf("SELECT `user_password` FROM `matusik_users` WHERE `user_email`=\"%s\"", $userMail));
        $stmt->execute();
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $result = $stmt->fetch();
        $conn = null;
        return $result["user_password"];
    }
    return "DB connect failure!";
}

function userExists(string $userMail): bool
{
    $conn = openCon();
    $stmt = $conn->prepare("SELECT `user_email` FROM `matusik_users`");
    $stmt->execute();
    $stmt->setFetchMode(PDO::FETCH_ASSOC);
    $newMail = strtolower($userMail);
    foreach ($stmt->fetchAll() as $mail) {
        if ($newMail == $mail ["user_email"]) {
            $conn = null;
            return true;
        }
    }
    $conn = null;
    return false;
}

function insertNewUser (string $email, string $name, string $surname, string $pass)
{
    $conn = openCon();
    try {
        // set the PDO error mode to exception
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $sql = sprintf("INSERT INTO `matusik_users` 
                                    (user_email, user_name, user_password, user_role_id, user_not_banned) VALUES 
                                    (\"%s\", \"%s\", \"%s\", 4, 0)",
            strtolower($email), $name . " " . $surname, password_hash($pass, PASSWORD_DEFAULT));
        // use exec() because no results are returned
        $conn->exec($sql);
    } catch (PDOException $e) {
        echo $sql . "<br>" . $e->getMessage();
    }
}

function getRoleFromID(int $role_id) {
    $conn = openCon();
    if ($conn != null) {
        $stmt = $conn->prepare(sprintf("SELECT `nazev` FROM `matusik_pravo` WHERE `id_pravo`=\"%d\"", $role_id));
        $stmt->execute();
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $result = $stmt->fetch();
        $conn = null;
        return $result["nazev"];
    }
    return "DB connect failure!";
}

function getAllUsers() {
    $conn = openCon();
    if ($conn != null) {
        $stmt = $conn->prepare("SELECT `user_id`,`user_email`,`user_name`,`user_role_id`,`user_not_banned` 
                                        FROM `matusik_users`");
        $stmt->execute();
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $result = $stmt->fetchAll();
        $conn = null;
        return $result;
    }
    return "DB connect failure!";
}

function banUser(int $user_id) {
    $conn = openCon();
    try {
        // set the PDO error mode to exception
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $sql = sprintf("UPDATE `matusik_users` SET `user_not_banned` = '1' WHERE `user_id` = '%d';", $user_id);
        // use exec() because no results are returned
        $conn->exec($sql);
    } catch (PDOException $e) {
        echo $sql . "<br>" . $e->getMessage();
    }
}

function unbanUser(int $user_id) {
    $conn = openCon();
    try {
        // set the PDO error mode to exception
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $sql = sprintf("UPDATE `matusik_users` SET `user_not_banned` = '0' WHERE `user_id` = '%d';", $user_id);
        // use exec() because no results are returned
        $conn->exec($sql);
    } catch (PDOException $e) {
        echo $sql . "<br>" . $e->getMessage();
    }

}
?>
