<?php

include_once( "/Functions/database.php" );
include_once( "connectDB.php" );


$GLOBALS['cookie_name'] = "timecard_v2";
$GLOBALS['user'] = array();
$GLOBALS['user']['loggedin'] = false;

function check_user_session()
{
	
	if( isset( $_COOKIE[$GLOBALS['cookie_name']] ) )
	{
		$id = $_COOKIE[$GLOBALS['cookie_name']];
		$GLOBALS['user'] = db_get_person( $id );
		$GLOBALS['user']['loggedin'] = true;
	}
}



function set_user_session( $user )
{
	setcookie( $GLOBALS['cookie_name'], $user );
}


function redirect_by_url( $redir_url )
{
    if ( stristr( $_SERVER['SERVER_SOFTWARE'], "Microsoft-IIS" ) ) {
        // the ugly IIS-hack to avoid crashing IIS
        print "<html><head>\n<title>Redirecting ...</title>\n";
        print "<meta http-equiv=\"refresh\" content=\"0; URL=$redir_url\">";
        print "</head>\n";
        print "<body><a href=\"$redir_url\">Redirecting ...</a></body>\n";
        print "</html>";
    } else {
        // our standard-way
        header( "Location: $redir_url" );
    } 
}




function print_header( $title )
{
	$GLOBALS['page_title'] = $title;
	include( 'header.php' );
}


function print_footer()
{
	include( 'footer.php' );
}



function make_category_combo( $category_id ) 
{
    global $databaseConnection;			

	$combo_name = 'category_id';
	$html = "<select name=\"category_id\">\n";
	
	$sql = "SELECT * FROM categories ORDER BY category_name ASC";
	$res = $databaseConnection->query($sql) or die ($databaseConnection->error());
	
	while( $row = $res->fetch_assoc() )
	{	
		if( $row['category_id'] == $category_id )
		{
			$html .= "<option selected value=\"{$row['category_id']}\">{$row['category_name']}</option>\n";
		}
		else
		{
			$html .= "<option value=\"{$row['category_id']}\">{$row['category_name']}</option>\n";
		}
	}
	
	$html .= "</select>\n";
	
	return $html;
}



function make_project_combo( $project_id, $combo_name = 'project_id' )
{
	$html = "<select name=\"{$combo_name}\">\n";
	
	global $databaseConnection;
	$sql = "SELECT * FROM projects ORDER BY project_name ASC";
	$res = $databaseConnection->query($sql);
	
	while( $row = $res->fetch_assoc() )
	{
		if( $row['project_id'] == $project_id )
		{
			$html .= "<option selected value=\"{$row['project_id']}\">{$row['project_name']}</option>\n";
		}
		else
		{
			$html .= "<option value=\"{$row['project_id']}\">{$row['project_name']}</option>\n";
		}
	}
	
	
	$html .= "</select>\n";
	
	return $html;
}



function make_project_list( $include_all_option = true, $rows = 10, $list_name = 'project_id' )
{
	$html = "<select multiple size=\"{$rows}\" name=\"{$list_name}\">\n";
	
	global $databaseConnection;
	$sql = "SELECT * FROM projects ORDER BY project_name ASC";
	$res = $databaseConnection->query($sql);
	
	if( $include_all_option )
	{
		$html .= "<option selected value=\"all\">All Projects</option>\n";
	}
	
	while( $row = $res->fetch_assoc() )
	{
		$html .= "<option value=\"{$row['project_id']}\">{$row['project_name']}</option>\n";
	}
	
	$html .= "</select>\n";
	
	return $html;
}



function make_people_list( $include_all_option = true, $rows = 10 )
{
	$html = "<select multiple size=\"{$rows}\" name=\"person_id\">\n";
	
	global $databaseConnection;
	$sql = "SELECT * FROM people ORDER BY person_lastname, person_firstname ASC";
	$res = mysql_query( $sql, $conn );
	
	if( $include_all_option )
	{
		$html .= "<option selected value=\"all\">All People</option>\n";
	}
	
	while( $row = $res->fetch_assoc() )
	{
		$html .= "<option value=\"{$row['person_id']}\">{$row['person_lastname']}, {$row['person_firstname']}</option>\n";
	}
	
	$html .= "</select>\n";
	
	return $html;
}



function make_month_combo( $month )
{
	$html = "<select name=\"month\">\n";
	
	if( $month == -1 )
	{
		$today = getdate();
		$month = $today['mon'];
	}
	
	for( $i = 1; $i <= 12; $i++ )
	{
		$d = date( "F", mktime( 0, 0, 0, $i, 1 ) );
		
		if( $month == $i )
		{
			$html .= "<option selected value=\"{$i}\">{$d}</option>\n";
		}
		else
		{
			$html .= "<option value=\"{$i}\">{$d}</option>\n";
		}
	}
	
	$html .= "</select>\n";
	
	return $html;
}


function make_month_year_combo( $startmonth, $startyear, $endmonth, $endyear, $selmonth )
{
	$html = "<select name=\"month\">\n";
	$i = 0;
	
	for( $year = $endyear; $year >= $startyear; $year-- )
	{
		if( $year == $endyear )
		{
			$end = $endmonth;
		}
		else
		{
			$end = 12;
		}
		
		if( $year == $startyear )
		{
			$start = $startmonth;
		}
		else
		{
			$start = 1;
		}
		
		for( $month = $end; $month >= $start; $month--)
		{
			$t = mktime( 1, 1, 1, $month, 1, $year );
			$text = date( "F Y", $t );
			
			if( $i == $selmonth )
			{
				$html .= "<option selected value=\"{$i}\">{$text}</option>\n";
			}
			else
			{
				$html .= "<option value=\"{$i}\">{$text}</option>\n";
			}
			$i--;
		}
	}
	
	$html .= "</select>\n";
	return $html;
}

?>