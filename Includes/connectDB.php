<?php
    require_once ("/Includes/simpledb-config.php");
    require_once ("/Functions/database.php");

    // Create database connection
    $databaseConnection = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
	//$databaseConnection = new mysqli ('localhost', 'root', 'CViz_15_**', 'timesystem2015');
    if ($databaseConnection->connect_error)
    {
        die("Database selection failed: " . $databaseConnection->connect_error);
    }

    // Create tables if needed.
    prep_DB_content();
?>