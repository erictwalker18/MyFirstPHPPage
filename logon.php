<?php
    require_once ("Includes/common.php");
    require_once ("Includes/simpledb-config.php"); 
    require_once ("Includes/connectDB.php");
    //include("Includes/header.php");
	print_header("");
	
    if (isset($_POST['submit']))
    {
        $username = $_POST['username'];
        $password = $_POST['password'];
        
        $query = "SELECT person_id, username FROM people WHERE username = '{$username}' AND password = SHA('{$password}') ";
        $res = $databaseConnection->query($query);
        if ($res->num_rows == 1)
        {
            $row = $res->fetch_assoc();
            $_SESSION['person_id'] = $row['person_id'];
            $_SESSION['username'] = $row['username'];
            set_user_session($_SESSION['person_id']);
            header ("Location: index.php");
        }
        else
        {
            echo "Username/password combination is incorrect.";
        }
    }
?>
    <h2>Log on</h2>
        <form action="logon.php" method="post">
            <fieldset>
            <legend>Log on</legend>
            <ol>
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
<?php
    include("Includes/footer.php");  
?>