<?php

include_once( 'Includes/common.php' );

$list = false;
$project = array();
$project['project_id'] = -1;
$project['project_name'] = '';
$project['project_desc'] = '';
$project['active'] = 1;
$project['category_id'] = 0;

if( isset( $_REQUEST['add'] ) )
{
	$project = $_REQUEST;
	unset( $project['project_id'] );
	unset( $project['add'] );
	
    //Error checking for mysql database- requires last_worked_id and total_hours
    if (!array_key_exists('last_worked_id', $project))
    {
        $project['last_worked_id'] = db_get_first_id('people', 'person_id');
    }
	db_save_project( $project );

	redirect_by_url( "projects.php" );
}
else if( isset( $_REQUEST['delete'] ) )
{
	$project = db_get_project( $_REQUEST['project_id'] );
}
else if( isset( $_REQUEST['confirmdelete'] ) )
{
	db_delete_project( $_REQUEST['project_id'] );
	$list = true;
}
else if( isset( $_REQUEST['project_name'] ) )
{
	$project = $_REQUEST;
	db_save_project( $_REQUEST );

	redirect_by_url( "projects.php" );
}
else if( isset( $_REQUEST['project_id'] ) )
{
	$project = db_get_project( $_REQUEST['project_id'] );
}
else if( isset( $_REQUEST['new'] ) )
{
}
else
{
	$list = true;
}

print_header( 'Manage Projects' );


if( $list )
{
	$projects = db_get_projects();
	?>
	<div class="DataRow">
	<div class="DataHeader" style="width: 200px">Project Name</div>
	<div class="DataHeader" style="width: 150px">Total Hours</div>
	<div class="DataHeader" style="width: 400px">Last Worked On</div>
	</div>
	<?php
	
	foreach( $projects as $id => $project )
	{
		$person = db_get_person( $project['last_worked_id'] );
		?>
		<div class="DataRow">
		<div class="DataValue" style="width: 200px"><a class="ProjectLink" href="projects.php?project_id=<?php echo $project['project_id'] ?>"><?php echo $project['project_name'] ?></a></div>
		<div class="DataValue" style="width: 150px"><?php echo $project['total_hours'] ?></div>
		<div class="DataValue" style="width: 400px"><?php echo $project['last_worked'] ?> by 
			<a href="people.php?person_id=<?php echo $person['person_id'] ?>"><?php echo $person['person_firstname'] ?> <?php echo $person['person_lastname'] ?></a></div>
		</div>
		<?php
	}
	
	?>
	<a href="projects.php?new">Add New Project</a>
	<?php
}
else if( isset( $_REQUEST['delete'] ) )
{
	?>
	Are you sure you want to delete <b><?php echo $project['project_name'] ?></b>?<br>
	<form action="projects.php" method="post">
	<input type="hidden" name="project_id" value="<?php echo $project['project_id'] ?>" />
	<input type="submit" value="Confirm Delete" name="confirmdelete" />
	</form>
	<?php
}
else
{
	?>
	<form action="projects.php" method="post">
	<input type="hidden" name="project_id" value="<?php echo $project['project_id'] ?>" />
	<table border="0">
		<tr>
			<td>Project Name</td>
			<td><input size="45" name="project_name" value="<?php echo $project['project_name'] ?>" maxlength="45" /></td>
		</tr>
		<tr>
			<td>Category</td>
			<td><?php echo make_category_combo( $project['category_id'] ) ?></td>
		</tr>
		<tr>
			<td style="vertical-align: top">Project Description</td>
			<td><textarea name="project_desc" rows="10" cols="50"><?php echo $project['project_desc'] ?></textarea></td>
		</tr>
        <tr>
			<td style="vertical-align: top">Active?</td>
			<td>
                <input name="active" type="radio" value="1" <?php if($project['active'] == 1){ echo 'checked';} ?>>Yes</input>
                <input name="active" type="radio" value="0" <?php if(!($project['active'] == 1)){ echo 'checked';} ?>>No</input>
            </td>
		</tr>
	</table>
	<br>
	
	<?php
	if( $project['project_id'] == -1 )
	{
		?>
		<input name="add" type="submit" value="Add New Project" />
		<?php
	}
	else
	{
		?>
		<input type="submit" value="Save Changes" /><br>
		<input type="submit" value="Delete Project" name="delete" />
		<?php
	}
	?>
	
	</form>
    
	<?php
} ?>

<?php
print_footer();
?>