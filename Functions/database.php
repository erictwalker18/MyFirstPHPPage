<?php
require_once ("/Includes/simpledb-config.php");
require_once ("/Includes/connectDB.php");

function prep_DB_content ()
{
    global $databaseConnection;

    create_tables($databaseConnection);
}

//Data table creation (used only if necessary)
/*
    There are 4 tables: people, categories, projects, and hours. This was chosen because we don't want to have to excessively repeat data
    when we don't have to. See below for the things stored in each table.
*/
function create_tables($databaseConnection)
{
    $query_people = "CREATE TABLE IF NOT EXISTS people (person_id INT NOT NULL AUTO_INCREMENT, person_lastname VARCHAR(50), person_firstname VARCHAR(50), username VARCHAR(50), password CHAR(40), active BOOLEAN, admin BOOLEAN, PRIMARY KEY (person_id))";
    $databaseConnection->query($query_people);
    
    $query_categories = "CREATE TABLE IF NOT EXISTS categories (category_id INT NOT NULL AUTO_INCREMENT, category_name VARCHAR(50), category_desc VARCHAR(500), active BOOLEAN, PRIMARY KEY (category_id))";
    $databaseConnection->query($query_categories);

    $query_projects = "CREATE TABLE IF NOT EXISTS projects (project_id INT NOT NULL AUTO_INCREMENT, project_name VARCHAR(50), project_desc VARCHAR(500), active BOOLEAN, category_id INT,";
    $query_projects .=" last_worked DATE, last_worked_id INT,";
    //We make foreign keys actually reference something so we don't get a project with a category id that doesn't exist
    $query_projects .= " PRIMARY KEY (project_id), FOREIGN KEY (category_id) REFERENCES categories(category_id), FOREIGN KEY (last_worked_id) REFERENCES people(person_id))";
    $databaseConnection->query($query_projects);

    $query_hours = "CREATE TABLE IF NOT EXISTS hours (hours_id INT NOT NULL AUTO_INCREMENT, date DATE, hours FLOAT, project_id INT, person_id INT, comments VARCHAR(500), task VARCHAR(50), billable BOOLEAN, billable_hours FLOAT, ";
    $query_hours .= "PRIMARY KEY (hours_id), FOREIGN KEY (project_id) REFERENCES projects(project_id), FOREIGN KEY (person_id) REFERENCES people(person_id))";
    $databaseConnection->query($query_hours);
}

function db_save_person( $person )
{
	
	
	$person = prepare_person( $person );
	$sql = create_save_sql( 'people', $person, 'person_id' );
	$databaseConnection->query($sql);
}



function db_save_project( $project )
{
	
	
	$project = prepare_project( $project );
	$sql = create_save_sql( 'projects', $project, 'project_id' );
	$databaseConnection->query($sql);	
}



function db_save_category( $cat )
{
	
	
	$cat = prepare_category( $cat );
	$sql = create_save_sql( 'categories', $cat, 'category_id' );
	$databaseConnection->query($sql);
}



function db_save_hours( $hours )
{
	
	
	$hours = prepare_hours( $hours );
	$sql = create_save_sql( 'hours', $hours, 'hours_id' );
	$databaseConnection->query($sql);
	if( $err = mysqli_error() )
	{
		show_mysql_error( "$err: $sql" );
	}
}



function db_delete_person( $id )
{
	
	$sql = "DELETE FROM people WHERE person_id={$id}";
	$databaseConnection->query($sql);
}



function db_delete_project( $id )
{
	
	$sql = "DELETE FROM projects WHERE project_id={$id}";
	$databaseConnection->query($sql);
}



function db_delete_category( $id )
{
	
	$sql = "DELETE FROM categories WHERE category_id={$id}";
	$databaseConnection->query($sql);
}



function db_delete_hours( $id )
{
	
	$sql = "DELETE FROM hours WHERE hours_id={$id}";
	$databaseConnection->query($sql);
}



function db_get_last_id( $table, $idfield )
{
	
	$sql = "SELECT * FROM {$table} ORDER BY {$idfield} DESC";
	$res = $databaseConnection->query($sql);
	$row = mysqli_fetch_assoc( $res );
	mysqli_free_result( $res );
	return $row[$idfield];
}



function db_get_person( $id )
{
	
	$sql = "SELECT * FROM people WHERE person_id={$id}";
	$res = mysqli_query( $databaseConnection, $sql );
	
	if( $err = mysqli_error() )
	{
		$row = array();
		$row['person_id'] = -1;
		$row['person_firstname'] = 'error';
		$row['person_lastname'] = 'error';
	}
	else
	{
		$row = mysqli_fetch_assoc( $res );
		mysqli_free_result( $res );
	}
	
	return $row;
}



function db_get_project( $id )
{
	
	$sql = "SELECT * FROM projects WHERE project_id={$id}";
	$res = mysqli_query( $databaseConnection, $sql );
	
	if( $err = mysqli_error() )
	{
		$row = array();
		$row['project_id'] = -1;
		$row['project_name'] = 'error';
		$row['project_desc'] = $err;
	}
	else
	{
		$row = mysqli_fetch_assoc( $res );
		mysqli_free_result( $res );
	}

	return $row;
}



function db_get_category( $id )
{
	
	$sql = "SELECT * FROM categories WHERE category_id={$id}";
	$res = mysqli_query( $databaseConnection, $sql );
	
	if( $err = mysqli_error() )
	{
		$row = array();
		$row['category_id'] = -1;
		$row['category_name'] = 'error';
		$row['category_desc'] = $err;
	}
	else
	{
		$row = mysqli_fetch_assoc( $res );
		mysqli_free_result( $res );
	}
	
	return $row;
}



function db_get_people( $firstname = '', $lastname = '' )
{
	$people = array();
	
	
	if( $firstname == '' )
	{
		if( $lastname == '' )
		{
			$sql = "SELECT * FROM people ORDER BY person_lastname";
		}
		else
		{
			$sql = "SELECT * FROM people WHERE person_firstname='{$firstname}'";
		}
	}
	else if( $lastname == '' )
	{
		$sql = "SELECT * FROM people WHERE person_lastname='{$firstname}'";
	}
	else
	{
		$sql = "SELECT * FROM people WHERE person_firstname='{$firstname}' AND person_lastname='{$lastname}'";
	}
	
	$res = mysqli_query( $databaseConnection, $sql );
	while( $row = mysqli_fetch_assoc( $res ) )
	{
		$people[$row['person_id']] = $row;
	}
	
	return $people;
}



function db_get_projects()
{
	$projects = array();
	
	$sql = "SELECT * FROM projects ORDER BY project_name";
	$res = mysqli_query( $databaseConnection, $sql );
	while( $row = mysqli_fetch_assoc( $res ) )
	{
		$projects[$row['project_id']] = $row;
	}
	
	return $projects;
}



function db_get_projects_in_category( $category_id )
{
	$projects = array();
	
	$sql = "SELECT * FROM projects WHERE category_id={$category_id} ORDER BY project_name";
	$res = mysqli_query( $databaseConnection, $sql );
	while( $row = mysqli_fetch_assoc( $res ) )
	{
		$projects[$row['project_id']] = $row;
	}
	
	return $projects;
}



function db_get_categories()
{
	$cats = array();
	
	$sql = "SELECT * FROM categories ORDER BY category_name";
	$res = mysqli_query( $databaseConnection, $sql );
	while( $row = mysqli_fetch_assoc( $res ) )
	{
		$cats[$row['category_id']] = $row;
	}
	
	return $cats;
}

function db_get_project_id( $name )
{
	$sql = "SELECT * FROM projects WHERE project_name={$name}";
	$res = mysqli_query( $databaseConnection, $sql );
	if( $err = mysqli_error() )
	{
		$row = array();
		$row['project_id'] = -1;
		$row['project_name'] = 'error';
		$row['project_desc'] = $err;
	}
	else
	{
		$row = mysqli_fetch_assoc( $res );
		mysqli_free_result( $res );
	}
	return $row;
}

function db_get_category_id( $name )
{
	
	$sql = "SELECT * FROM categories WHERE category_name={$name}";
	$res = mysqli_query( $databaseConnection, $sql );
	
	if( $err = mysqli_error() )
	{
		$row = array();
		$row['category_id'] = -1;
		$row['category_name'] = 'error';
		$row['category_desc'] = $err;
	}
	else
	{
		$row = mysqli_fetch_assoc( $res );
		mysqli_free_result( $res );
	}

	return $row;
}


function prepare_project( $project )
{
	$clean = array();
	
	foreach( $project as $key => $val )
	{
		switch( $key )
		{
		case 'project_name':
		case 'project_desc':
			$clean[$key] = "'" . $val . "'";
			break;
			
		case 'last_worked':
		case 'total_hours':
		case 'project_id':
		case 'category_id':
			$clean[$key] = $val;
		}	
	}
	
	return $clean;
}



function prepare_category( $cat )
{
	$clean = array();
	
	foreach( $cat as $key => $val )
	{
		switch( $key )
		{
		case 'category_name':
		case 'category_desc':
			$clean[$key] = "'" . $val . "'";
			break;
			
		case 'category_id':
			$clean[$key] = $val;
		}
	}
	
	return $clean;
}



function prepare_person( $person )
{
	$clean = array();
	
	foreach( $person as $key => $val )
	{
		switch( $key )
		{
		case 'person_firstname':
		case 'person_lastname':
			$clean[$key] = "'" . $val . "'";
			break;
			
		case 'person_id':
		case 'current_project_id':
			$clean[$key] = $val;
		}
	}
	
	return $clean;
}


function prepare_hours( $hours )
{
	$clean = array();
	
	foreach( $hours as $key => $val )
	{   
		switch( $key )
		{
		case 'project_id':
		case 'person_id':
		case 'hours':
        case 'billable_hours':
			$clean[$key] = $val;
			break;
			
		case 'date':
			$clean[$key] = "'" . date( "Y-m-d", strtotime( $val ) ) . " 1:00:00'";
			break;
		
		case 'comments':
        case 'task':
        case 'billable':
			$clean[$key] = "'" . $val . "'";
		}
	}
	
	return $clean;
}



function create_save_sql( $table, $vals, $idfield )
{
	if( isset( $vals[$idfield] ) )
	{
		$sql = create_update_sql( $table, $vals, $idfield );
	}
	else
	{
		$sql = create_insert_sql( $table, $vals, $idfield );
	}
	
	return $sql;
}

function create_insert_sql( $table, $vals, $idfield )
{
	$first = true;
	$sql = "INSERT INTO {$table} (";
	
	foreach( $vals as $key => $val )
	{
		if( $key != $idfield )
		{
			if( $first )
			{
				$first = false;
				$sql .= $key;
			}
			else
			{
				$sql .= ", " . $key;
			}
		}
	}
	$first = true;
	$sql .= ") VALUES(";
	foreach( $vals as $key => $val )
	{
		if( $key != $idfield )
		{
			if( $first )
			{
				$first = false;
				$sql .= $val;
			}
			else
			{
				$sql .= ", " . $val;
			}
		}
	}
	$sql .= ")";
	return $sql;
}

function create_update_sql( $table, $vals, $idfield )
{
	$sql = "UPDATE {$table} SET ";
	$id = -1;
	$first = true;
	
	foreach( $vals as $key => $val )
	{
		if( $key == $idfield)
		{
			$id = $val;
		}
		else
		{
			if( $first )
			{
				$first = false;
				$sql .= $key . "=" . $val . " ";
			}
			else
			{
				$sql .= ", " . $key . "=" . $val . " ";
			}
		}
	}
	
	$sql .= "WHERE {$idfield}={$id}";
	
	return $sql;
}

function db_get_hours_for_person( $pid )
{
	
	$sql = "SELECT * FROM hours INNER JOIN projects ON hours.project_id = projects.project_id WHERE person_id={$pid} ORDER BY hours.date DESC";
	$res = mysqli_query( $databaseConnection, $sql );
	if( $err = mysqli_error() )
	{
		show_mysql_error( "$err: $sql" );
	}
	
	return $res;
}


function db_get_hours_in_range( $pid, $startdate, $enddate )
{
	
	$sdate = date( "Y-m-d", $startdate );
	$edate = date( "Y-m-d", $enddate );
	$sql = "SELECT * FROM hours INNER JOIN projects ON hours.project_id = projects.project_id WHERE person_id={$pid} AND hours.date>='{$sdate}' AND hours.date<='{$edate}' ORDER BY hours.date DESC";
	$res = mysqli_query( $databaseConnection, $sql );
	if( $err = mysqli_error() )
	{
		show_mysql_error( "$err: $sql" );
	}
	
	return $res;
}


function db_get_hours( $hours_id )
{
	
	$sql = "SELECT * FROM hours INNER JOIN projects ON hours.project_id = projects.project_id INNER JOIN people ON hours.person_id = people.person_id WHERE hours_id={$hours_id}";
	$res = mysqli_query( $databaseConnection, $sql );
	
	if( $err = mysqli_error() )
	{
		$row = array();
		show_mysql_error( "$err: $sql" );
	}
	else
	{
		$row = mysqli_fetch_assoc( $res );
	}
	
	return $row;
}



function db_get_category_array()
{
	
	$sql = "SELECT * FROM categories ORDER BY category_name ASC";
	$res = mysqli_query( $databaseConnection, $sql );
	
	$cats = array();
	
	if( $err = mysqli_error() )
	{
		show_mysql_error( "$err: $sql" );
	}
	else
	{
		while( $row = mysqli_fetch_assoc( $res ) )
		{
			$cats[$row['category_id']] = $row;
		}
		
		mysqli_free_result( $res );
	}
	
	return $cats;
}



function show_mysql_error( $err )
{
	echo "MySQL Error:<br>\n{$err}\n";
}



function db_update_project( $id )
{
	$total_hours = 0;
	
	$sql = "SELECT * FROM hours WHERE project_id={$id} ORDER BY date DESC";
	
	$res = $databaseConnection->query($sql );

	if( $err = mysqli_error() )
	{
		show_mysql_error( "$err: $sql" );
	}
	
	if( $row = mysqli_fetch_assoc( $res ) )
	{
		$total_hours += $row['hours'];
		$sql = "UPDATE projects SET last_worked='{$row['date']}', last_worked_id={$row['person_id']}, ";
	}
	else
	{
		$total_hours = 0;
		$sql = "UPDATE projects SET last_worked='0-0-0', last_worked_id=0, ";
	}
	
	while( $row = mysqli_fetch_assoc( $res ) )
	{
		$total_hours += $row['hours'];
	}
	$sql .= "total_hours={$total_hours} WHERE project_id={$id}";
	mysqli_query( $sql );
	if( $err = mysqli_error() )
	{
		show_mysql_error( "$err: $sql" );
	}
}

?>