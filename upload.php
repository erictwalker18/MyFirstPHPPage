<?php
require_once("Functions/parser.php");
require_once("Functions/database.php");
require_once("Includes/common.php");

print_header("Hours Upload Log");

$target_dir = "Uploads/";
$target_file = $target_dir . basename($_FILES["fileToUpload"]["name"]);
$uploadOk = 1;
$fileType = pathinfo($target_file,PATHINFO_EXTENSION);
// Check if file already exists
if (file_exists($target_file)) {
    echo "File already exists on server. Deleting...";
    unlink($target_file);
    echo "Old file deleted. Trying to upload new version. <br>";
}
// Check file size
if ($_FILES["fileToUpload"]["size"] > 5000000) {
    echo "Sorry, your file is too large. ";
    $uploadOk = 0;
}
// Allow certain file formats
if($fileType != "csv") {
    echo "Sorry, only CSV files are allowed. ";
    $uploadOk = 0;
}
// Check if $uploadOk is set to 0 by an error
if ($uploadOk == 0) {
    echo "Your file was not uploaded. <br>";
// if everything is ok, try to upload file
} else {
    if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {
        echo "The file ". basename( $_FILES["fileToUpload"]["name"]). " has been uploaded.<br>";
        //parse, then delete
		set_time_limit(120);
        parse_csv($target_file);
        unlink($target_file);
        echo "Hours have been added.<br>";
    } else {
        echo "Sorry, there was an error uploading your file.";
    }
}

print_footer();
?>
