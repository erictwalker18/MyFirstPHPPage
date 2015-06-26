<?php
    require_once ("Includes/session.php");
    require_once ("Includes/simpledb-config.php"); 
    require_once ("Includes/connectDB.php");
    include("Includes/header.php");

    if (isset($_POST['submit']))
    {
        $lastname = $_POST['last_name'];
        $firstname = $_POST['first_name'];
        $username = $_POST['username'];
        $password = $_POST['password'];

        $query = "INSERT INTO people ";
        $statement = $databaseConnection->prepare($query);
        $statement->bind_param('ss', $username, $password);
        //needs work...
        /*
        Consider using tom's code for this?






        */
        $statement->execute();
        $statement->store_result();

        if ($statement->num_rows == 1)
        {
            $statement->bind_result($_SESSION['userid'], $_SESSION['username']);
            $statement->fetch();
            header ("Location: index.php");
        }
        else
        {
            echo "Username/password combination is incorrect.";
        }
    }
?>
<div id="main">
    <h2>Register</h2>
        <form action="logon.php" method="post">
            <fieldset>
            <legend>Register</legend>
            <ol>
                <li>
                    <label for="last_name">Last name:</label> 
                    <input type="text" name="last_name" value="" id="last_name" />
                </li>
                <li>
                    <label for="first_name">First name:</label> 
                    <input type="text" name="first_name" value="" id="first_name" />
                </li>
                <li>
                    <label for="username">Username:</label> 
                    <input type="text" name="username" value="" id="username" />
                </li>
                <li>
                    <label for="password">Password:</label>
                    <input type="password" name="password" value="" id="password" />
                </li>
            </ol>
            <input type="submit" name="submit" value="Submit" />
            <p>
                <a href="index.php">Cancel</a>
            </p>
        </fieldset>
    </form>
</div>
<?php
    include("Includes/footer.php");  
?>
