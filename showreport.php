<?php

include_once( 'Includes/common.php' );

print_header( 'Report' );

$allprojects = false;
$projects = array();

foreach( $_REQUEST as $key => $val )
{
	switch( $key )
	{
	case 'project_id':
		if( $val == 'all' )
		{
			$allprojects = true;
		}
		else
		{
			$projects[] = $val;
		}
	}
}

if( count( $projects ) == 0)
{
	$allprojects = true;
}

print_footer();

?>