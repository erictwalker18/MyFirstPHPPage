<?php 
    require_once ("Includes/simpledb-config.php"); 
    require_once  ("Includes/connectDB.php");
    require_once ("Includes/common.php");

    print_header("Upload Time Entry File");      
?>
<form id="upload_section" action="upload.php" method="post" enctype="multipart/form-data">
    Select file to upload:
    <input type="file" name="fileToUpload" id="fileToUpload">
    <input type="submit" value="Upload File" name="submit">
</form>
<br>
<br>
<h3>First time uploading?</h3>
<p>Download the template here! Enter your hours then save the "tabular record" sheet as a comma delimited CSV. Upload this file using the above button!</p>
<form method="get" action="Templates/Template Hours Log.xlsx">
    <button type="submit">Download Template</button>
</form>

<?php
    if ( is_admin() )
    {
?>
    <br><h3>Template upload for administrators:</h3>
    <form id="upload_section" action="uploadTemplate.php" method="post" enctype="multipart/form-data">
    Select new template file to upload:
    <input type="file" name="fileToUpload" id="fileToUpload">
    <input type="submit" value="Upload File" name="submit">
    </form>
    <br><h3>Project upload for administrators:</h3>
    <form id="upload_section" action="uploadProjects.php" method="post" enctype="multipart/form-data">
    Select new project file to upload:
    <input type="file" name="fileToUpload" id="fileToUpload">
    <input type="submit" value="Upload File" name="submit">
    </form>
<?php
    }
?>

<?php 
    include ("Includes/footer.php");
?>