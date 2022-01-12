<?php
/*
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
*/
// nacteni souboru s funkcemi loginu (pracuje se session)
require_once("MyLogin.php");
require_once("upload.php");
$login = new MyLogin();

if (isset($_POST["authorName"])) {
    $formOk = true;
    foreach ($_POST as $formField) {
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
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <script src="https://cdn.ckeditor.com/ckeditor5/31.1.0/classic/ckeditor.js"></script>
    <script src="js/button.js"></script>
    <title>Clanky</title>
</head>
<body class="bg-light">
<?php include "./header.php" ?>
<h3>Články</h3>
<?php

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

function getReviewEditCard(array $review): string
{
    $reviewCard = "<div class =\"container\"><form method=\"POST\"><div class=\"row\"><div class=\"col\">";
    $reviewCard .= "<input type=\"hidden\" name=\"reviewID\" value=\"" . $review["review_id"] . "\">";
    $reviewCard .= "<div class=\"row\"><div class=\"col\"><label for='quality'>Kvalita obsahu:</label></div><div class=\"col\">
        <input type=\"number\" id=\"quality\" name=\"quality\" min=\"0.0\" max=\"5\" step=\"0.5\" value=\"".(($review["rating_quality"] != null)?$review["rating_quality"]:0)."\">";
    $reviewCard .= "</div></div><div class=\"row\"><div class=\"col\"><label for='formality'>Formální úroveň:</label></div><div class=\"col\">
            <input type=\"number\" id=\"formality\" name=\"formality\" min=\"0.0\" max=\"5\" step=\"0.5\" value=\"".(($review["rating_formality"] != null)?$review["rating_formality"]:0)."\">";
    $reviewCard .= "</div></div><div class=\"row\"><div class=\"col\"><label for='novelty'>Novost:</label></div><div class=\"col\">
            <input type=\"number\" id=\"novelty\" name=\"novelty\" min=\"0.0\" max=\"5\" step=\"0.5\" value=\"".(($review["rating_novelty"] != null)?$review["rating_novelty"]:0)."\">";
    $reviewCard .= "</div></div><div class=\"row\"><div class=\"col\"><label for='linguistic'>Kvalita jazyka:</label></div><div class=\"col\">
            <input type=\"number\" id=\"linguistic\" name=\"linguistic\" min=\"0.0\" max=\"5\" step=\"0.5\" value=\"".(($review["rating_linguistic"] != null)?$review["rating_linguistic"]:0)."\">";
    $reviewCard .= "</div></div>"; //first column end
    $reviewCard .= "<button type=\"submit\" class=\"btn btn-success\" value=\"Nahrát Recenzi\">Odeslat recenzi</button><button type=\"reset\" class=\"btn btn-danger\">Vrátit změny</button>";
    $reviewCard .= "</div><div class=\"col\"><textarea name=\"reviewContent\" id=\"editorReview". $review["review_id"] . "\">
                        " . (($review["review_comment"] != null)?$review["review_comment"]:"<p>Vložte k recenzi komentář.</p>") . "
                    </textarea>
                    <script>
                        let editor". $review["review_id"] . ";
                        ClassicEditor
                            .create(document.querySelector('#editorReview". $review["review_id"] . "'), {
                                toolbar: [ 'heading', '|', 'bold', 'italic', 'link', 'bulletedList', 'numberedList', 'blockQuote' ]
                            }).then(newEditor => {
                                editor = newEditor;
                            })
                            .catch(error => {
                                console.error(error);
                            });
                        var btnEl = document.querySelector('#submit');
                        btnEl.addEventListener('click', function () {
                            editor.updateSourceElement();
                        })
                    </script></div>";
    $reviewCard .= "</div></form></div>";
    return $reviewCard;
}

function getReviewStars(array $review) {
    $retstr = "Hodnoceni: ";
    $incrementer = 'A';
    foreach (getAllArticleReviews($review["article_id"]) as $reviewStars) {
        if ($reviewStars["reviewer_id"] == $review["reviewer_id"]) {
            //display blue badge with star rating
            $retstr = $retstr . "<span class=\"badge badge-primary\">" . "Moje " . scoreToStars(getReviewOverallScore($reviewStars)) . "</span>";
        } else {
            //display black badge with star rating
            $retstr = $retstr . "<span class=\"badge badge-secondary\">" . $incrementer . " " . scoreToStars(getReviewOverallScore($reviewStars)) . "</span>";
        }
        $incrementer++;
    }
    return $retstr;
}

function getReviewStars_aID(int $articleID) {
    $retstr = "Hodnoceni: ";
    $incrementer = 'A';
    foreach (getAllArticleReviews($articleID) as $reviewStars) {
        //display black badge with star rating
        $retstr .= "<span class=\"badge badge-secondary\">" . $incrementer . " " . scoreToStars(getReviewOverallScore($reviewStars)) . "</span>";
        $incrementer++;
    }
    return $retstr;
}




if(!$login->isUserLogged()) {
    ?>
    Konference je pouze pro prihlasene cleny
    <?php
} else {
    switch ($login->getUserInfo()["user_role_id"]) {
        case 4:
            echo "<a class=\"btn btn-primary\" data-bs-toggle=\"collapse\" href=\"#collapseNewArticle\" role=\"button\" aria-expanded=\"false\" aria-controls=\"collapseExample\">
                        Nahrát nový článek
                      </a>";
            echo "</p>";
            echo "<div class=\"collapse\" id=\"collapseNewArticle\">
                        <div class=\"card card-body text-black\">"; ?>
        <div class="container-fluid">
            <form method="POST" enctype="multipart/form-data">
                <div class="row">
                <div class="col">
                    <div class="row"><div class="col-5">
                <label for="authorName"> Jmena autoru </label></div><div class="col">
                            <input type="text" name="authorName" id="authorName"></div></div><div class="row"><div class="col-5">
                <label for="articleName"> Jmeno clanku </label></div><div class="col">
                    <input type="text" name="articleName" id="articleName"></div></div><div class="row"><div class="col-5">
                <label for="fileToUpload"> Clanek (format PDF, max. velikost 5MB) </label></div><div class="col">
                                    <input type="file" name="fileToUpload" id="fileToUpload"></div></div>
                <input type="submit" class="btn btn-primary" value="Nahrát Článek" name="submit" onclick="return verifyFields()" id="submit">
                    </div>
                <div class="col">
                <label for="editorArticle"> Abstrakt  </label>
                    <textarea name="articleContent" id="editorArticle">
                        Abstrakt clanku zde:
            </textarea>
                    <script>
                        let editor;
                        ClassicEditor
                            .create(document.querySelector('#editorArticle'), {
                                toolbar: [ 'heading', '|', 'bold', 'italic', 'link', 'bulletedList', 'numberedList', 'blockQuote' ]
                            }).then(newEditor => {
                            editor = newEditor;
                        })
                            .catch(error => {
                                console.error(error);
                            });

                        var btnEl = document.querySelector('#submit');
                        btnEl.addEventListener('click', function () {
                            editor.updateSourceElement();
                        })
                    </script>
                </div>
                </div>
            </form>
        </div>
                        <?php
            echo "</div>
                      </div></div>";
            foreach(getAllMyArticles($login->getUserInfo()["user_id"]) as $article ) {
                echo "<div class=\"container-fluid border bg-secondary text-white\">";
                //DISPLAY ARTICLE RATING
                echo "<div class=\"container-fluid\">";
                if ($article["article_approved"] == 0) {
                    //show review info, denied
                    echo "<div class=\"container-fluid bg-danger text-white\">";
                    echo getReviewStars_aID($article["article_id"]);
                    echo "<span class=\"badge alert-danger\">Status: zamitnuto</span>";
                    echo "</div>";
                } else if ($article["article_approved"] == 1) {
                    //show review info, accepted
                    echo "<div class=\"container-fluid bg-success text-white\">";
                    echo getReviewStars_aID($article["article_id"]);
                    echo "<span class=\"badge alert-success\">Status: akceptovano</span>";
                    echo "</div>";
                } else {
                    //show review info, undecided
                    echo "<div class=\"container-fluid bg-info text-white\">";
                    echo getReviewStars_aID($article["article_id"]);
                    echo "<span class=\"badge alert-info\">Status: Ceka na rozhodnuti</span>";
                    echo "</div>";
                }
                echo "</div>";
                //DISPLAY ARTICLE INFO
                echo "<p><u><em>" . $article["article_authors"] . "</em>: " . $article["article_name"] . "</u></p>";
                echo "<strong>Abstrakt:</strong>" . $article["article_abstract"];
                echo "<p><a class=\"btn btn-success\" href=\"data/" . $article["article_filename"] .  "\" download=\"\">Download</a>";
                echo "</p>";
                echo "</div>";
            }
    ?>
        <!-- AUTHOR VIEW OF PAGE -->

    <?php
            break;
        case 3:
?>
            <!-- REVIEWER VIEW OF PAGE -->
            <?php
            foreach (getAllMyReviews($login->getUserInfo()["user_id"]) as $review) {
                echo "<div class=\"container-fluid border bg-secondary text-white\">";
                $article = getArticleInfo($review["article_id"]);
                //DISPLAY ARTICLE RATING
                echo "<div class=\"container-fluid\">";
                if ($review["rating_quality"] == null) {
                    //review hasn't been completed yet by this reviewer
                    echo "<div class=\"container-fluid bg-info text-white\">";
                    echo "Hodnoceni: ceka na posouzeni";
                    echo "</div>";
                } else {
                    if ($article["article_approved"] == 0) {
                        //show review info, denied
                        echo "<div class=\"container-fluid bg-danger text-white\">";
                        echo getReviewStars($review);
                        echo "<span class=\"badge alert-danger\">Status: zamitnuto</span>";
                        echo "</div>";
                    } else if ($article["article_approved"] == 1) {
                        //show review info, accepted
                        echo "<div class=\"container-fluid bg-success text-white\">";
                        echo getReviewStars($review);
                        echo "<span class=\"badge alert-success\">Status: akceptovano</span>";
                        echo "</div>";
                    } else {
                        //show review info, undecided
                        echo "<div class=\"container-fluid bg-info text-white\">";
                        echo getReviewStars($review);
                        echo "<span class=\"badge alert-info\">Status: Ceka na rozhodnuti</span>";
                        echo "</div>";
                    }
                }
                echo "</div>";
                //DISPLAY ARTICLE INFO
                echo "<p><u><em>" . $article["article_authors"] . "</em>: " . $article["article_name"] . "</u></p>";
                echo "<strong>Abstrakt:</strong>" . $article["article_abstract"];
                echo "<p><a class=\"btn btn-success\" href=\"data/" . $article["article_filename"] .  "\" download=\"\">Download</a>";
                echo "<a class=\"btn btn-primary\" data-bs-toggle=\"collapse\" href=\"#collapseExample" . $review["article_id"] . "\" role=\"button\" aria-expanded=\"false\" aria-controls=\"collapseExample\">
                        Recenzovat
                      </a>";
                echo "</p>";
                echo "<div class=\"collapse\" id=\"collapseExample" . $review["article_id"] . "\">
                        <div class=\"card card-body text-black\">
                        " . getReviewEditCard($review) . "
                        </div>
                      </div></div>";
            }
            ?>
            <!-- END OF REVIEWER VIEW OF PAGE -->



            <?php
            break;
default:
?>
<!-- MOD+ADMIN VIEW OF PAGE -->
<?php
foreach (getAllArticles() as $article) {
    //DISPLAY ARTICLE INFO (brief)
    echo "<div class=\"container-fluid border bg-secondary text-white\"><p>";
    switch($article["article_approved"]) {
        case 1:
            echo "<span class=\"badge alert-success\">Status: akceptovano</span>";
            break;
        case 0:
            echo "<span class=\"badge alert-danger\">Status: zamitnuto</span>";
            break;
        default:
            echo "<span class=\"badge alert-info\">Status: Ceka na rozhodnuti</span>";
            break;
    }
    echo "</p>";
    echo "<p><u><em>" . $article["article_authors"] . "</em>: " . $article["article_name"] . "</u></p>";
    echo "<p>Recenze:</p>";
    $reviews = getAllArticleReviews($article["article_id"]);
    if (count($reviews) < 3) {
        //show form for adding new reviewers
        echo    "<form method=\"POST\">";
        echo    "<div class=\"input-group\">";
        echo    "<div class=\"input-group-prepend\">" .
                    "<label class=\"input-group-text\" for=\"inputGroupSelect\">Pridat recenzenta</label>" .
                "</div>";

        echo    "<select class=\"custom-select\" id=\"inputGroupSelect\" name=\"newReviewerID\">" .
                "<option selected>Recenzent</option>";
        foreach (getAllReviewers() as $possibleReviewer) {
            if (count(canBeAdded($possibleReviewer["user_id"], $article["article_id"])) == 0)
                echo "<option value=\"". $possibleReviewer["user_id"] . "\">" . $possibleReviewer["user_name"] . "</option>";
        }
        echo    "</select>";
        echo    "<div class=\"input-group-append\">" .
                "<input type=\"hidden\" name=\"reviewerArticleID\" value=\"" . $article["article_id"] . "\">" .
                    "<button class=\"btn btn-success\" type=\"submit\" onclick='return confirm(\"Add this usr?\")'>Pridat</button>" .
                "</div>";
        echo    "</div></form>";
    }
    echo       "<table class=\"table\">
                <thead>
                    <tr>
                        <th scope=\"col\">Jmeno Recenzenta</th>
                        <th scope=\"col\">Celkem</th>
                        <th scope=\"col\">Obsah</th>
                        <th scope=\"col\">Formalne</th>
                        <th scope=\"col\">Novost</th>
                        <th scope=\"col\">Jazyk</th>
                        <th scope=\"col\">Odebrat</th>
                    </tr>
                </thead>
              <tbody>";
    foreach ($reviews as $review) {
        echo "<tr>";
        echo "<th scope=\"row\">". getUserInfo_ID($review["reviewer_id"])["user_name"] ."</th>";
        if ($review["rating_quality"] == null) {
            echo "<td colspan=\"5\"><span class=\"badge alert-info\">Ceka na hodnoceni</span></td>";
        } else {
            echo "<td><span class=\"badge alert-primary\">" . scoreToStars(getReviewOverallScore($review)) . "</span></td>";
            echo "<td><span class=\"badge alert-primary\">" . scoreToStars($review["rating_quality"]) . "</span></td>";
            echo "<td><span class=\"badge alert-primary\">" . scoreToStars($review["rating_formality"]) . "</span></td>";
            echo "<td><span class=\"badge alert-primary\">" . scoreToStars($review["rating_novelty"]) . "</span></td>";
            echo "<td><span class=\"badge alert-primary\">" . scoreToStars($review["rating_linguistic"]) . "</span></td>";
        }
        echo "<td> <a href=\"#\" class=\"badge alert-danger\">PLACEHOLDER</a>  </td>";
        echo "</tr>";
    }
    echo "</tbody></table>";
    echo "</div>";
}

            break;
    }
}
?>
<!-- END OF MOD+ADMIN VIEW OF PAGE -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>
</body>
</html>