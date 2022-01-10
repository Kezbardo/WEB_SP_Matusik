<?php
// nacteni souboru s funkcemi loginu (pracuje se session)
require_once("MyLogin.php");
require_once("upload.php");
$login = new MyLogin();

$formOK = true;
foreach ($_POST as $formField) {
    if (empty($formField)) {
        echo "One of the fields was left blank!";
        $formOK = false;
    }
}

if (isset($_POST["authorName"]) && $formOK) {
    $newFile = uploadFile();
    createNewArticle($_POST["authorName"],$_POST["articleName"],$_POST["content"],$newFile,$login->getUserInfo()["user_id"]);
}

if (isset($_POST["newReviewerID"]) && is_numeric($_POST["newReviewerID"]) == true) {
    addNewReviewer($_POST["newReviewerID"], $_POST["reviewerArticleID"]);
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
<?php
function getReviewOverallScore(mixed $review)
{
    $returnVal = $review["rating_quality"] + $review["rating_formality"] + $review["rating_novelty"] + $review["rating_linguistic"];
    return $returnVal/4.0;
}

function scoreToStars(float $score) {
    $returnString = "";
    for ($i = 0; $i < 5; $i++){
        if ($score - $i > 1) { //add filled star
            $returnString = $returnString . "<i class=\"bi bi-star-fill\"></i>";
        } else if ($score - $i == 0.5) { //add half star
            $returnString = $returnString . "<i class=\"bi bi-star-half\"></i>";
        } else { //add empty star
            $returnString = $returnString . "<i class=\"bi bi-star\"></i>";
        }
    }
}

function getReviewEditCard(array $review)
{
    return "";
}

function getReviewStars(mixed $review) {
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




if(!$login->isUserLogged()) {
    ?>
    Konference je pouze pro prihlasene cleny
    <?php
} else {
    switch ($login->getUserInfo()["user_role_id"]) {
        case 4:
    ?>
        <!-- AUTHOR VIEW OF PAGE -->
        Nahrani noveho clanku:
    <form method="POST" enctype="multipart/form-data">
        <label> Jmena autoru
            <input type="text" name="authorName" id="authorName"></label>
        <label> Jmeno clanku
            <input type="text" name="articleName" id="articleName"></label>
        <label> Abstrakt
            <textarea name="content" id="editor">
                        Abstrakt clanku zde:
            </textarea>
            <script>
                let editor;
                ClassicEditor
                    .create(document.querySelector('#editor'), {
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
        </label>
        <label> Clanek (format PDF, max. velikost 5MB)
            <input type="file" name="fileToUpload" id="fileToUpload"></label>
        <input type="submit" value="Upload Image" name="submit" onclick="return verifyFields()" id="submit">
    </form>
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
                if ($review["rating_quality"] == null ) {
                    //review hasn't been completed yet by this reviewer
                    echo "<div class=\"container-fluid bg-info text-white\">";
                    echo "Hodnoceni: ceka na posouzeni";
                    echo "</div>";
                } else {
                    if ($review["review_verdict"] == 0) {
                        //show review info, denied
                        echo "<div class=\"container-fluid bg-danger text-white\">";
                        echo getReviewStars($review);
                        echo "<span class=\"badge alert-danger\">Status: zamitnuto</span>";
                        echo "</div>";
                    } else if ($review["review_verdict"] == 1) {
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
    Anim pariatur cliche reprehenderit, enim eiusmod high life accusamus terry richardson ad squid. Nihil anim keffiyeh helvetica, craft beer labore wes anderson cred nesciunt sapiente ea proident.
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
                    "<button class=\"btn btn-outline-success\" type=\"submit\" onclick='return confirm(\"Add this usr?\")'>Pridat</button>" .
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
<br>
CLANKY
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>
</body>
</html>