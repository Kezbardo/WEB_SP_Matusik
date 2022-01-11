<?php
function uploadFile()
{
    $target_dir = getcwd() . "/data/";
    $target_file_name = substr(uniqid(pathinfo(basename($_FILES["fileToUpload"]["name"]), PATHINFO_FILENAME) . "_"), 0, -3) . ".pdf";
    $target_file = $target_dir . $target_file_name;
    $uploadOk = 1;
    $fileType = strtolower(pathinfo(basename($_FILES["fileToUpload"]["name"]), PATHINFO_EXTENSION));

// Check if file already exists
    if (file_exists($target_file)) {
        echo "Sorry, file already exists.";
        $uploadOk = 0;
    }

// Check file size
    if ($_FILES["fileToUpload"]["size"] > 5000000) {
        echo "Sorry, your file is too large.";
        $uploadOk = 0;
    }

// Allow certain file formats
    if ($fileType != "pdf") {
        echo "Sorry, only PDF files are allowed.";
        $uploadOk = 0;
    }

// Check if $uploadOk is set to 0 by an error
    if ($uploadOk == 0) {
        echo "Sorry, your file was not uploaded.";
// if everything is ok, try to upload file
    } else {
        if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {
            echo "The file " . htmlspecialchars(basename($_FILES["fileToUpload"]["name"])) . " has been uploaded.";
            return $target_file_name;
        } else {
            echo "Sorry, there was an error uploading your file.";
        }
    }
    return false;
}
?>