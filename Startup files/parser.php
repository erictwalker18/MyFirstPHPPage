<?php
require_once ("database.php");
function parse_csv($filename)
{
    $hours = fopen("$filename", "r") or die("Unable to open file!");
    $firstline = fgets($hours);
    $headers = explode(',', $firstline);
    $acceptedHeaders = array("date","project_id","hours","task","billable","billable_hours","comments","category", "person_id");

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
        if ($headers[$x] == "billable hours") 
        {
            $headers[$x] = "billable_hours";
        }
        if ($headers[$x] == "description") 
        {
            $headers[$x] = "comments";
        }
        if (!in_array($headers[$x], $acceptedHeaders))
            die("Incorrect file type. You must use the default csv file provided by your system administrator.");
    }
    //Goes through the lines of the file, forms an hours entry based on the content of the line.
    while (!feof($hours)){
        $currentLine = fgets($hours);

        //Check for deleted rows, common when messing with csv's in excel
        $isNull = TRUE;
        for ($i = 0; $i < $currentLine; $i++)
        {
            if (substr($currentLine, $i, 1) != ",")
            {
                $isNull = FALSE;
                break;
            }
        }
        if ($currentLine == "" || $isNull)
            break;

        $currentData = explode(",", $currentLine);
        $clean = array();
        $clean = parse_row($headers, $currentData);
        db_save_hours($clean);
    }
    fclose($hours);
}
function parse_row($dataTypes, $dataLine) 
{
    $cleanHours = array();
    for ($x=0; $x<sizeof($dataTypes); $x++)
    {
        $data = $dataLine[$x];
        $dataType = $dataTypes[$x];
        $clean = "";
        switch ($dataType) {
            case "date":
                //$clean[$key] = "'" . date( "Y-m-d", strtotime( $val ) ) . " 1:00:00'";?
                $fullData = explode('/',$data);
                $date = date_create();
                date_date_set($date, $fullData[2], $fullData[0], $fullData[1]);
                $clean .= date_format($date, "Y-m-d");
                break;

            case "project_id":
                $tmp = db_get_project_id($data);
                if ($tmp['project_id'] == -1)
                {
                    //If the project hasn't yet been added, we'll add it to make life easier.
                    $newProj = array();
                    $newProj['project_name']=$data;
                    $newProj['total_hours']=0;
                    /*


                    work here



                    */
                    //$newProj['last_worked_id'] = db_get_category_id($dataLine[array_search("person_id", $dataTypes)]);

                    $cat_id = db_get_category_id($dataLine[array_search("category", $dataTypes)]);
                    $cat_id = $cat_id['category_id'];
                    if ($cat_id == NULL) 
                    {
                        //If the category also hasn't been added, then we'll add it to make life easier.
                        $newCat = array();
                        $newCat['category_name'] = $dataLine[array_search("category", $dataTypes)];
                        $newCat['category_desc'] = '';
                        $cat_id = db_get_last_id('categories', 'category_id');
                    }

                    $newProj['category_id']=$cat_id;
                    echo "New project to save: ";
                    print_r($newProj);
                    echo "<br>";
                    db_save_project($newProj);

                    $tmp = db_get_project_id($data);
                }
                $clean .= $tmp['project_id'];
                echo "our project id: ".$clean."<br>";
                break;

            case "person_id":
                list($firstname, $lastname) = explode(' ', $data);
                $clean = db_get_person_id($firstname, $lastname);
                if ($clean == NULL)
                {
                    die("Incorrect person. Please add: ".$firstname." ".$lastname);
                }
                break;

            case "hours":
            case "billable_hours":
            case "task":
            case "comments":
                $clean .= $data;
                break;

            case "billable":
                if ($data == "Yes")
                    $clean .= 1;
                elseif ($data == "No")
                    $clean .= 0;
                else
                    die("Incorrect input for billable. Requires 'Yes' or 'No' (without the quotes.)");
                break;

            case "category": //we don't have a category field in the hours table
                break;

            default:
                die("This type of heading is not allowed: " . $dataType);
                break;
        }
        $cleanHours[$dataType] = $clean;
        if (array_key_exists("category", $cleanHours))
        {
            unset($cleanHours["category"]);
        }
    }
    return $cleanHours;
}
?>
