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
     *  @param string $userMail Jmeno uzivatele.
     */
    public function login(string $userMail, string $pass){
        $result = getUserPass($userMail);
        if (password_verify($pass, $result) == true) {
            $data = [self::KEY_NAME => $userMail,
                self::KEY_DATE => date("d. m. Y, G:i:s")];
            $this->ses->setSession(self::SESSION_KEY, $data);
        }
    }

    public function register(string $userEmail, string $pass, string $name, string $surname) {
        $conn = openCon();
        if ($conn != null) {
            if (!userExists($userEmail)) {
                insertNewUser($userEmail, $name, $surname, $pass);
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
     *  @return array  Informace o uzivateli.
     */
    public function getUserInfo() {
        if(!$this->isUserLogged()) {
            return null;
        }
        $d = $this->ses->readSession(self::SESSION_KEY);
        $email = $d[self::KEY_NAME];
        return getUserInfo($email);
    }

    public function getUserName() {
        if(!$this->isUserLogged()) {
            return null;
        }
        $d = $this->ses->readSession(self::SESSION_KEY);
        $email = $d[self::KEY_NAME];
        return getUserName($email); //handle the database connections in a separate file
    }
    
}
?>