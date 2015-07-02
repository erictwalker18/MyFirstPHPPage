<?php

include_once( 'Includes/common.php' );

$list = false;

$person = array();
$person['person_firstname'] = '';
$person['person_lastname'] = '';
$person['person_id'] = -1;
$person['current_project_id'] = 0;
$person['active'] = 0;

date_default_timezone_set('America/Denver');

if( isset( $_REQUEST['add'] ) )
{
	$person = $_REQUEST;
	unset( $person['person_id'] );
	unset( $person['add'] );
	db_save_person( $person );
	redirect_by_url( "people.php" );
}
else if( isset( $_REQUEST['delete'] ) )
{
	$person = db_get_person( $_REQUEST['person_id'] );
}
else if( isset( $_REQUEST['confirmdelete'] ) )
{
	db_delete_person( $_REQUEST['person_id'] );
	$list = true;
}
else if( isset( $_REQUEST['person_firstname'] ) )
{
	$person = $_REQUEST;
	db_save_person( $_REQUEST );

    redirect_by_url( "people.php" );
}
else if( isset( $_REQUEST['person_id'] ) )
{
	$person = db_get_person( $_REQUEST['person_id'] );
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
	$people = db_get_people( '', '' );

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
	?>
	<!-- this right </table> --> 
	<br>
	<a href="people.php?new">Add New Person</a>
	<?php
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
        if( $person['person_id'] == -1 )
	    {
	?>
        <tr><td>Username</td><td><input name="username" value="" /></td></tr>
        <tr><td>Password</td><td><input type="password" name="password" value="" /></td></tr>
    <?php
        }
	?>
    <tr>
		<td style="vertical-align: top">Active?</td>
		<td>
            <input name="active" type="radio" value="1" <?php if($person['active'] == 1){ echo 'checked';} ?>>Yes</input>
            <input name="active" type="radio" value="0" <?php if(!($person['active'] == 1)){ echo 'checked';} ?>>No</input>
        </td>
	</tr>
	<tr><td>Current Default Project</td><td><?php echo make_project_combo( $person['current_project_id'], 'current_project_id' ) ?></td></tr>
	</table>
	<br><br>
	<input type="hidden" name="person_id" value="<?php echo $person['person_id'] ?>" />

	<?php

	if( $person['person_id'] == -1 )
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