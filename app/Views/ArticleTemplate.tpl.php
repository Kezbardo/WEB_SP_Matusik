<?php


global $tplData;
//require(DIRECTORY_VIEWS . "/TemplateBasics.class.php");
//$tplHeaders = new TemplateBasics();
?>


<?php
//$tplHeaders->getHTMLHeader($tplData['title']);

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

$res = "";
if (array_key_exists('roleID',$tplData)) {
    switch ($tplData['roleID']) {
        case 4:
            ?>
                <p>
                    <a class="btn btn-primary" data-bs-toggle="collapse" href="#collapseNewArticle" role="button" aria-expanded="false" aria-controls="collapseExample">
                        Nahrát nový článek
                    </a>
                </p>
            <div class="collapse" id="collapseNewArticle">
                <div class="card card-body text-black">
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
        </div>
            </div>
        <?php foreach ($tplData['articles'] as $article) {?>
        <div class="container-fluid border bg-secondary text-white">
            <div class="container-fluid">
                <?php if ($article["article_approved"] == 0) {?>
                    <div class="container-fluid bg-danger text-white">
                    <?php echo $tplData['articles'][$article["article_id"]] ?>
                    <span class="badge alert-danger">Status: nepřijato</span>
                    </div>
                <?php } else if ($article["article_approved"] == 1) { ?>
                    <div class="container-fluid bg-success text-white">
                        <?php echo $tplData['articles'][$article["article_id"]] ?>
                        <span class="badge alert-success">Status: přijato</span>
                    </div>
                <?php } else { ?>
                    <div class="container-fluid bg-info text-white">
                        <?php echo $tplData['articles'][$article["article_id"]] ?>
                        <span class="badge alert-info">Status: Čeká na rozhodnutí</span>
                    </div>
                <?php } ?>
            </div>
            <p><u><em> <?php echo $article["article_authors"]; ?> </em> <?php echo $article["article_name"]; ?></u></p>
            <strong>Abstrakt:</strong> <?php echo $article["article_abstract"]; ?>
            <p>
                <a class="btn btn-success" href="../../data/<?php echo $article["article_filename"]; ?>" download="">Stáhnout článek</a>
            </p>
        </div>
            <?php }
            break;
        case 3:
            foreach ($tplData['reviews'] as $review) {
                ?>
                <div class="container-fluid border bg-secondary text-white">
                    <div class="container-fluid">
                        <?php if ($review["rating_quality"] == null) {
                            ?>
                            <div class="container-fluid bg-info text-white">Hodnocení: čeká na posouzení</div>
                            <?php
                        } else {
                            if ($review['article']["article_approved"] == 0) { ?>
                                <div class="container-fluid bg-danger text-white">
                                    <?php echo $tplData['reviews']['stars']; ?>
                                    <span class="badge alert-danger">Status: Nepřijato</span>
                                </div>
                                <?php } else if ($review['article']["article_approved"] == 1) { ?>
                                <div class="container-fluid bg-success text-white">
                                    <?php echo $tplData['reviews']['stars']; ?>
                                    <span class="badge alert-success">Status: Přijato</span>
                                </div> <?php } else { ?>
                                    <div class="container-fluid bg-info text-white">
                                        <?php echo $tplData['reviews']['stars']; ?>
                                        <span class="badge alert-info">Status: Čeká na rozhodnutí</span>
                                    </div>
                                <?php
                            }
                        } ?>
                                <p><u><em> <?php echo $review['article']["article_authors"]; ?> </em> <?php echo $review['article']["article_name"]; ?></u></p>
                                <strong>Abstrakt:</strong> <?php echo $review['article']["article_abstract"]; ?>
                                <p>
                                    <a class="btn btn-success" href="../../data/<?php echo $review['article']["article_filename"]; ?>" download="">Stáhnout článek</a>
                                    <a class="btn btn-primary" data-bs-toggle="collapse" href="#collapseExample<?php echo $review["article_id"]; ?>" role="button" aria-expanded="false" aria-controls="collapseExample">
                                    Recenzovat
                                    </a>
                                </p>
                                <div class="collapse" id="collapseExample<?php echo $review["article_id"]; ?>">
                                <div class="card card-body text-black">
                                                           <?php echo $this->getReviewEditCard($review); ?>
                                </div>
                                </div>
                                </div>

                                <div class="container-fluid bg-info text-white"></div>
                                <?php
                        ?>
                    </div>
                </div>
                <?php
            }
            break;
        default: //here I realized that it probably should've been through php first
            foreach ($tplData['articles'] as $article) {
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
                if (count($article["possible_reviewers"]) > 0) {
                    //show form for adding new reviewers
                    echo    "<form method=\"POST\">";
                    echo    "<div class=\"input-group\">";
                    echo    "<div class=\"input-group-prepend\">" .
                        "<label class=\"input-group-text\" for=\"inputGroupSelect\">Pridat recenzenta</label>" .
                        "</div>";

                    echo    "<select class=\"custom-select\" id=\"inputGroupSelect\" name=\"newReviewerID\">" .
                        "<option selected>Recenzent</option>";
                    foreach ($article["possible_reviewers"] as $possibleReviewer) {
                            echo "<option value=\"". $possibleReviewer["user_id"] . "\">" . $possibleReviewer["user_name"] . "</option>";
                    }
                    echo    "</select>";
                    echo    "<div class=\"input-group-append\">" .
                        "<input type=\"hidden\" name=\"reviewerArticleID\" value=\"" . $article["article_id"] . "\">" .
                        "<button class=\"btn btn-success\" type=\"submit\" onclick='return confirm(\"Přidat tohoto uživatele jako recenzenta?\")'>Přidat</button>" .
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
                foreach ($tplData['articles']['reviews'] as $review) {
                    echo "<tr>";
                    echo "<th scope=\"row\">". $tplData['articles']['reviews']['reviewer_name'] ."</th>";
                    if ($review["rating_quality"] == null) {
                        echo "<td colspan=\"5\"><span class=\"badge alert-info\">Ceka na hodnoceni</span></td>";
                    } else {
                        foreach ($tplData['articles']['reviews']['stars'] as $starSection) {
                            echo "<td><span class=\"badge alert-primary\">" . $starSection . "</span></td>";
                        }
                    }
                    echo "<td> <a href=\"#\" class=\"badge alert-danger\">PLACEHOLDER</a>  </td>";
                    echo "</tr>";
                }
                echo "</tbody></table>";
                echo "</div>";
            }
            break;
    }
} else { //user was not logged in
    $res .= $tplData['toUser'];
}
echo $res;

//$tplHeaders->getHTMLFooter();
