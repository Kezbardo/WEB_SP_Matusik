<?php

namespace app\Controllers;

use app\Models\DatabaseModel as MyDB;

class LandingController implements IController
{
    private $db;

    public function __construct()
    {
        //require_once(DIRECTORY_MODELS . "/DatabaseModel.class.php");
        $this->db = MyDB::getDatabaseModel();
    }

    public function show(string $pageTitle): array
    {
        global $tplData;
        $tplData = [];

        $tplData['title'] = $pageTitle;

        $tplData['stories'] = $this->db->getAllArticles();


        return $tplData;
    }


}