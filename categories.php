<?php

include_once( 'Includes/common.php' );

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
	redirect_by_url( "categories.php" );
}
else if( isset( $_REQUEST['delete'] ) )
{
	$cat = db_get_category( $_REQUEST['category_id'] );
}
else if( isset( $_REQUEST['confirmdelete'] ) )
{
	db_delete_category( $_REQUEST['category_id'] );
	$list = true;
}
else if( isset( $_REQUEST['category_name'] ) )
{
	$cat = $_REQUEST;
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
	$cats = db_get_categories();
	
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
	
	?>
	<a href="categories.php?new">Add New Category</a>
	<?php
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
			<td>Categpry Name</td>
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