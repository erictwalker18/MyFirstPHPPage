<?php 
    require_once ("Includes/simpledb-config.php"); 
    require_once  ("Includes/connectDB.php");
    include("Includes/header.php");         
?>
<div id="main">
<form action="upload.php" method="post" enctype="multipart/form-data">
    Select file to upload:
    <input type="file" name="fileToUpload" id="fileToUpload">
    <input type="submit" value="Upload File" name="submit">
</form>
</div>

<?php 
    include ("Includes/footer.php");
?>