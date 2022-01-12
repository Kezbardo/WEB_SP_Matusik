<?php
//////////////////////////////////////////////////////////////////
/////////////////  Globalni nastaveni aplikace ///////////////////
//////////////////////////////////////////////////////////////////

//// Pripojeni k databazi ////

/** Adresa serveru. */
define("DB_SERVER","students.kiv.zcu.cz"); // https://students.kiv.zcu.cz lze 147.228.63.10, ale musite byt na VPN
/** Nazev databaze. */
define("DB_NAME","db1_vyuka");
/** Uzivatel databaze. */
define("DB_USER","db1_vyuka");
/** Heslo uzivatele databaze */
define("DB_PASS","db1_vyuka");


//// Nazvy tabulek v DB ////

/** Tabulka s uzivateli. */
define("TABLE_USERS", "matusik_users");
/** Tabulka s recenzemi. */
define("TABLE_REVIEWS", "matusik_recenze");
/** Tabulka s pravy. */
define("TABLE_ROLES", "matusik_pravo");
/** Tabulka s clanky. */
define("TABLE_ARTICLES", "matusik_clanky");


//// Dostupne stranky webu ////

/** Adresar kontroleru. */
const DIRECTORY_CONTROLLERS = "app/Controllers";
/** Adresar modelu. */
const DIRECTORY_MODELS = "app/Models";
/** Adresar sablon */
const DIRECTORY_VIEWS = "app/Views";

/** Klic defaultni webove stranky. */
const DEFAULT_WEB_PAGE_KEY = "uvod";

/** Dostupne webove stranky. */
const WEB_PAGES = array(//// Uvodni stranka ////
    "uvod" => array(
        "title" => "Úvodní stránka",

        //// kontroler
        "controller_class_name" => \app\Controllers\LandingController::class,


        // ClassBased sablona
        "view_class_name" => \app\Views\TemplateBasics::class,

        // TemplateBased sablona
        //"view_class_name" => \app\Views\TemplateBased\TemplateBasics::class,
        "template_type" => \app\Views\TemplateBasics::PAGE_LANDING,
    ),
    //// KONEC: Uvodni stranka ////

    //// Sprava uzivatelu ////
    "sprava" => array(
        "title" => "Správa uživatelů",

        //// kontroler
        "controller_class_name" => \app\Controllers\UserManagementController::class,

        "view_class_name" => \app\Views\TemplateBasics::class,

        // ClassBased sablona
        //"view_class_name" => \app\Views\ClassBased\UserManagementTemplate::class,

        // TemplateBased sablona
        //"view_class_name" => \app\Views\TemplateBased\TemplateBasics::class,
        "template_type" => \app\Views\TemplateBasics::PAGE_USER_MANAGEMENT,
    ),
    //// KONEC: Sprava uzivatelu ////

    "clanky" => array(
        "title" => "Články",

        "controller_class_name" => \app\Controllers\ArticleController::class,

        "view_class_name" => \app\Views\TemplateBasics::class,

        //"view_class_name" => \app\Views\TemplateBased\TemplateBasics::class,
        "template_type" => \app\Views\TemplateBasics::PAGE_ARTICLES,
    ),"prihlaseni" => array(
        "title" => "Přihlášení",

        "controller_class_name" => \app\Controllers\LoginController::class,

        "view_class_name" => \app\Views\TemplateBasics::class,

        //"view_class_name" => \app\Views\TemplateBased\TemplateBasics::class,
        "template_type" => \app\Views\TemplateBasics::PAGE_LOGIN,
    ),
);

?>
