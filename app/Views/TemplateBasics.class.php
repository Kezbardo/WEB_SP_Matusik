<?php

namespace app\Views;

use app\Controllers\LoginController;
use app\Models\LoginModel;
use app\Models\SessionModel;

class TemplateBasics implements IView
{
    const PAGE_LANDING = "LandingPageTemplate.tpl.php";
    const PAGE_USER_MANAGEMENT = "UserManagementTemplate.tpl.php";
    const PAGE_LOGIN = "LoginTemplate.tpl.php";
    const PAGE_ARTICLES = "ArticleTemplate.tpl.php";


    public function printOutput(array $templateData, string $pageType = self::PAGE_LANDING)
    {
        $login = new LoginController();
        $this->getHTMLHeader($templateData['title'], $login);
        global $tplData;
        $tplData = $templateData;
        require_once($pageType);
        $this->getHTMLFooter();
    }


    public function getHTMLHeader(string $pageTitle, LoginController $login) {
        ?>
        <!DOCTYPE html>
        <html lang="en">
            <head>
                <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
                <meta charset="UTF-8">
                <meta name="viewport" content="width=device-width, initial-scale=1">
                <title><?php echo $pageTitle; ?></title>
        </head>
        <body>
            <nav class="py-2 bg-light border-bottom">
                <div class="container d-flex flex-wrap">
                    <ul class="nav me-auto">
                        <li class="nav-item"><a href="?page=uvod" class="nav-link link-dark px-2 active" aria-current="page">Home</a></li>
                        <li class="nav-item"><a href="?page=clanky" class="nav-link link-dark px-2">Články</a></li>
                        <li class="nav-item"><a href="?page=sprava" class="nav-link link-dark px-2">Uživatelé</a></li>
                    </ul>

                    <?php
                    if(!$login->isUserLogged()) {
                        ?>
                        <ul class="nav">
                            <li class="nav-item"><a href="?page=prihlaseni" class="nav-link link-dark px-2">Login</a></li>
                        </ul>
                        <?php
                    } else {
                        ?>
                        <div class="dropdown text-end">
                            <a href="#" class="nav-link link-dark text-decoration-none dropdown-toggle" id="dropdownUser1" data-bs-toggle="dropdown" aria-expanded="false">
                                <?php
                                $userInfo = $login->getUserInfo();
                                echo $userInfo["user_name"] . " (<b>" . $login->getRoleName($userInfo["user_role_id"]) . "</b>)";
                                ?>
                            </a>
                            <ul class="dropdown-menu text-small" aria-labelledby="dropdownUser1" style="">
                                <!--<li><a class="dropdown-item" href="#">Profile</a></li>-->
                                <li>
                                    <form method="POST">
                                        <button type="submit" name="action" value="logout" class="dropdown-item">
                                            Sign out
                                        </button>
                                    </form>
                                </li>
                            </ul>
                        </div>
                        <?php
                    }
                    ?>
                </div>
            </nav>
<?php
    }

    public function getHTMLFooter()
    {
    }

}