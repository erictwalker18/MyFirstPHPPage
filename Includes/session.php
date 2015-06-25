<?php
    session_start();
    require_once  ("Includes/connectDB.php");

    function logged_on()
    {
        return isset($_SESSION['userid']);
    }
?>