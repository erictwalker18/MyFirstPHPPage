<?php

include_once( 'Includes/common.php' );

check_user_session();

if( !$GLOBALS['user']['loggedin'] )
{
	redirect_by_url( 'index.php' );
	exit();
}


  

$user = $GLOBALS['user'];
$pid = $user['person_id'];
$hid = -1;
$title = "Edit Hours";



if( isset( $_REQUEST['add'] ) )
{
	unset( $_REQUEST['hours_id'] );
	db_save_hours_check_duplicate( $_REQUEST );
	$hid = db_get_last_id( 'hours', 'hours_id' );
	$hours = db_get_hours( $hid );
	db_update_project( $hours['project_id'] );
	redirect_by_url( "time.php" );
}
else if( isset( $_REQUEST['delete'] ) )
{
	$hours = db_get_hours( $_REQUEST['hours_id'] );
	db_delete_hours( $_REQUEST['hours_id'] );
	db_update_project( $hours['project_id'] );
	redirect_by_url( "time.php" );
}
else if( isset( $_REQUEST['project_id'] ) )
{
	$hours = db_get_hours( $_REQUEST['hours_id'] );
	$old_project_id = $hours['project_id'];
	
	db_save_hours( $_REQUEST );
	$hid = $_REQUEST['hours_id'];
	$hours = db_get_hours( $hid );
	
	db_update_project( $hours['project_id'] );
	if( $hours['project_id'] != $old_project_id )
	{
		db_update_project( $old_project_id );
	}
    redirect_by_url( "time.php" );
}
else if( isset( $_REQUEST['hours_id'] ) )
{
	$hid = $_REQUEST['hours_id'];
	$hours = db_get_hours( $hid );
    
    //Check that it's a valid user trying to get to this hours log
    $user = get_user();
    if ( !($user['person_id']==$hours['person_id'] || is_admin()) )
    {
?>
        <script>
            alert("Sorry, you don't have access to this hours entry.");
        </script>
<?php
        redirect_by_url("index.php");
    }
}
else
{
	$title = "Log New Hours";
	$hid = -1;
	$hours = array();
	$hours['hours_id'] = $hid;
	$hours['person_id'] = $pid;
	$hours['project_id'] = $user['current_project_id'];
	if(isset($_GET['cal_date'])) $hours['date'] = date("Y-m-d", strtotime($_GET['cal_date']));
	else $hours['date'] = date( "Y-m-d", time() );
	$hours['hours'] = 0;
    $hours['task'] = '';
	$hours['comments'] = '';
    $hours['billable'] = 0;
    $hours['billable_hours'] = 0;
}

print_header( $title );

?>


<form action="details.php" method="post">
<input type="hidden" name="hours_id" value="<?php echo $hours['hours_id'] ?>" />
<input type="hidden" name="person_id" value="<?php echo $hours['person_id'] ?>" />

<table> <!--cellpadding="2" cellspacing="0"-->
	<tr>
		<td style ="width: 100px" class="TableDataCaption">Person</td>
		<td class="TableDataValue"><?php echo $user['person_firstname'] ?> <?php echo $user['person_lastname'] ?></td>
	</tr>
	<tr>
		<td class="TableDataCaption">Date</td>
		<td class="TableDataValue"><input name="date" value="<?php echo date("n/j/Y", strtotime( $hours['date'] ) ) ?>" /></td>
	</tr>
	<tr>
		<td class="TableDataCaption">Project</td>
		<td class="TableDataValue"><?php echo make_project_combo( $hours['project_id'] ) ?></td>        
	</td>
	<tr>
		<td class="TableDataCaption">Hours</td>
		<td class="TableDataValue"><input name="hours" value="<?php echo $hours['hours'] ?>" /></td>
	</tr>
    <tr>
		<td class="TableDataCaption">Billable?</td>
        <td>
            <input name="billable" type="radio" value="1" <?php if($hours['billable'] == 1){ echo 'checked';} ?>>Yes</input>
            <input name="billable" type="radio" value="0" <?php if(!($hours['billable'] == 1)){ echo 'checked';} ?>>No</input>
        </td>
	</tr>
    <tr>
		<td class="TableDataCaption">Task</td>
		<td class="TableDataValue"><input name="task" value=<?php echo $hours['task'] ?>></input></td>
	</tr>
	<tr>
		<td class="TableDataCaption">Details</td>
		<td class="TableDataValue"><textarea name="comments" rows="10" cols="50"><?php echo $hours['comments'] ?></textarea></td>
	</tr>
</table>

<?php
if( $hid < 0 )
{
	?><input type="submit" name="add" value="Add Hours" /><?php
}
else
{
	?>
	<input type="submit" value="Save Changes" /><br>
	<input type="submit" value="Delete Record" name="delete" />
	<?php
}
?>

</form>
<?php

print_footer();

?>