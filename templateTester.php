<?php
/** Error reporting 
error_reporting(E_ALL);

error_reporting(E_ALL);
ini_set('display_errors', TRUE);
ini_set('display_startup_errors', TRUE);
*/

define('EOL',(PHP_SAPI == 'cli') ? PHP_EOL : '<br />');

date_default_timezone_set('America/Denver');

/** Include PHPExcel_IOFactory */
require_once 'Classes/PHPExcel/IOFactory.php';

function open_template()
{
    if (!file_exists("Templates/Template Hours Log.xlsx")) {
	    exit("Sorry, couldn't find the right template file. Please upload one." . EOL);
    }
    //return PHPExcel_IOFactory::load("Templates/Template Hours Log.xlsx");
    $objPHPExcelReader = PHPExcel_IOFactory::createReader('Excel2007');
    return $objPHPExcelReader->load('Templates/Template Hours Log.xlsx');
    
}

function save_template($objPHPExcel)
{

    $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
    $objWriter->save('Templates/Template Hours Log.xlsx');
}

function save_new_project($name, $cat, $desc)
{
    $objPHPExcel = open_template();

    $projectSheet = $objPHPExcel->getSheetByName('Projects');
    $highestRow = $projectSheet->getHighestRow();
    for ($i = 2; $i <= $highestRow; $i++)
    {
        if (strcasecmp($name, $projectSheet->getCell('A'.$i))<0)
        {
            $projectSheet->insertNewRowBefore($i,1);
            $projectSheet->setCellValue('A'.$i,$name);
            $projectSheet->setCellValue('B'.$i,$cat);
            $projectSheet->setCellValue('C'.$i,$desc);
            break;
        }
        elseif ($i == $highestRow)
        {
            $projectSheet->setCellValue('A'.$i,$name);
            $projectSheet->setCellValue('B'.$i,$cat);
            $projectSheet->setCellValue('C'.$i,$desc);
        }
    }

    add_data_validation($objPHPExcel);
    
    save_template($objPHPExcel);
}

function save_new_category($name, $desc)
{
    $objPHPExcel = open_template();

    $projectSheet = $objPHPExcel->getSheetByName('Categories');
    $highestRow = $projectSheet->getHighestRow();
    for ($i = 2; $i <= $highestRow; $i++)
    {
        if (strcasecmp($name, $projectSheet->getCell('A'.$i))<0)
        {
            $projectSheet->insertNewRowBefore($i,1);
            $projectSheet->setCellValue('A'.$i,$name);
            $projectSheet->setCellValue('B'.$i,$desc);
            break;
        }
        elseif ($i == $highestRow)
        {
            $projectSheet->setCellValue('A'.$i,$name);
            $projectSheet->setCellValue('B'.$i,$desc);
        }
    }

    add_data_validation($objPHPExcel);
    
    save_template($objPHPExcel);
}

function save_new_person($name)
{
    $objPHPExcel = open_template();

    $projectSheet = $objPHPExcel->getSheetByName('People');
    $highestRow = $projectSheet->getHighestRow();
    for ($i = 2; $i <= $highestRow; $i++)
    {
        if (strcasecmp($name, $projectSheet->getCell('A'.$i))<0)
        {
            $projectSheet->insertNewRowBefore($i,1);
            $projectSheet->setCellValue('A'.$i,$name);
            break;
        }
        elseif ($i == $highestRow)
        {
            $projectSheet->setCellValue('A'.$i,$name);
        }
    }

    add_data_validation($objPHPExcel);
    
    save_template($objPHPExcel);
}

function update_existing_project($old_name, $name, $cat, $desc)
{
    $objPHPExcel = open_template();

    $projectSheet = $objPHPExcel->getSheetByName('Projects');
    //Loop through until we find the project, then update it
    $highestRow = $projectSheet->getHighestRow();
    for ($i = 2; $i <= $highestRow; $i++)
    {
        if ($projectSheet->getCell('A'.$i) == $old_name)
        {
            $projectSheet->setCellValue('A'.$i,$name);
            $projectSheet->setCellValue('B'.$i,$cat);
            $projectSheet->setCellValue('C'.$i,$desc);
        }
    }

    add_data_validation($objPHPExcel);
    
    save_template($objPHPExcel);
}

function update_existing_category($old_name, $name, $desc)
{
    $objPHPExcel = open_template();

    $projectSheet = $objPHPExcel->getSheetByName('Categories');
    //Loop through until we find the category, then update it
    $highestRow = $projectSheet->getHighestRow();
    for ($i = 2; $i <= $highestRow; $i++)
    {
        if ($projectSheet->getCell('A'.$i) == $old_name)
        {
            $projectSheet->setCellValue('A'.$i,$name);
            $projectSheet->setCellValue('B'.$i,$desc);
        }
    }

    add_data_validation($objPHPExcel);
    
    save_template($objPHPExcel);
}

function update_existing_person($old_name, $name)
{
    $objPHPExcel = open_template();

    $projectSheet = $objPHPExcel->getSheetByName('People');
    //Loop through until we find the person, then update it
    $highestRow = $projectSheet->getHighestRow();
    for ($i = 2; $i <= $highestRow; $i++)
    {
        if ($projectSheet->getCell('A'.$i) == $old_name)
        {
            $projectSheet->setCellValue('A'.$i,$name);
        }
    }

    add_data_validation($objPHPExcel);
    
    save_template($objPHPExcel);
}

function delete_project($name)
{
    $objPHPExcel = open_template();
    $projectSheet = $objPHPExcel->getSheetByName('Projects');
    $highestRow = $projectSheet->getHighestRow();
    for ($i = 2; $i <= $highestRow; $i++)
    {
        if (strcasecmp($name, $projectSheet->getCell('A'.$i)) == 0)
        {
            $projectSheet->removeRow($i);
            break;
        }
    }

    add_data_validation($objPHPExcel);
    
    save_template($objPHPExcel);
}

function delete_category($name)
{
    $objPHPExcel = open_template();
    $projectSheet = $objPHPExcel->getSheetByName('Categories');
    $highestRow = $projectSheet->getHighestRow();
    for ($i = 2; $i <= $highestRow; $i++)
    {
        if (strcasecmp($name, $projectSheet->getCell('A'.$i)) == 0)
        {
            $projectSheet->removeRow($i);
            break;
        }
    }

    add_data_validation($objPHPExcel);
    
    save_template($objPHPExcel);
}

function delete_person($name)
{
    $objPHPExcel = open_template();
    $projectSheet = $objPHPExcel->getSheetByName('People');
    $highestRow = $projectSheet->getHighestRow();
    for ($i = 2; $i <= $highestRow; $i++)
    {
        if (strcasecmp($name, $projectSheet->getCell('A'.$i)) == 0)
        {
            $projectSheet->removeRow($i);
            break;
        }
    }

    add_data_validation($objPHPExcel);
    
    save_template($objPHPExcel);
}


//Adds data validation to the whole sheet, as PHPExcel doesn't retain any data validation when reading a file
function add_data_validation($objPHPExcel)
{
    $projRows = $objPHPExcel->getSheetByName('Projects')->getHighestRow();
    $catRows = $objPHPExcel->getSheetByName('Categories')->getHighestRow();
    $nameRows = $objPHPExcel->getSheetByName('People')->getHighestRow();
    $dateRows = $objPHPExcel->getSheetByName('Dates')->getHighestRow();
    for ($i = 2; $i<=141; $i++)
    {
        //Date validation
        $dateValidation = $objPHPExcel->getSheetByName('Tabular Record')->getCell('A'.$i)->getDataValidation();
        $dateValidation->setType( PHPExcel_Cell_DataValidation::TYPE_LIST );
        $dateValidation->setErrorStyle( PHPExcel_Cell_DataValidation::STYLE_INFORMATION );
        $dateValidation->setAllowBlank(true);
        $dateValidation->setShowInputMessage(true);
        $dateValidation->setShowErrorMessage(true);
        $dateValidation->setShowDropDown(false);
        $dateValidation->setErrorTitle('Input error');
        $dateValidation->setError('Value is not in list.');
        $dateValidation->setPromptTitle('Type the date');
        $dateValidation->setPrompt('Please enter a valid date.');
        $dateValidation->setFormula1('Name!$A$6:$A$19');
        
        //Project data validation
        $projValidation = $objPHPExcel->getSheetByName('Tabular Record')->getCell('B'.$i)->getDataValidation();
        $projValidation->setType( PHPExcel_Cell_DataValidation::TYPE_LIST );
        $projValidation->setErrorStyle( PHPExcel_Cell_DataValidation::STYLE_INFORMATION );
        $projValidation->setAllowBlank(true);
        $projValidation->setShowInputMessage(true);
        $projValidation->setShowErrorMessage(true);
        $projValidation->setShowDropDown(true);
        $projValidation->setErrorTitle('Input error');
        $projValidation->setError('Value is not in list.');
        $projValidation->setPromptTitle('Pick from list');
        $projValidation->setPrompt('Please pick a value from the drop-down list.');
        $projValidation->setFormula1('Projects!$A$2:$A$'.$projRows);

        //Hours validation
        $hoursValidation = $objPHPExcel->getActiveSheet()->getCell('C'.$i)->getDataValidation();
        $hoursValidation->setType( PHPExcel_Cell_DataValidation::TYPE_WHOLE );
        $hoursValidation->setErrorStyle( PHPExcel_Cell_DataValidation::STYLE_STOP );
        $hoursValidation->setAllowBlank(true);
        $hoursValidation->setShowInputMessage(true);
        $hoursValidation->setShowErrorMessage(true);
        $hoursValidation->setErrorTitle('Input error');
        $hoursValidation->setError('Only numbers between 0 and 24 are allowed!');
        $hoursValidation->setPromptTitle('Allowed input');
        $hoursValidation->setPrompt('Only numbers between 0 and 24 are allowed.');
        $hoursValidation->setFormula1(0);
        $hoursValidation->setFormula2(24);
        
        //Billable validation
        $valSheetName = "'Validation Lists'!";
        $billValidation = $objPHPExcel->getSheetByName('Tabular Record')->getCell('E'.$i)->getDataValidation();
        $billValidation->setType( PHPExcel_Cell_DataValidation::TYPE_LIST );
        $billValidation->setErrorStyle( PHPExcel_Cell_DataValidation::STYLE_INFORMATION );
        $billValidation->setAllowBlank(true);
        $billValidation->setShowInputMessage(true);
        $billValidation->setShowErrorMessage(true);
        $billValidation->setShowDropDown(true);
        $billValidation->setErrorTitle('Input error');
        $billValidation->setError('Value is not in list.');
        $billValidation->setPromptTitle('Pick from list');
        $billValidation->setPrompt('Please pick Yes or No');
        $billValidation->setFormula1($valSheetName.'$A$2:$A$3');

    }

    //Name validation
    $nameValidation = $objPHPExcel->getSheetByName('Name')->getCell('A3')->getDataValidation();
    $nameValidation->setType( PHPExcel_Cell_DataValidation::TYPE_LIST );
    $nameValidation->setErrorStyle( PHPExcel_Cell_DataValidation::STYLE_INFORMATION );
    $nameValidation->setAllowBlank(true);
    $nameValidation->setShowInputMessage(true);
    $nameValidation->setShowErrorMessage(true);
    $nameValidation->setShowDropDown(true);
    $nameValidation->setErrorTitle('Input error');
    $nameValidation->setError('Value is not in list.');
    $nameValidation->setPromptTitle('Pick from list');
    $nameValidation->setPrompt('Choose your name from this list');
    $nameValidation->setFormula1('People!$A$2:$A$'.$nameRows);

    //Date validation
    $dateValidation = $objPHPExcel->getSheetByName('Name')->getCell('A6')->getDataValidation();
    $dateValidation->setType( PHPExcel_Cell_DataValidation::TYPE_LIST );
    $dateValidation->setErrorStyle( PHPExcel_Cell_DataValidation::STYLE_INFORMATION );
    $dateValidation->setAllowBlank(true);
    $dateValidation->setShowInputMessage(true);
    $dateValidation->setShowErrorMessage(true);
    $dateValidation->setShowDropDown(true);
    $dateValidation->setErrorTitle('Input error');
    $dateValidation->setError('Value is not in list.');
    $dateValidation->setPromptTitle('Pick from list');
    $dateValidation->setPrompt('Choose your starting date from this list');
    $dateValidation->setFormula1('Dates!$A$2:$A$'.$dateRows);

    for ($i = 2; $i<=$projRows; $i++)
    {
        //Category validation
        $catValidation = $objPHPExcel->getSheetByName('Projects')->getCell('B'.$i)->getDataValidation();
        $catValidation->setType( PHPExcel_Cell_DataValidation::TYPE_LIST );
        $catValidation->setErrorStyle( PHPExcel_Cell_DataValidation::STYLE_INFORMATION );
        $catValidation->setAllowBlank(true);
        $catValidation->setShowInputMessage(true);
        $catValidation->setShowErrorMessage(true);
        $catValidation->setShowDropDown(true);
        $catValidation->setErrorTitle('Input error');
        $catValidation->setError('Value is not in list.');
        $catValidation->setPromptTitle('Pick from list');
        $catValidation->setPrompt('Choose a category from this list');
        $catValidation->setFormula1('Categories!$C$2:$C$'.$catRows);
    }
}
?>
