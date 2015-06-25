<?php
require_once ("database.php");
function parse_csv($filename)
{
    $hours = fopen("$filename", "r") or die("Unable to open file!");
    $firstline = fgets($hours);
    $headers = explode(',', $firstline);
    $acceptedHeaders = array("date","project","hours","task","billable","billable_hours","description","name","category", "firstname", "lastname");
    //Error handling. We clean up the headers, removing new lines and such, and check if the headings are valid.
    for ($x=0; $x < sizeof($headers); $x++) {
        $headers[$x] = strtolower($headers[$x]);
        $headers[$x] = str_replace(array("\n", "\r"), '', $headers[$x]);
        if ($headers[$x] == "name") {
            array_splice($headers, $x, 1, array("firstname", "lastname"));
        }
        if ($headers[$x] == "billable?") {
            $headers[$x] = "billable";
        }
        if ($headers[$x] == "billable hours") {
            $headers[$x] = "billable_hours";
        }
        if (!in_array($headers[$x], $acceptedHeaders))
            die("Incorrect file type. You must use the default csv file provided by your system administrator.");
    }
    //Goes through the lines of the file, forms an hours entry based on the content of the line.
    echo "Headers: ";
    print_r($headers);
    while (!feof($hours)){
        $currentLine = fgets($hours);
        if ($currentLine == "")
            break;
        $currentData = explode(",", $currentLine);
        $valuesToAdd;
        for ($x=0; $x < sizeof($headers); $x++) {
            $valuesToAdd[$headers[$x]] = parseItem($headers[$x], $currentData[$x]);
        }
        print_r($valuesToAdd);
        echo "<br>";
        echo create_insert_sql("hours", $valuesToAdd, $headers);
    }
    fclose($hours);
}
function parseItem($dataType, $data) {
    $strToReturn = "";
    switch ($dataType) {
        case "date":
        $fullData = explode('/',$data);
        $date = date_create();
        date_date_set($date, $fullData[2], $fullData[0], $fullData[1]);
        $strToReturn .= date_format($date, "Y/m/d");
        break;
        case "project":
        case "hours":
        case "task";
        case "billable":
        case "billable_hours":
        case "description":
        case "firstname":
        case "lastname":
        case "category":
        $strToReturn .= $data;
        default:
        die("This type of heading is not allowed: " . $dataType);
        break;
    }
    return $strToReturn;
}
?>
