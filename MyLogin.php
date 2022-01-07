<?php
include "dbconn.php";
class MyLogin
{

    /** @var MySessions $ses  Objekt pro praci se session. */
    private $ses;

    // viditelnost konstant od PHP v.7.1  !!!!

    /** @var string SESSION_KEY  Klic pro ulozeni uzivatele do session */
    private const SESSION_KEY = "usr";

    /** @var string KEY_NAME  Klic pro ulozeni jmena do pole.  */
    private const KEY_NAME = "jm";
    /** @var string KEY_DATE  Klic pro ulozeni datumu do pole. */
    private const KEY_DATE = "dt";

    /**
     *  Pri vytvoreni objektu zahajim session.
     */
    public function __construct(){
        require_once("MySessions.php");
        // vytvorim instanci vlastni tridy pro praci se session (objekt)
        $this->ses = new MySessions;
    }

    /**
     *  Otestuje, zda je uzivatel prihlasen.
     *  @return bool
     */
    public function isUserLogged():bool {
        return $this->ses->isSessionSet(self::SESSION_KEY);
    }

    /**
     *  Nastavi do session jmeno uzivatele a datum prihlaseni.
     *  @param string $userName Jmeno uzivatele.
     */
    public function login(string $userName, string $pass){
        $conn = openCon();
        if ($conn != null) {
            $stmt = $conn->prepare(sprintf("SELECT `user_password` FROM `matusik_users` WHERE `user_email`=\"%s\"", $userName));
            $stmt->execute();
            $result = $stmt->setFetchMode(PDO::FETCH_ASSOC);
            $resulte = $stmt->fetch();
            echo $resulte ["user_password"];
            if ($result == $pass) {
                $data = [self::KEY_NAME => $userName,
                    self::KEY_DATE => date("d. m. Y, G:i:s")];
                $this->ses->setSession(self::SESSION_KEY, $data);
            }
        }
        $conn = null;
    }

    public function register(string $userEmail, string $pass, string $name, string $surname) {
        $conn = openCon();
        if ($conn != null) {
            $stmt = $conn->prepare(sprintf("SELECT `user_email` FROM `matusik_users`"));
            $stmt->execute();
            $result = $stmt->setFetchMode(PDO::FETCH_ASSOC);
            $newMail = strtolower($userEmail);
            foreach ($stmt->fetchAll() as $mail) {
                if ($newMail == $mail ["user_email"]) {
                    echo "User with given e-mail already exists!";
                    $conn = null;
                    return;
                }
            }
            try {
                // set the PDO error mode to exception
                $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                $sql = sprintf("INSERT INTO `matusik_users` (user_email, user_name, user_password, user_role_id)
  VALUES (\"%s\", \"%s\", \"%s\", 4)", $newMail, $name . " " . $surname, password_hash($pass, PASSWORD_DEFAULT));
                // use exec() because no results are returned
                $conn->exec($sql);
                echo "New record created successfully";
            } catch(PDOException $e) {
                echo $sql . "<br>" . $e->getMessage();
            }
        }
    }

    /**
     *  Odhlasi uzivatele.
     */
    public function logout(){
        $this->ses->removeSession(self::SESSION_KEY);
    }

    /**
     *  Vrati informace o uzivateli.
     *  @return string|null  Informace o uzivateli.
     */
    public function getUserInfo() {
        if(!$this->isUserLogged()) {
            return null;
        }
        $d = $this->ses->readSession(self::SESSION_KEY);
        return "Jméno: " . $this->getUserName() . "<br>"
            . "Datum: " . $d[self::KEY_DATE] . "<br>";
    }

    public function getUserName() {
        if(!$this->isUserLogged()) {
            return null;
        }
        $d = $this->ses->readSession(self::SESSION_KEY);
        return $d[self::KEY_NAME];

    }
}
?>