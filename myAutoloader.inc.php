<?php
const FILE_EXTENSIONS = array(".class.php", ".interface.php");

spl_autoload_register(function ($className){
    $className = str_replace("\\", "/", $className);
    $filename = dirname(__FILE__) . "/" . $className;
    foreach (FILE_EXTENSIONS as $ext) {
        if (file_exists($filename . $ext)) {
            $filename .= $ext;
            break;
        }
    }
    require_once($filename);
});
?>
