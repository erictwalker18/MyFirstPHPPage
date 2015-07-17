<?php
require_once ("database.php");
$startup_file = NULL;

function parse_csv($filename)
{
    echo "hiya";
    $GLOBALS['startup_file'] = fopen("$filename", "r") or die("Unable to open file!");
    $headers = fgetcsv($GLOBALS['startup_file']);
    $acceptedHeaders = array("project_name", "category_id", "project_desc", "active");
    //Error handling. We clean up the headers, removing new lines and such, and check if the headings are valid.
    for ($x=0; $x < sizeof($headers); $x++) {
        $headers[$x] = strtolower($headers[$x]);
        $headers[$x] = str_replace(array("\n", "\r"), '', $headers[$x]);
        if ($headers[$x] == "project name") 
        {
            $headers[$x] = "project_name";
        }
        if ($headers[$x] == "category")
        {
            $headers[$x] = "category_id"; 
        }
        if ($headers[$x] == "description") 
        {
            $headers[$x] = "project_desc";
        }
        if (!in_array($headers[$x], $acceptedHeaders))
            parse_error("Incorrect file type. You must use the default csv file provided by Eric Walker.");
    }
    //Goes through the lines of the file, forms an hours entry based on the content of the line.
    while (!feof($GLOBALS['startup_file'])){
        $currentData = fgetcsv($GLOBALS['startup_file']);
        $isNull = TRUE;
        //Checking for null values that are common at the end of the template file
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
        $clean = array();
        for ($x=0; $x < sizeof($headers); $x++) {
            $clean[$headers[$x]] = parseItem($headers[$x], $currentData[$x]);
        }
        echo "Trying to save a project: ";
        print_r($clean);
        echo "<br>";
        db_save_project( $clean );
    } 
    fclose($GLOBALS['startup_file']);
}

function parseItem($dataType, $data) 
{
    $clean = "";
    $data = str_replace("'", "''", $data);
    switch ($dataType) {
        case "category_id":
            $tmp = db_get_category_id($data);
            if ( $tmp['category_id'] == -1 )
            {
                parse_error("This category hasn't been added to the database: " . $data);
            }
            else
            {
                $clean .= $tmp['category_id'];
            }
            break;
        case "project_name":
        case "project_desc";
            $clean .= $data;
            break;
        case "active":
            if ($data == "Yes")
                $clean .= 1;
            else
                $clean .= 0;
            break;
        default:
            parse_error("This type of heading is not allowed: " . $dataType);
            break;
    }
    return $clean;
}

function parse_error($err_msg)
{
    fclose($GLOBALS['startup_file']);
    die($err_msg);
}
?>
