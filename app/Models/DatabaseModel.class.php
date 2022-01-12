<?php
namespace app\Models;


class DatabaseModel
{
    private static $database;
    private $pdo;

    private function __construct()
    {
        try {
            $this->pdo = new \PDO("mysql:host=".DB_SERVER.";dbname=".DB_NAME,DB_USER,DB_PASS);
            // set the PDO error mode to exception
            $this->pdo->setAttribute(\PDO::ATTR_EMULATE_PREPARES, false);
            $this->pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        } catch (\PDOException $e) {
            echo "Connection failed: " . $e->getMessage();
        }
    }

    public static function getDatabaseModel(): DatabaseModel
    {
        if(empty(self::$database)){
            self::$database = new DatabaseModel();
        }
        return self::$database;
    }

    function getUserInfo_mail(string $userMail):?array
    {
        global $pdo;
        if ($this->pdo != null) {
            $stmt = $pdo->prepare("SELECT `user_id`,`user_email`,`user_name`,`user_role_id`,`user_not_banned` 
                                        FROM `matusik_users` WHERE `user_email`=:email");
            $stmt->execute(['email' => $userMail]);
            $stmt->setFetchMode(\PDO::FETCH_ASSOC);
            return $stmt->fetch();
        }
        return null;
    }

    function getUserInfo_ID(int $userID):?array
    {
        global $pdo;
        if ($pdo != null) {
            $stmt = $pdo->prepare("SELECT `user_id`,`user_email`,`user_name`,`user_role_id`,`user_not_banned` 
                                        FROM `matusik_users` WHERE `user_id`=:userID");
            $stmt->execute(['userID' => $userID]);
            $stmt->setFetchMode(\PDO::FETCH_ASSOC);
            return $stmt->fetch();
        }
        return null;
    }

    function getUserName(string $userMail):?string
    {
        global $pdo;
        if ($pdo != null) {
            $stmt = $pdo->prepare("SELECT `user_name` FROM `matusik_users` WHERE `user_email`=:email");
            $stmt->execute(['email' => $userMail]);
            $stmt->setFetchMode(\PDO::FETCH_ASSOC);
            $result = $stmt->fetch();
            
            return $result["user_name"];
        }
        return null;
    }

    function getUserPass(string $userMail):?string
    {
        global $pdo;
        if ($pdo != null) {
            $stmt = $pdo->prepare("SELECT `user_password` FROM `matusik_users` WHERE `user_email`=:email");
            $stmt->execute(['email' => $userMail]);
            $stmt->setFetchMode(\PDO::FETCH_ASSOC);
            $result = $stmt->fetch();
            
            return $result["user_password"];
        }
        return null;
    }

    function userExists(string $userMail): bool
    {
        global $pdo;
        $stmt = $pdo->prepare("SELECT `user_email` FROM `matusik_users`");
        $stmt->execute();
        $stmt->setFetchMode(\PDO::FETCH_ASSOC);
        $newMail = strtolower($userMail);
        foreach ($stmt->fetchAll() as $mail) {
            if ($newMail == $mail ["user_email"]) {
                
                return true;
            }
        }
        return false;
    }

    function insertNewUser(string $email, string $name, string $surname, string $pass)
    {
        global $pdo;
        try {
            // set the PDO error mode to exception
            $stmt = $pdo->prepare("INSERT INTO `matusik_users` 
                                    (user_email, user_name, user_password, user_role_id, user_not_banned) VALUES 
                                    (:email, :uname, :pass, 4, 0)");
            $stmt->execute(['email' => strtolower($email), 'uname' => $name . " " . $surname, 'pass' => password_hash($pass, PASSWORD_DEFAULT)]);
        } catch (\PDOException $e) {
            echo $e->getMessage();
        }
    }

    function getRoleFromID(int $role_id):?string
    {
        global $pdo;
        if ($pdo != null) {
            $stmt = $pdo->prepare("SELECT `nazev` FROM `matusik_pravo` WHERE `id_pravo`=:roleID");
            $stmt->execute(['roleID' => $role_id]);
            $stmt->setFetchMode(\PDO::FETCH_ASSOC);
            $result = $stmt->fetch();
            
            return $result["nazev"];
        }
        return null;
    }

    function getAllUsers():?array
    {
        global $pdo;
        if ($pdo != null) {
            $stmt = $pdo->prepare("SELECT `user_id`,`user_email`,`user_name`,`user_role_id`,`user_not_banned` 
                                        FROM `matusik_users`");
            $stmt->execute();
            $stmt->setFetchMode(\PDO::FETCH_ASSOC);
            return $stmt->fetchAll();
        }
        return null;
    }

    function getAllRolesBelow(int $role_id):?array
    {
        global $pdo;
        if ($pdo != null) {
            $stmt = $pdo->prepare("SELECT * FROM `matusik_pravo` WHERE `id_pravo`> :roleID");
            $stmt->execute(['roleID' => $role_id]);
            $stmt->setFetchMode(\PDO::FETCH_ASSOC);
            return $stmt->fetchAll();
        }
        return null;
    }

    function banUser(int $user_id)
    {
        global $pdo;
        try {
            $stmt = $pdo->prepare("UPDATE `matusik_users` SET `user_not_banned` = '1' WHERE `user_id` = :userID;");
            $stmt->execute(['userID' => $user_id]);
        } catch (\PDOException $e) {
            echo $e->getMessage();
        }
    }

    function unbanUser(int $user_id)
    {
        global $pdo;
        try {
            $stmt = $pdo->prepare("UPDATE `matusik_users` SET `user_not_banned` = '0' WHERE `user_id` = :userID;");
            $stmt->execute(['userID' => $user_id]);
        } catch (\PDOException $e) {
            echo $e->getMessage();
        }
    }

    function changeRole(int $user_id, int $new_role_id)
    {
        global $pdo;
        try {
            $stmt = $pdo->prepare("UPDATE `matusik_users` SET `user_role_id` = :roleID WHERE `user_id` = :userID;");
            $stmt->execute(['roleID' => $new_role_id, 'userID' => $user_id]);
        } catch (\PDOException $e) {
            echo $e->getMessage();
        }
    }

    function createNewArticle(string $authorName, string $articleName, string $content, string $newFile, int $user_id)
    {
        global $pdo;
        try {
            $stmt = $pdo->prepare("INSERT INTO `matusik_clanky` 
                                    (user_id, article_authors, article_name, article_abstract, article_filename, article_approved) VALUES 
                                    (:userID, :authors, :articleName, :abstract, :article_filename, 0)");
            // use exec() because no results are returned
            $stmt->execute([
                'userID' => $user_id, 'authors' => $authorName,
                'articleName' => $articleName, 'abstract' => $content, 'article_filename' => $newFile]);
        } catch (\PDOException $e) {
            echo $e->getMessage();
        }
    }

    function getAllMyReviews(int $user_id):?array
    {
        global $pdo;
        if ($pdo != null) {
            $stmt = $pdo->prepare("SELECT * FROM `matusik_recenze` WHERE `reviewer_id`= :userID");
            $stmt->execute(['userID' => $user_id]);
            $stmt->setFetchMode(\PDO::FETCH_ASSOC);
            return $stmt->fetchAll();
        }
        return null;
    }

    function getArticleInfo(int $article_id):?array
    {
        global $pdo;
        if ($pdo != null) {
            $stmt = $pdo->prepare("SELECT `article_authors`, `article_name`, `article_abstract`, `article_filename` FROM `matusik_clanky` WHERE `article_id`= :articleID");
            $stmt->execute(['articleID' => $article_id]);
            $stmt->setFetchMode(\PDO::FETCH_ASSOC);
            $result = $stmt->fetchAll();
            
            return $result[0];
        }
        return null;

    }

    function getAllArticleReviews(int $article_id):?array
    {
        global $pdo;
        if ($pdo != null) {
            $stmt = $pdo->prepare("SELECT * FROM `matusik_recenze` WHERE `article_id`= :articleID");
            $stmt->execute(['articleID' => $article_id]);
            $stmt->setFetchMode(\PDO::FETCH_ASSOC);
            return $stmt->fetchAll();
        }
        return null;
    }


    function getAllArticles():?array
    {
        global $pdo;
        if ($pdo != null) {
            $stmt = $pdo->prepare("SELECT * FROM `matusik_clanky`");
            $stmt->execute();
            $stmt->setFetchMode(\PDO::FETCH_ASSOC);
            return $stmt->fetchAll();
        }
        return null;
    }


    function getAllReviewers():?array
    {
        global $pdo;
        if ($pdo != null) {
            $stmt = $pdo->prepare("SELECT * FROM `matusik_users` WHERE `user_role_id` = 3");
            $stmt->execute();
            $stmt->setFetchMode(\PDO::FETCH_ASSOC);
            return $stmt->fetchAll();
        }
        return null;

    }

    function canBeAdded(int $reviewID, int $articleID):?array
    {
        global $pdo;
        if ($pdo != null) {
            $stmt = $pdo->prepare("SELECT * FROM `matusik_recenze` WHERE `reviewer_id` = :reviewer AND `article_id` = :articleID");
            $stmt->execute(['reviewer' => $reviewID,
                'articleID' => $articleID]);
            $stmt->setFetchMode(\PDO::FETCH_ASSOC);
            return $stmt->fetchAll();
        }
        return null;

    }


    function addNewReviewer(int $newReviewerID, int $reviewerArticleID)
    {
        global $pdo;
        try {
            $stmt = $pdo->prepare("INSERT INTO `matusik_recenze` 
                                    (article_id, reviewer_id) VALUES 
                                    (:articleID, :reviewerID)");
            // use exec() because no results are returned
            $stmt->execute(['articleID' => $reviewerArticleID, 'reviewerID' => $newReviewerID]);
        } catch (\PDOException $e) {
            echo $e->getMessage();
        }
    }

    function updateRating(float $quality, float $formality, float $novelty, float $linguistic, string $reviewContent, int $reviewID)
    {
        global $pdo;
        try {
            $stmt = $pdo->prepare("UPDATE `matusik_recenze` SET `rating_quality` = :rQuality,`rating_formality` = :rFormality,
                                `rating_novelty` = :rNovelty,`rating_linguistic` = :rLinguistic,`review_comment` = :rComment 
                                WHERE `review_id` = :rID;");
            $stmt->execute(['rQuality' => $quality, 'rFormality' => $formality,
                'rNovelty' => $novelty, 'rLinguistic' => $linguistic,
                'rComment' => $reviewContent, 'rID' => $reviewID]);
        } catch (\PDOException $e) {
            echo $e->getMessage();
        }
    }

    function getAllMyArticles(int $user_id):?array
    {
        global $pdo;
        if ($pdo != null) {
            $stmt = $pdo->prepare("SELECT * FROM `matusik_clanky` WHERE `user_id`= :userID");
            $stmt->execute(['userID' => $user_id]);
            $stmt->setFetchMode(\PDO::FETCH_ASSOC);
            return $stmt->fetchAll();
        }
        return null;
    }
}
?>
