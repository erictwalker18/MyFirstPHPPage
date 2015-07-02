<?php require_once ("Includes/common.php"); ?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <title>Timecard Testing Site</title>
        <link href="/Styles/Site.css" rel="stylesheet" type="text/css" />
        <meta name="viewport" content="width=device-width, initial-scale=1"> 


    </head>
    <body>
        <div class="outer-wrapper"> <!-- This div gets closed in the footer -->
        <header>
            <div class="content-wrapper">
                <div class="float-left">
                    <p class="site-title">
                        <a href="http://www.placeways.com"> <img src="../Images/Placeways Standard Logo 754x527.png" alt="Logo" style="width:110px;height:77px;"> </a> 
                        <a href="/index.php">Timecards</a>
                    </p>
                </div>
                <div class="float-right">
                    <section id="login">
                        <ul id="login">
                        <?php
                        if (logged_on())
                        {
                            echo '<li><a href="/logoff.php">Sign out</a></li>' . "\n";
                        }
                        else
                        {
                            echo '<li><a href="/logon.php">Login</a></li>' . "\n";
                        }
                        ?>
                        </ul>
                        <?php if (logged_on()) {
                            echo "<div class=\"welcomeMessage\">Welcome, <strong>{$GLOBALS['user']['username']}</strong></div>\n";
                        } ?>
                    </section>
                </div>

                <div class="clear-fix"></div>
            </div>

                <section class="navigation" data-role="navbar">
                    <nav>
                        <ul id="menu">
                            <li><a href="/index.php">Home</a></li>
                            <li><a href="/uploadpage.php">Upload</a></li>
                            <li><a href="/time.php">Log Hours</a></li>
                            <li><a href="/people.php">People</a></li>
                            <li><a href="/projects.php">Projects</a></li>
                            <li><a href="/categories.php">Categories</a></li>
                            <li><a href="/report.php">Reports</a></li>
                        </ul>
                    </nav>
            </section>
        </header>
        <div id="main"> <!-- This div also gets closed in the footer! -->
            <h2><?php echo $GLOBALS['page_title'] ?></h2>

