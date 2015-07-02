<?php

include_once( 'Includes/common.php' );
include_once( 'Functions/database.php' );

$start = strtotime( $_REQUEST['start'] );
$end = strtotime( $_REQUEST['end'] );

$sstart = date( "Y-m-d", $start );
$send = date( "Y-m-d", $end );

$sql = "SELECT * FROM hours INNER JOIN projects ON hours.project_id = projects.project_id INNER JOIN people ON hours.person_id = people.person_id INNER JOIN categories ON projects.category_id = categories.category_id WHERE hours.date >= '{$sstart}' AND hours.date <= '${send}' ORDER BY hours.date";
$res = $databaseConnection->query($sql);

if( !($res = $databaseConnection->query($sql)) )
{
	show_mysql_error($databaseConnection->error());
}

$file = fopen( "hours.csv", "w" );

$header = "person,date,project,category,hours,details\n";
fwrite( $file, $header );

$row = $res->fetch_assoc();
while( $row != null )
{
	$srow = "\"{$row['person_lastname']}, {$row['person_firstname']}\",\"{$row['date']}\",\"{$row['project_name']}\",\"{$row['category_name']}\",\"{$row['hours']}\",\"{$row['details']}\"\n";
	fwrite( $file, $srow );

	$row = $res->fetch_assoc();
}

header( "Content-type:application/octet-stream" );
header( "Content-disposition: attachment; filename=\"hours.csv\"" );
readfile( "hours.csv" );

?>