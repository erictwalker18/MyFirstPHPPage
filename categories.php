<?php

include_once( 'Includes/common.php' );
include_once( 'templateTester.php' );

$list = false;
$cat = array();
$cat['category_id'] = -1;
$cat['category_name'] = '';
$cat['category_desc'] = '';
$cat['active'] = 0;

if( isset( $_REQUEST['add'] ) )
{
	$cat = $_REQUEST;
	unset( $cat['category_id'] );
	unset( $cat['add'] );
	
	db_save_category( $cat );
	$cat['category_id'] = db_get_last_id('categories', 'category_id');
    //Update the template file
    save_new_category($cat['category_name'], $cat['category_desc']);

	redirect_by_url( "categories.php" );
}
else if( isset( $_REQUEST['delete'] ) )
{
	$cat = db_get_category( $_REQUEST['category_id'] );
}
else if( isset( $_REQUEST['confirmdelete'] ) )
{
    //Update the template file
    $catToDelete = db_get_category($_REQUEST['category_id']);
    $catToDelete = $catToDelete['category_name'];
    delete_category($catToDelete);

	db_delete_category( $_REQUEST['category_id'] );
	$list = true;
}
else if( isset( $_REQUEST['category_name'] ) )
{
	$cat = $_REQUEST;

    //Update the template file
    $oldname = db_get_category($_REQUEST['category_id']);
    $oldname = $oldname['category_name'];
    update_existing_category($oldname, $cat['category_name'], $cat['category_desc']);

	db_save_category( $_REQUEST );

    redirect_by_url( "categories.php" );
}
else if( isset( $_REQUEST['category_id'] ) )
{
	$cat = db_get_category( $_REQUEST['category_id'] );
}
else if( isset( $_REQUEST['new'] ) )
{
}
else
{
	$list = true;
}

print_header( 'Manage Categories' );

if( $list )
{
	if ( isset($_REQUEST['active_only']) )
    {
	    $cats = db_get_category_group( 'active', 1 );
    }
    else
    {
        $cats = db_get_category_group( 'active', 1 );
        $cats = array_merge($cats, db_get_category_group( 'active', 0 ));
    }

	if ( !isset($_REQUEST['active_only']) )
    {
	?>
        <a href="categories.php?active_only">Active Categories Only</a>
    <?php
    }
    else
    {
    ?>
        <a href="categories.php">All Categories</a>
    <?php
    }
    ?>
	<div class="DataRow">
	<div class="DataHeader" style="width: 200px">Category Name</div>
	</div>
	<?php
	
	foreach( $cats as $id => $cat )
	{
		?>
		<div class="DataRow">
		<div class="DataValue"><a class="ProjectLink" href="categories.php?category_id=<?php echo $cat['category_id'] ?>"><?php echo $cat['category_name'] ?></a></div>
		</div>
		<?php
	}
    if ( is_admin() )
    {
    ?>
	<br>
    <br>
	<a href="categories.php?new">Add New Category</a>
	<?php
    }
}
else if( isset( $_REQUEST['delete'] ) )
{
	?>
	Are you sure you want to delete <b><?php echo $cat['category_name'] ?></b>?<br>
	<form action="categories.php" method="post">
	<input type="hidden" name="category_id" value="<?php echo $cat['category_id'] ?>" />
	<input type="submit" value="Confirm Delete" name="confirmdelete" />
	</form>
	<?php
}
else
{
	?>
	<form action="categories.php" method="post">
	<input type="hidden" name="category_id" value="<?php echo $cat['category_id'] ?>" />
	<table border="0">
		<tr>
			<td>Category Name</td>
			<td><input size="45" name="category_name" value="<?php echo $cat['category_name'] ?>" maxlength="45" /></td>
		</tr>
		<tr>
			<td style="vertical-align: top">Category Description</td>
			<td><textarea name="category_desc" rows="10" cols="50"><?php echo $cat['category_desc'] ?></textarea></td>
		</tr>
        <tr>
			<td style="vertical-align: top">Active?</td>
			<td>
                <input name="active" type="radio" value="1" <?php if($cat['active'] == 1){ echo 'checked';} ?>>Yes</input>
                <input name="active" type="radio" value="0" <?php if(!($cat['active'] == 1)){ echo 'checked';} ?>>No</input>
            </td>
		</tr>
	</table>
	<br>
	
	<?php
	if( $cat['category_id'] == -1 )
	{
		?>
		<input name="add" type="submit" value="Add New Category" />
		<?php
	}
	else
	{
		?>
		<input type="submit" value="Save Changes" /><br>
		<input type="submit" value="Delete Category" name="delete" />
		<p>
		Projects in this Category:<br>
		<?php
		$projects = db_get_projects_in_category( $cat['category_id'] );
		foreach( $projects as $id => $project )
		{
			echo "<a href=\"projects.php?project_id={$project['project_id']}\">{$project['project_name']}</a><br />\n";
		}
		?>
		</p>
		<?php
	}
	?>
	
	</form>
	<?php
}

print_footer();
?>