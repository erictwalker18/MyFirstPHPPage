<?php
require_once ("database.php");
require_once ("/Includes/common.php");
date_default_timezone_set('America/Denver');
$hours = NULL;
function parse_csv($filename)
{
    $GLOBALS['hours'] = fopen("$filename", "r") or die("Unable to open file!");
    $headers = fgetcsv($GLOBALS['hours']);
    $acceptedHeaders = array("date","project_id","hours","task","billable","comments","category","person_id");
    //Error handling. We clean up the headers, removing new lines and such, and check if the headings are valid.
    for ($x=0; $x < sizeof($headers); $x++) {
        $headers[$x] = strtolower($headers[$x]);
        $headers[$x] = str_replace(array("\n", "\r"), '', $headers[$x]);
        if ($headers[$x] == "name") 
        {
            $headers[$x] = "person_id";
        }
        if ($headers[$x] == "project")
        {
            $headers[$x] = "project_id"; 
        }
        if ($headers[$x] == "billable?") 
        {
            $headers[$x] = "billable";
        }
        if ($headers[$x] == "description") 
        {
            $headers[$x] = "comments";
        }
        if (!in_array($headers[$x], $acceptedHeaders))
            parse_error("Incorrect file type. You must use the default csv file provided by your system administrator.", $filename);
    }
    //Goes through the lines of the file, forms an hours entry based on the content of the line.
    while (!feof($GLOBALS['hours'])){
        $currentData = fgetcsv($GLOBALS['hours']);
        $isNull = TRUE;
        //Checking for null values that are common at the end of the template file
		if ($currentData == null)
		{
			continue;
		}
        foreach ($currentData as $key => $val)
        {
            if( !($val == '') )
            {
                $isNull = FALSE;
                break;
            }
        }
        if ( $currentData == NULL || $isNull )
        {
            break;
        }
        //Go through the row and parse each item
        $clean=parse_row($headers, $currentData, $filename);
        //If there was an error, we're going to skip that row
        if (sizeof($clean)<1) 
        {
            continue;
        }

        if ( !array_key_exists('person_id', $clean) )
        {
            //Pull the person_id from the current user
            $person = get_user();
            $clean['person_id'] = $person['person_id'];
        }
        db_save_hours_check_duplicate( $clean );
		//db_save_hours( $clean );
	    $hid = db_get_last_id( 'hours', 'hours_id' );
	    $last_hours = db_get_hours( $hid );
	    db_update_project( $last_hours['project_id'] );
        $project = db_get_project($clean['project_id']);
        echo "Added ".$clean['hours']." hours on ".$clean['date']." for the project ".$project['project_name'].".<br>";
    } 
    fclose($GLOBALS['hours']);
}

function parse_row($headers, $currentData, $filename) 
{   
    //Go through the row and parse each item
    $clean = array();
    for ($x=0; $x < sizeof($headers); $x++) 
    {
        if ($headers[$x] == "person_id")
        {
            //Check for a name given, if there is one, then use that
            if ($currentData[$x] != '')
            {
                $clean[$headers[$x]] = parseItem($headers[$x], $currentData[$x], $filename);
                
                //If we couldn't find that person, they haven't been added, so we're skipping the entry.
                if ($clean[$headers[$x]]=="")
                {
                    $clean = array();
                    return $clean;
                }
            }
            //Otherwise, pull the name from the session
            else
            {
                //Pull the person_id from the current user
                $person = get_user();
                $clean['person_id'] = $person['person_id'];
            }
        }
        //Default value for billable is from the project
        else if ($headers[$x] == "project_id")
        {
            $clean[$headers[$x]] = parseItem($headers[$x], $currentData[$x], $filename);
            //If we couldn't find that project, it hasn't been added, so we're skipping this entry.
            if ($clean[$headers[$x]]=="") 
            {
                $clean = array();
                return $clean;
            }
            $proj = db_get_project($clean['project_id']);
            if (!array_key_exists('billable', $clean) and array_key_exists('billable', $proj))
            {
                $clean['billable'] = $proj['billable'];
            }
        }
        elseif ($headers[$x] != "category")
        {
            //Also making the billable default to the value from the project
            if ($headers[$x] == 'billable' and array_key_exists('billable', $clean) and $clean['billable'] == 1)
            {
                    if ($currentData[$x] == 'No')
                    {
                        $clean[$headers[$x]] = 0;
                    }
            }
            else
            {
                $clean[$headers[$x]] = parseItem($headers[$x], $currentData[$x], $filename);
            }
        }
    }
    return $clean;
}

function parseItem($dataType, $data, $filename) 
{
    $clean = "";
    $data = str_replace("'", "''", $data);
    switch ($dataType) 
    {
        case "date":
            //$clean[$key] = "'" . date( "Y-m-d", strtotime( $val ) ) . " 1:00:00'";?
            $fullData = explode('/',$data);
            $date = date_create();
            date_date_set($date, $fullData[2], $fullData[0], $fullData[1]);
            $clean .= date_format($date, "Y-m-d");
            break;
        case "project_id":
            $tmp = db_get_project_id($data);
            if ( $tmp['project_id'] == -1 )
            {
                echo "This project hasn't been added to the database: " . $data.". Skipping all entries with this project.<br>";
            }
            else
            {
                $clean .= $tmp['project_id'];
            }
            break;
        case "person_id":
            list($lastname, $firstname) = explode(', ', $data);
            $clean = db_get_person_id($firstname, $lastname);
            if ( $clean == NULL )
            {
                echo "This person has not been added to the database: " . $firstname . " " . $lastname.". Skipping all entries with this person.<br>";
            }
            break;
        case "hours":
        case "task":
        case "comments":
            $clean .= $data;
            break;
        case "billable":
            if ($data == "Yes")
                $clean .= 1;
            else
                $clean .= 0;
            break;
        case "category":
            //do nothing, we already have this information in the database, supposedly
            break;
        default:
            parse_error("This type of heading is not allowed: " . $dataType, $filename);
            break;
    }
    return $clean;
}

function parse_error($err_msg, $filename)
{
    fclose($GLOBALS['hours']);
    unlink($filename);
    die($err_msg);
}
?>