<?php

namespace app\Controllers;


use app\Models\DatabaseModel as MyDB;

class ArticleController implements IController
{
    private $db;
    private $login;


    public function __construct()
    {
        $this->db = MyDB::getDatabaseModel();
        $this->login = new LoginController();
    }

    function getReviewOverallScore(array $review)
    {
        $returnVal = $review["rating_quality"] + $review["rating_formality"] + $review["rating_novelty"] + $review["rating_linguistic"];
        $returnVal = $returnVal / 4.0;
        return (round($returnVal*2) / 2);
    }

    function scoreToStars(float $score) {
        $returnString = "";
        for ($i = 0; $i < 5; $i++){
            if ($score - $i >= 1) { //add filled star
                $returnString = $returnString . "<svg xmlns=\"http://www.w3.org/2000/svg\" width=\"16\" height=\"16\" fill=\"currentColor\" class=\"bi bi-star-fill\" viewBox=\"0 0 16 16\">
  <path d=\"M3.612 15.443c-.386.198-.824-.149-.746-.592l.83-4.73L.173 6.765c-.329-.314-.158-.888.283-.95l4.898-.696L7.538.792c.197-.39.73-.39.927 0l2.184 4.327 4.898.696c.441.062.612.636.282.95l-3.522 3.356.83 4.73c.078.443-.36.79-.746.592L8 13.187l-4.389 2.256z\"/>
</svg>";
            } else if ($score - $i == 0.5) { //add half star
                $returnString = $returnString . "<svg xmlns=\"http://www.w3.org/2000/svg\" width=\"16\" height=\"16\" fill=\"currentColor\" class=\"bi bi-star-half\" viewBox=\"0 0 16 16\">
  <path d=\"M5.354 5.119 7.538.792A.516.516 0 0 1 8 .5c.183 0 .366.097.465.292l2.184 4.327 4.898.696A.537.537 0 0 1 16 6.32a.548.548 0 0 1-.17.445l-3.523 3.356.83 4.73c.078.443-.36.79-.746.592L8 13.187l-4.389 2.256a.52.52 0 0 1-.146.05c-.342.06-.668-.254-.6-.642l.83-4.73L.173 6.765a.55.55 0 0 1-.172-.403.58.58 0 0 1 .085-.302.513.513 0 0 1 .37-.245l4.898-.696zM8 12.027a.5.5 0 0 1 .232.056l3.686 1.894-.694-3.957a.565.565 0 0 1 .162-.505l2.907-2.77-4.052-.576a.525.525 0 0 1-.393-.288L8.001 2.223 8 2.226v9.8z\"/>
</svg>";
            } else { //add empty star
                $returnString = $returnString . "<svg xmlns=\"http://www.w3.org/2000/svg\" width=\"16\" height=\"16\" fill=\"currentColor\" class=\"bi bi-star\" viewBox=\"0 0 16 16\">
  <path d=\"M2.866 14.85c-.078.444.36.791.746.593l4.39-2.256 4.389 2.256c.386.198.824-.149.746-.592l-.83-4.73 3.522-3.356c.33-.314.16-.888-.282-.95l-4.898-.696L8.465.792a.513.513 0 0 0-.927 0L5.354 5.12l-4.898.696c-.441.062-.612.636-.283.95l3.523 3.356-.83 4.73zm4.905-2.767-3.686 1.894.694-3.957a.565.565 0 0 0-.163-.505L1.71 6.745l4.052-.576a.525.525 0 0 0 .393-.288L8 2.223l1.847 3.658a.525.525 0 0 0 .393.288l4.052.575-2.906 2.77a.565.565 0 0 0-.163.506l.694 3.957-3.686-1.894a.503.503 0 0 0-.461 0z\"/>
</svg>";
            }
        }
        return $returnString;
    }

    function getReviewStars(array $review) {
        $retstr = "Hodnoceni: ";
        $incrementer = 'A';
        foreach (getAllArticleReviews($review["article_id"]) as $reviewStars) {
            if ($reviewStars["reviewer_id"] == $review["reviewer_id"]) {
                //display blue badge with star rating
                $retstr = $retstr . "<span class=\"badge badge-primary\">" . "Moje " . $this->scoreToStars($this->getReviewOverallScore($reviewStars)) . "</span>";
            } else {
                //display black badge with star rating
                $retstr = $retstr . "<span class=\"badge badge-secondary\">" . $incrementer . " " . $this->scoreToStars($this->getReviewOverallScore($reviewStars)) . "</span>";
            }
            $incrementer++;
        }
        return $retstr;
    }

    private function getReviewStars_aID(int $articleID): string
    {
        $retstr = "Hodnoceni: ";
        $incrementer = 'A';
        foreach (getAllArticleReviews($articleID) as $reviewStars) {
            //display black badge with star rating
            $retstr .= "<span class=\"badge badge-secondary\">" . $incrementer . " " . $this->scoreToStars($this->getReviewOverallScore($reviewStars)) . "</span>";
            $incrementer++;
        }
        return $retstr;
    }

    private function getAllPossibleReviewers(int $article_id)
    {
        $retArr = [];
        foreach (getAllReviewers() as $possibleReviewer) {
            if ($this->db->canBeAdded($possibleReviewer["reviewer_id"], $article_id)) {
                $retArr .= $possibleReviewer;
            }
        }
        return $retArr;
    }

    public function show(string $pageTitle): array
    {
        global $login;
        global $tplData;
        $tplData = [];
        $tplData['title'] = $pageTitle;

        //HANLDE POST ACTIONS HERE
        //OLD POST HANDLING
        if (isset($_POST["authorName"])) {
            $formOk = true;
            foreach ($_POST as $formField) { //check all form fields, none should be empty
                if (empty($formField)) {
                    echo "One of the fields was left blank!";
                    $formOk = false;
                    break;
                }
            }
            if ($formOk) {
                $newFile = uploadFile();
                if ($newFile != false) {
                    createNewArticle($_POST["authorName"], $_POST["articleName"], $_POST["articleContent"], $newFile, $login->getUserInfo()["user_id"]);
                }
            }
        }

        if (isset($_POST["newReviewerID"]) && is_numeric($_POST["newReviewerID"]) == true) {
            addNewReviewer($_POST["newReviewerID"], $_POST["reviewerArticleID"]);
        }


        if (isset($_POST["quality"])) {
            updateRating($_POST["quality"], $_POST["formality"], $_POST["novelty"], $_POST["linguistic"], $_POST["reviewContent"], $_POST["reviewID"]);
        }

        if ($login != null && $login->isUserLogged()) {
            switch($login->getUserInfo()["user_role_id"]){
                case 4:
                    //author view
                    $tplData['roleID'] = 4;
                    $tplData['articles'] = $this->db->getAllMyArticles($login->getUserInfo()["user_id"]);
                    foreach ($tplData['articles'] as $article) {
                        $tplData['articles'][$article["article_id"]] = $this->getReviewStars_aID($article["article_id"]);
                    }
                    break;
                case 3:
                    //review view
                    $tplData['roleID'] = 3;
                    $tplData['reviews'] = $this->db->getAllMyReviews($login->getUserInfo()["user_id"]);
                    foreach ($tplData['reviews'] as $review) {
                        $tplData['reviews']['article'] = $this->db->getArticleInfo($review["article_id"]);
                        $tplData['reviews']['stars'] = $this->getReviewStars($review);
                    }
                    break;
                default:
                    //modmin view
                    $tplData['roleID'] = 2;
                    $tplData['articles'] = $this->db->getAllArticles();
                    foreach ($tplData['articles'] as $article) {
                        $tplData['articles']['reviews'] = $this->db->getAllArticleReviews($article["article_id"]);
                        if (count($tplData['articles']['reviews']) < 3) {
                            $tplData['articles']['possible_reviewers'] = $this->getAllPossibleReviewers($article["article_id"]);
                        }
                        foreach ($tplData['articles']['reviews'] as $review) {
                            $tplData['articles']['reviews']['stars'] = $this->scoreToStars($this->getReviewOverallScore($review));
                            $tplData['articles']['reviews']['stars'] .= $this->scoreToStars($review["rating_quality"]);
                            $tplData['articles']['reviews']['stars'] .= $this->scoreToStars($review["rating_formality"]);
                            $tplData['articles']['reviews']['stars'] .= $this->scoreToStars($review["rating_novelty"]);
                            $tplData['articles']['reviews']['stars'] .= $this->scoreToStars($review["rating_linguistics"]);
                            $tplData['articles']['reviews']['reviewer_name'] = $this->db->getUserInfo_ID($review["reviewer_id"])["user_name"];
                        }
                    }
                    break;
            }

        } else {
            $tplData['toUser'] = "Pouze pro prihlasene uzivatele!";
        }


        return $tplData;
    }



}