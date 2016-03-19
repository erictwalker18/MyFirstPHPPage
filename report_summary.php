<?php

include_once( 'Includes/common.php' );
include_once( 'Functions/database.php' );

date_default_timezone_set('America/Denver');

$start = strtotime( $_REQUEST['start'] );
$end = strtotime( $_REQUEST['end'] );

$sstart = date( "F j, Y", $start );
$send = date( "F j, Y", $end );

?>
<html>
<head>
<title>Summary Report: <?php echo $sstart ?> to <?php echo $send ?></title>
</head>
<link rel="stylesheet" type="text/css" href="report_summary.css" />
<body>

<p class="ReportHeader">Summary Report</p>
<p class="ReportDate"><?php echo $sstart ?> - <?php echo $send ?></p>

<?php

if( $_REQUEST['person_id'] == 'all' )
{
	$people = db_get_people();
}
elseif( $_REQUEST['person_id'] == 'active' )
{
	$people = db_get_people_group( 'active', 1 );
}
else
{
	// _$REQUEST will only have the last person_id variable in it, so we
	// have to parse the query string ourselves.
	$people = array();
	$query = explode( '&', $_SERVER['QUERY_STRING'] );
	foreach( $query as $var )
	{
		$req = explode( '=', $var );
		if( $req[0] == 'person_id' )
		{
			$person = db_get_person( $req[1] );
			$people[$person['person_id']] = $person;
		}
	}

}

$sstart = date( "Y-m-d", $start );
$send = date( "Y-m-d", $end );
$cats = db_get_category_array();

foreach( $people as $id => $person )
{
	?>
	<div class="SubReportBody">
	<div class="SubReportHeader"><?php echo $person['person_lastname'] ?>, <?php echo $person['person_firstname'] ?></div>
	<div class="ReportTable">
	
	<div class="ReportHeaderRow">
	<div class="ReportColumnSmall">Hours</div>
    <div class="ReportColumnSmall">Billable Hours</div>
	<div class="ReportFirstColumn">Project Category</div>
	</div>

	<?php
	$total = 0;
    $total_billable = 0;
	foreach( $cats as $category_id => $cat )
	{
		$sql = "SELECT SUM(hours.hours) AS total, SUM(hours.billable_hours) AS total_billable FROM hours INNER JOIN projects ON hours.project_id = projects.project_id INNER JOIN categories on projects.category_id = categories.category_id WHERE person_id={$id} AND categories.category_id = {$category_id} AND hours.date >= '{$sstart}' AND hours.date <= '{$send}'";
		
		if( !($res = $databaseConnection->query($sql)) )
		{
			show_mysql_error( $databaseConnection->error() );
		}
		else
		{
			$row = $res->fetch_assoc();
			?>
			<div class="ReportRow">
			<div class="ReportColumnSmall"><?php
			if( isset( $row['total'] ) )
			{
				echo $row['total'];
				$total += $row['total'];
			}
			else
			{
				echo '0';
			}
			?></div>
            <div class="ReportColumnSmall"><?php
			if( isset( $row['total_billable'] ) )
			{
				echo $row['total_billable'];
				$total_billable += $row['total_billable'];
			}
			else
			{
				echo '0';
			}
			?></div>
			<div class="ReportFirstColumn"><?php echo $cat['category_name'] ?></div>
			</div>
			<?php
		}
	}
	?>
	<div class="ReportBottomRow">
    <div class="ReportColumnSmall"><b><?php echo $total_billable ?></b></div>
	<div class="ReportColumnSmall"><b><?php echo $total ?></b></div>
	<div class="ReportFirstColumn"><b>Total Hours</b></div>
	</div>
	</div>
	
	</div>
	<?php
}
?>

</body>
</html>
<?php
?>