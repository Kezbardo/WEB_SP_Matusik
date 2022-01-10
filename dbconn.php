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
        $conn->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $conn;
    } catch(PDOException $e) {
        echo "Connection failed: " . $e->getMessage();
        return null;
    }
}

function getUserInfo_mail(string $userMail) {
    $conn = openCon();
    if ($conn != null) {

        $stmt = $conn->prepare("SELECT `user_id`,`user_email`,`user_name`,`user_role_id`,`user_not_banned` 
                                        FROM `matusik_users` WHERE `user_email`=:email");
        $stmt->execute(['email' => $userMail]);
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $result = $stmt->fetch();
        $conn = null;
        return $result;
    }
    return "DB connect failure!";
}

function getUserInfo_ID(int $userID) {
    $conn = openCon();
    if ($conn != null) {
        $stmt = $conn->prepare("SELECT `user_id`,`user_email`,`user_name`,`user_role_id`,`user_not_banned` 
                                        FROM `matusik_users` WHERE `user_id`=:userID");
        $stmt->execute(['userID' => $userID]);
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
        $stmt = $conn->prepare("SELECT `user_name` FROM `matusik_users` WHERE `user_email`=:email");
        $stmt->execute(['email' => $userMail]);
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
        $stmt = $conn->prepare("SELECT `user_password` FROM `matusik_users` WHERE `user_email`=:email");
        $stmt->execute(['email' => $userMail]);
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
        $stmt = $conn->prepare("INSERT INTO `matusik_users` 
                                    (user_email, user_name, user_password, user_role_id, user_not_banned) VALUES 
                                    (:email, :uname, :pass, 4, 0)");
        $stmt->execute(['email' => strtolower($email), 'uname' => $name . " " . $surname, 'pass' => password_hash($pass, PASSWORD_DEFAULT)]);
    } catch (PDOException $e) {
        echo $sql . "<br>" . $e->getMessage();
    }
}

function getRoleFromID(int $role_id) {
    $conn = openCon();
    if ($conn != null) {
        $stmt = $conn->prepare("SELECT `nazev` FROM `matusik_pravo` WHERE `id_pravo`=:roleID");
        $stmt->execute(['roleID' => $role_id]);
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

function getAllRolesBelow(int $role_id)
{
    $conn = openCon();
    if ($conn != null) {
        $stmt = $conn->prepare("SELECT * FROM `matusik_pravo` WHERE `id_pravo`> :roleID");
        $stmt->execute(['roleID' => $role_id]);
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
        $stmt = $conn->prepare("UPDATE `matusik_users` SET `user_not_banned` = '1' WHERE `user_id` = :userID;");
        $stmt->execute(['userID' => $user_id]);
    } catch (PDOException $e) {
        echo $sql . "<br>" . $e->getMessage();
    }
}

function unbanUser(int $user_id) {
    $conn = openCon();
    try {
        $stmt = $conn->prepare("UPDATE `matusik_users` SET `user_not_banned` = '0' WHERE `user_id` = :userID;");
        $stmt->execute(['userID' => $user_id]);
    } catch (PDOException $e) {
        echo $sql . "<br>" . $e->getMessage();
    }

}

function changeRole(int $user_id, int $new_role_id) {
    $conn = openCon();
    try {
        $stmt = $conn->prepare("UPDATE `matusik_users` SET `user_role_id` = :roleID WHERE `user_id` = :userID;");
        $stmt->execute(['roleID' => $new_role_id, 'userID' => $user_id]);
    } catch (PDOException $e) {
        echo $sql . "<br>" . $e->getMessage();
    }
}

function createNewArticle(string $authorName, string $articleName, string $content, string $newFile, int $user_id)
{
    $conn = openCon();
    try {
        $stmt = $conn->prepare("INSERT INTO `matusik_clanky` 
                                    (user_id, article_authors, article_name, article_abstract, article_filename, article_approved) VALUES 
                                    (:userID, :authors, :articleName, :abstract, :article_filename, 0)");
        // use exec() because no results are returned
        $stmt->execute([
            'userID' => $user_id, 'authors' => $authorName,
            'articleName' => $articleName, 'abstract' => $content, 'article_filename' => $newFile ]);
    } catch (PDOException $e) {
        echo $sql . "<br>" . $e->getMessage();
    }
}

function getAllMyReviews(int $user_id) {
    $conn = openCon();
    if ($conn != null) {
        $stmt = $conn->prepare("SELECT * FROM `matusik_recenze` WHERE `reviewer_id`= :roleID");
        $stmt->execute(['roleID' => $user_id]);
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $result = $stmt->fetchAll();
        $conn = null;
        return $result;
    }
    return "DB connect failure!";
}

function getArticleInfo(int $article_id) {
    $conn = openCon();
    if ($conn != null) {
        $stmt = $conn->prepare("SELECT `article_authors`, `article_name`, `article_abstract`, `article_filename` FROM `matusik_clanky` WHERE `article_id`= :articleID");
        $stmt->execute(['articleID' => $article_id]);
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $result = $stmt->fetchAll();
        $conn = null;
        return $result[0];
    }
    return "DB connect failure!";

}

function getAllArticleReviews(int $article_id) {
    $conn = openCon();
    if ($conn != null) {
        $stmt = $conn->prepare("SELECT * FROM `matusik_recenze` WHERE `article_id`= :articleID");
        $stmt->execute(['articleID' => $article_id]);
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $result = $stmt->fetchAll();
        $conn = null;
        return $result;
    }
    return "DB connect failure!";
}


function getAllArticles() {
    $conn = openCon();
    if ($conn != null) {
        $stmt = $conn->prepare("SELECT * FROM `matusik_clanky`");
        $stmt->execute();
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $result = $stmt->fetchAll();
        $conn = null;
        return $result;
    }
    return "DB connect failure!";
}


function getAllReviewers()
{
    $conn = openCon();
    if ($conn != null) {
        $stmt = $conn->prepare("SELECT * FROM `matusik_users` WHERE `user_role_id` = 3");
        $stmt->execute();
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $result = $stmt->fetchAll();
        $conn = null;
        return $result;
    }
    return "DB connect failure!";

}

function canBeAdded(int $reviewID, int $articleID)
{
    $conn = openCon();
    if ($conn != null) {
        $stmt = $conn->prepare("SELECT * FROM `matusik_recenze` WHERE `reviewer_id` = :reviewer AND `article_id` = :articleID");
        $stmt->execute(['reviewer' => $reviewID,
                        'articleID' => $articleID]);
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $result = $stmt->fetchAll();
        $conn = null;
        return $result;
    }
    return "DB connect failure!";

}


function addNewReviewer(int $newReviewerID, int $reviewerArticleID)
{
    $conn = openCon();
    try {
        $stmt = $conn->prepare("INSERT INTO `matusik_recenze` 
                                    (article_id, reviewer_id) VALUES 
                                    (:articleID, :reviewerID)");
        // use exec() because no results are returned
        $stmt->execute(['articleID' => $reviewerArticleID, 'reviewerID' => $newReviewerID]);
    } catch (PDOException $e) {
        echo $sql . "<br>" . $e->getMessage();
    }
}
?>
