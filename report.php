<?php
    
include_once( 'Includes/common.php' );

$start = time();
date_default_timezone_set('America/Denver');
$start_array = getdate( $start );
if( $start_array['mday'] > 15 )
{
	$start_day = 16;
	$end = mktime( 1, 1, 1, $start_array['mon'] + 1, 0 );
	$end_array = getdate( $end );
	$end_day = $end_array['mday'];
}
else
{
	$start_day = 1;
	$end_day = 15;
}

$start = mktime( 0, 0, 0, $start_array['mon'], $start_day );
$end = mktime( 0, 0, 0, $start_array['mon'], $end_day);

print_header( 'Reports' );

?>

<table>
	<tr>
		<td class="ReportTypeBox" style = "width: 250px; vertical-align: top">
			<div class="ReportTypeBoxHeader">
			Summary Report
			</div>
			<div class="ReportTypeDetails">
			<form action="report_summary.php" method="get" target="_new">
			<table border="0" ><!--cellspacing="0"-->
				<tr>
					<td class="TableDataCaption">Start Date</td>
					<td class="TableDataValue"><input name="start" value="<?php echo date( "n/d/Y", $start )?>" /></td>
				</tr>
				<tr>
					<td class="TableDataCaption">End Date</td>
					<td class="TableDataValue"><input name="end" value="<?php echo date( "n/d/Y", $end )?>" /></td>
				</tr>
				<tr>
					<td class="TableDataCaption">People</td>
					<td class="TableDataValue"><?php echo make_people_list() ?></td>
				</tr>
			</table>
			<br>
			<input type="submit" value="Generate Report" />
			</form>
			</div>
		</td>
		<td class="ReportTypeBox" style = "width: 350px; vertical-align: top">
			<div class="ReportTypeBoxHeader">
			Category Detail Report
			</div>
			<div class="ReportTypeDetails">
			<form action="report_detail.php" method="get" target="_new">
			<table border="0" ><!--cellspacing="0"-->
				<tr>
					<td class="TableDataCaption">Category</td>
					<td class="TableDataValue"><?php echo make_category_combo( 3 ) ?></td>
				</tr>
				<tr>
					<td class="TableDataCaption">Start Date</td>
					<td class="TableDataValue"><input name="start" value="<?php echo date( "n/d/Y", $start ) ?>" /></td>
				</tr>
				<tr>
					<td class="TableDataCaption">End Date</td>
					<td class="TableDataValue"><input name="end" value="<?php echo date( "n/d/Y", $end ) ?>" /></td>
				</tr>
			</table>
			<br>
			<input type="submit" value="Generate Report" />
			</form>
			</div>
		</td>
	</tr>
	<tr>
		<td class="ReportTypeBox" style = "width: 350px; vertical-align: top">
			<div class="ReportTypeBoxHeader">
			Project Detail Report 
            <?php
            if ( !isset($_REQUEST['all']) )
            {
	        ?>
            <a href="report.php?all" style="float: right;">All Projects</a>
            <?php
            }
            else
            {
            ?>
            <a href="report.php" style="float: right;">Active Projects</a>
            <?php
            }                 
            ?>
			</div>
			<div class="ReportTypeDetails">
			<form action="report_detail.php" method="get" target="_new">
			<table border="0" ><!--cellspacing="0"-->
				<tr>
					<td class="TableDataCaption">Project</td>
					<td class="TableDataValue"><?php
                    if (!isset($_REQUEST['all'])) {
                        echo make_project_combo_active( 0 );
                    }
                    else{
                        echo make_project_combo(0);
                    } ?></td>
				</tr>
				<tr>
					<td class="TableDataCaption">Start Date</td>
					<td class="TableDataValue"><input name="start" value="<?php echo date( "n/d/Y", $start ) ?>" /></td>
				</tr>
				<tr>
					<td class="TableDataCaption">End Date</td>
					<td class="TableDataValue"><input name="end" value="<?php echo date( "n/d/Y", $end ) ?>" /></td>
				</tr>
			</table>
			<br>
			<input type="submit" value="Generate Report" />
			</form>
			</div>
		</td>
		<td class="ReportTypeBox" style = "width: 250px; vertical-align: top">
			<div class="ReportTypeBoxHeader">
			CSV Export
			</div>
			<div class="ReportTypeDetails">
			<form action="report_csv.php" method="get">
			<table border="0" ><!--cellspacing="0"-->
				<tr>
					<td class="TableDataCaption">Start Date</td>
					<td class="TableDataValue"><input name="start" value="<?php echo date( "n/d/Y", $start ) ?>" /></td>
				</tr>
				<tr>
					<td class="TableDataCaption">End Date</td>
					<td class="TableDataValue"><input name="end" value="<?php echo date( "n/d/Y", $end ) ?>" /></td>
				</tr>
			</table>
			<br>
			<input type="submit" value="Generate Report" />
			</form>
			</div>
		</td>
	</tr>
</table>
<?php

print_footer();

?>