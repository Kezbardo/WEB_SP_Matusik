<?php



global $tplData;
//require(DIRECTORY_VIEWS . "/TemplateBasics.class.php");
//$tplHeaders = new TemplateBasics();
?>


<?php
//$tplHeaders->getHTMLHeader($tplData['title']);

$res = "";
if (array_key_exists('stories',$tplData)) {
    print_r($tplData);
    foreach ($tplData['stories'] as $d) {
        $res .= "<h2>$d[article_name]</h2>";
        $res .= "Autor<br><br>";
        $res .= "uryvek";
    }
} else {
    $res .= "pohadky nenalezeny";
}
echo $res;

//$tplHeaders->getHTMLFooter();
