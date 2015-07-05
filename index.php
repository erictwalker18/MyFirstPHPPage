<?php 
    require_once ("Includes/simpledb-config.php"); 
    require_once  ("Includes/connectDB.php");
    include("Includes/common.php");

    print_header("Welcome to the Placeways timecard system.");
?>

    <ol class="round">
        <li class="one">
            <h5>Login</h5>
           Your username and password should be provided by a system administrator. 
        </li>
        <li class="two">
            <h5>Enter hours</h5>
             After you login, you can enter hours through a form or upload a CSV from a template.
         </li>
    </ol>

<?php 
    include ("Includes/footer.php");
?>
