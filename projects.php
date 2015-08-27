<?php

include_once( 'Includes/common.php' );
include_once( 'templateTester.php' );

$list = false;
$project = array();
$project['project_id'] = -1;
$project['project_name'] = '';
$project['project_desc'] = '';
$project['active'] = 1;
$project['category_id'] = 0;
$project['billable'] = 0;

if( isset( $_REQUEST['add'] ) )
{
	$project = $_REQUEST;
	unset( $project['project_id'] );
	unset( $project['add'] );
	
    //Error checking for mysql database- requires last_worked_id
    if (!array_key_exists('last_worked_id', $project))
    {
        $project['last_worked_id'] = db_get_first_id('people', 'person_id');
    }
	db_save_project( $project );

    //Update the template file
    $cat = db_get_category($project['category_id']);
    save_new_project($project['project_name'], $cat['category_name'], $project['project_desc']);

	redirect_by_url( "projects.php" );
}
else if( isset( $_REQUEST['delete'] ) )
{
	$project = db_get_project( $_REQUEST['project_id'] );
}
else if( isset( $_REQUEST['confirmdelete'] ) )
{
    //Update the template file
    $projToDelete = db_get_project($_REQUEST['project_id']);
    $projToDelete = $projToDelete['project_name'];
    delete_project($projToDelete);

	db_delete_project( $_REQUEST['project_id'] );

	$list = true;
}
else if( isset( $_REQUEST['project_name'] ) )
{
	$project = $_REQUEST;

    //Update the template file
    $oldname = db_get_project($_REQUEST['project_id']);
    $oldname = $oldname['project_name'];
    $cat = db_get_category($project['category_id']);
    update_existing_project($oldname, $project['project_name'], $cat['category_name'], $project['project_desc']);

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
	if ( isset($_REQUEST['active_only']) )
    {
	    $projects = db_get_project_group( 'active', 1 );
    }
    else
    {
        $projects = db_get_project_group( 'active', 1 );
        $projects = array_merge($projects, db_get_project_group( 'active', 0 ));
    }
    
	if ( !isset($_REQUEST['active_only']) )
    {
	?>
    <a href="projects.php?active_only">Active Projects Only</a>
    <?php
    }
    else
    {
    ?>
    <a href="projects.php">All Projects</a>
    <?php
    }
	?>
	<div class="DataRow">
	<div class="DataHeader" style="width: 300px">Project Name</div>
	<div class="DataHeader" style="width: 150px">Total Hours</div>
	<div class="DataHeader" style="width: 400px">Last Worked On</div>
	</div>
	<?php
	
	foreach( $projects as $id => $project )
	{
        if (isset($project['last_worked_id']))
        {
		    $person = db_get_person( $project['last_worked_id'] );
        }
        else
        {
            $person = array('person_id'=>'-1', 'person_firstname'=>'File', 'person_lastname'=>'Upload');
        }
		?>
		<div class="DataRow">
		<div class="DataValue" style="width: 300px"><a class="ProjectLink" href="projects.php?project_id=<?php echo $project['project_id'] ?>"><?php echo $project['project_name'] ?></a></div>
		<div class="DataValue" style="width: 150px"><?php echo $project['total_hours']; ?></div>
		<div class="DataValue" style="width: 400px"><?php if (isset($project['last_worked'])) {echo $project['last_worked']. " by ";} else { echo 'Created by';} ?>
			<a href="people.php?person_id=<?php echo $person['person_id'] ?>"><?php echo $person['person_firstname'] ?> <?php echo $person['person_lastname'] ?></a></div>
		</div>
		<?php
	}
    ?>
	<br>
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
        <tr>
		    <td style="vertical-align: top">Billable?</td>
            <td>
                <input name="billable" type="radio" value="1" <?php if($project['billable'] == 1){ echo 'checked';} ?>>Yes</input>
                <input name="billable" type="radio" value="0" <?php if(!($project['billable'] == 1)){ echo 'checked';} ?>>No</input>
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