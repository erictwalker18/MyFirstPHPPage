<?php

include_once( 'Includes/common.php' );
include_once( 'templateTester.php' );

$list = false;

$person = array();
$person['person_firstname'] = '';
$person['person_lastname'] = '';
$person['username'] = '';
$person['person_id'] = -1;
$person['current_project_id'] = 0;
$person['active'] = 0;
$person['admin'] = 0;

if (logged_on())
{
    $currentUser = get_user();
}
else
{
    $currentUser = $person;
}

date_default_timezone_set('America/Denver');

if( isset( $_REQUEST['add'] ) )
{
	$person = $_REQUEST;
	unset( $person['person_id'] );
	unset( $person['add'] );
	db_save_person( $person );
	
	$person['person_id'] = db_get_last_id('people', 'person_id');
    //Update the template file
    save_new_person( $person['person_lastname'] . ', ' . $person['person_firstname'] );

	redirect_by_url( "people.php" );
}
else if( isset( $_REQUEST['delete'] ) )
{
	$person = db_get_person( $_REQUEST['person_id'] );
}
else if( isset( $_REQUEST['confirmdelete'] ) )
{
    //Update the template file
    $personToDelete = db_get_person($_REQUEST['person_id']);
    $person = $personToDelete['person_lastname']. ', '. $personToDelete['person_firstname'];
    delete_person($person);

	db_delete_person( $_REQUEST['person_id'] );
	$list = true;
}
else if( isset( $_REQUEST['person_firstname'] ) )
{
	$person = $_REQUEST;

    //Update the template file
    $oldperson = db_get_person($_REQUEST['person_id']);
    $oldname = $oldperson['person_lastname'] . ', ' . $oldperson['person_firstname'];
    update_existing_person($oldname, $person['person_lastname']. ', ' .$person['person_firstname']);

	db_save_person( $_REQUEST );

    redirect_by_url( "people.php" );
}
else if( isset( $_REQUEST['person_id'] ) )
{
	$person = db_get_person( $_REQUEST['person_id'] );
    if ( !(is_admin() || $person['person_id'] == $user['person_id']) )
    {
        redirect_by_url( "people.php");
    }
}
else if( isset( $_REQUEST['new'] ) )
{
}
else
{
	$list = true;
}

print_header( 'Manage People' );

if( $list )
{
    if ( isset($_REQUEST['active_only']) )
    {
	    $people = db_get_people_group( 'active', 1 );
    }
    else
    {
        $people = db_get_people_group( 'active', 1 );
        $people =  $people + db_get_people_group( 'active', 0 );
    }
    if ( !isset($_REQUEST['active_only']) )
    {
	?>
    <a href="people.php?active_only">Active People Only</a>
    <?php
    }
    else
    {
    ?>
    <a href="people.php">All People</a>
    <?php
    }
    ?>
	<div class="DataRow">
	<div class="DataValue" style="width: 200px;">Person</div>
	<div class="DataValue" style="width: 200px;">Last Reported</div>
	</div>
<?php
	foreach( $people as $id => $person )
	{
		$sql = "SELECT * FROM hours WHERE person_id={$id} ORDER BY hours.date DESC";
		$res = $databaseConnection->query($sql);
		$hours = $res->fetch_assoc();
		
?>
		<div class="DataRow">
		<div class="DataValue" style="width: 200px;"><a class="PersonLink" href="people.php?person_id=<?php echo $person['person_id'] ?>"><?php echo $person['person_lastname'] ?>,
			<?php echo $person['person_firstname'] ?></a></div>
		<div class="DataValue" style="width: 200px;"></div>
		<?php
		if( $hours )
		{
			echo date( "n/d/Y", strtotime( $hours['date'] ) );
		}
		?>
		</div>
		<?php
	}
    if ( is_admin() )
    {
    ?>
    <br>
	<a href="people.php?new">Add New Person</a>
	<?php
    }
}
else if( isset( $_REQUEST['delete'] ) )
{
	?>
	Are you sure you want to delete <b><?php echo $person['person_firstname'], " ", $person['person_lastname'] ?></b>?<br>
	<form action="people.php" method="post">
	<input type="hidden" name="person_id" value="<?php echo $person['person_id'] ?>" />
	<input type="submit" value="Confirm Delete" name="confirmdelete" />
	</form>
	<?php
}
else
{
	?>
	<form action="people.php" method="post">
	<table>
	<tr><td>Last Name</td><td><input name="person_lastname" value="<?php echo $person['person_lastname'] ?>" /></td></tr>
	<tr><td>First Name</td><td><input name="person_firstname" value="<?php echo $person['person_firstname'] ?>" /></td></tr>
    <?php
        if( is_admin() || $currentUser['person_id'] == $person['person_id'])
	    {
	?>
        <tr><td>Username</td><td><input name="username" value="<?php echo $person['username'] ?>" /></td></tr>
        <tr><td>Password</td><td><input type="password" name="password" value="" /></td></tr>
    <?php
        }
	?>
    <?php
        if( is_admin() )
	    {
	?>
        <tr>
	        <td style="vertical-align: top">Admin?</td>
	        <td>
                <input name="admin" type="radio" value="1" <?php if($person['admin'] == 1){ echo 'checked';} ?>>Yes</input>
                <input name="admin" type="radio" value="0" <?php if(!($person['admin'] == 1)){ echo 'checked';} ?>>No</input>
            </td>
	    </tr>
        <tr>
		    <td style="vertical-align: top">Active?</td>
		    <td>
                <input name="active" type="radio" value="1" <?php if($person['active'] == 1){ echo 'checked';} ?>>Yes</input>
                <input name="active" type="radio" value="0" <?php if(!($person['active'] == 1)){ echo 'checked';} ?>>No</input>
            </td>
	    </tr>
    <?php
        }
	?>
	<tr><td>Current Default Project</td><td><?php echo make_project_combo( $person['current_project_id'], 'current_project_id' ) ?></td></tr>
	</table>
	<br><br>
	<input type="hidden" name="person_id" value="<?php echo $person['person_id'] ?>" />

	<?php

	if( $person['person_id'] == -1 && is_admin() )
	{
	?>
		<input name="add" type="submit" value="Add New Person" />
	<?php
	}
	else
	{
	?>
		<input type="submit" value="Save Changes" /><br>
		<input type="submit" value="Delete Person" name="delete" />
	<?php
	}

	?>

	</form>
<?php
}

?>
<?php

print_footer();

?>