<?php

namespace app\Controllers;

use app\Models\DatabaseModel as MyDB;
use app\Models\SessionModel;

class LoginController
{
    private $db;
    /** @var SessionModel $ses  Objekt pro praci se session. */
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
        //require_once("MySessions.php"); if it doesn't work try requiring sessionmodel here
        // vytvorim instanci vlastni tridy pro praci se session (objekt)
        $this->ses = new SessionModel();
        $this->db = MyDB::getDatabaseModel();
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
        global $db;
        $result = $db->getUserPass($userMail);
        if (password_verify($pass, $result) == true) {
            $data = [self::KEY_NAME => $userMail,
                self::KEY_DATE => date("d. m. Y, G:i:s")];
            $this->ses->setSession(self::SESSION_KEY, $data);
        }
    }

    public function register(string $userEmail, string $pass, string $name, string $surname)
    {
        global $db;
        if (!$db->userExists($userEmail)) {
            $db->insertNewUser($userEmail, $name, $surname, $pass);
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
        global $db;
        if(!$this->isUserLogged()) {
            return null;
        }
        $d = $this->ses->readSession(self::SESSION_KEY);
        $email = $d[self::KEY_NAME];
        return $db->getUserInfo_mail($email);
    }

    public function getUserName() {
        global $db;
        if(!$this->isUserLogged()) {
            return null;
        }
        $d = $this->ses->readSession(self::SESSION_KEY);
        $email = $d[self::KEY_NAME];
        return $db->getUserName($email); //handle the database connections in a separate file
    }

    public function getRoleName(int $role_id) {
        global $db;
        return $db->getRoleFromID($role_id);
    }
}