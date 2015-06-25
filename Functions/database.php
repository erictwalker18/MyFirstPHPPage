<?php
require_once ("/Includes/simpledb-config.php");

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
    $query_people = "CREATE TABLE IF NOT EXISTS people (id INT NOT NULL AUTO_INCREMENT, last_name VARCHAR(50), first_name VARCHAR(50), username VARCHAR(50), password CHAR(40), active BOOLEAN, admin BOOLEAN, PRIMARY KEY (id))";
    $databaseConnection->query($query_people);
    
    $query_categories = "CREATE TABLE IF NOT EXISTS categories (id INT NOT NULL AUTO_INCREMENT, category_name VARCHAR(50), category_desc VARCHAR(500), active BOOLEAN, PRIMARY KEY (id))";
    $databaseConnection->query($query_categories);

    $query_projects = "CREATE TABLE IF NOT EXISTS projects (id INT NOT NULL AUTO_INCREMENT, project_name VARCHAR(50), project_desc VARCHAR(500), active BOOLEAN, category_id INT,";
    $query_projects .=" last_worked DATE, last_worked_id INT,";
    //We make foreign keys actually reference something so we don't get a project with a category id that doesn't exist
    $query_projects .= " PRIMARY KEY (id), FOREIGN KEY (category_id) REFERENCES categories(id), FOREIGN KEY (last_worked_id) REFERENCES people(id))";
    $databaseConnection->query($query_projects);

    $query_hours = "CREATE TABLE IF NOT EXISTS hours (id INT NOT NULL AUTO_INCREMENT, date DATE, hours FLOAT, project_id INT, person_id INT, comments VARCHAR(500), task VARCHAR(50), billable BOOLEAN, billable_hours FLOAT, ";
    $query_hours .= "PRIMARY KEY (id), FOREIGN KEY (project_id) REFERENCES projects(id), FOREIGN KEY (person_id) REFERENCES people(id))";
    $databaseConnection->query($query_hours);
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
?>