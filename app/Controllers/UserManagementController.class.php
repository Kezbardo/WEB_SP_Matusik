<?php

namespace app\Controllers;


use app\Models\DatabaseModel as MyDB;

class UserManagementController implements IController
{
    private $db;

    public function __construct()
    {
        $this->db = MyDB::getDatabaseModel();
    }

    public function show(string $pageTitle): array
    {
        $login = new LoginController();
        global $tplData;
        $tplData = [];
        $tplData['title'] = $pageTitle;

        //HANLDE POST ACTIONS HERE
        //OLD POST HANDLING
        if(isset($_POST["unban"])) {
            $this->db->unbanUser($_POST["unban"]);
        }
            // mam pozadavek na unban?
        else if (isset($_POST["ban"])) {
            $this->db->banUser($_POST["ban"]);
        }
        else if (isset($_POST["roleChange"])) {
            if (getUserInfo_ID($_POST["userRoleChange"])["user_role_id"] != $_POST["roleChange"]) {
                $this->db->changeRole($_POST["userRoleChange"], $_POST["roleChange"]);
            }
        }
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


        return $tplData;
    }

}