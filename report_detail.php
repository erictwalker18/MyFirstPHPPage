<?php

include_once( 'Includes/common.php' );
include_once( 'Functions/database.php' );

date_default_timezone_set('America/Denver');

if( isset( $_REQUEST['category_id'] ) )
{
	$cid = $_REQUEST['category_id'];
	$category = db_get_category( $cid );
	$name = $category['category_name'];
}
else
{
	$pid = $_REQUEST['project_id'];
	$project = db_get_project( $pid );
	$name = $project['project_name'];
}

$start = strtotime( $_REQUEST['start'] );
$end = strtotime( $_REQUEST['end'] );

$sstart = date( "F j, Y", $start );
$send = date( "F j, Y", $end );

$start_array = getdate( $start );
$end_array = getdate( $end );

$start_day = $start_array['yday'];
$end_day = $end_array['yday'];

$days = array();

for( $day = 0; $day <= ($end_day - $start_day); $day++ )
{
	$date = strtotime( "+{$day} day", $start );
	$sdate = date( "Y-m-d", $date );
	$days[$date] = $sdate;
}

$people = db_get_people();

$title = "{$name}: {$sstart} to {$send}";

?>
<html>
<head>
<title><?php echo $title ?></title>
</head>
<link rel="stylesheet" type="text/css" href="report_detail.css" />
<body>

<h2><?php echo $title ?></h2>

<table style="border: 1px solid black;" cellspacing="0" cellpadding="0">
	<tr>
		<td class="ReportColumn ReportRow">&nbsp;</td>
		<?php
		foreach( $days as $date => $sdate )
		{
			$sdate = date( "n/j/Y", $date );
			echo "<td class=\"ReportColumn ReportRow\">{$sdate}</td>";
		}
		?>
		<td class="ReportRow"><b>Total</b></td>
	</tr>
	<?php
	
	$daytotal = array();
	
	foreach( $people as $id => $person )
	{
		echo "<tr>\n";
		echo "\t\t<td class=\"ReportColumn ReportRow\">{$person['person_lastname']}, {$person['person_firstname']}</td>";
		$total = 0;
		
		foreach( $days as $date => $sdate )
		{
			if( isset( $cid ) )
			{
				$sql = "SELECT SUM(hours.hours) AS hours FROM hours INNER JOIN projects ON hours.project_id = projects.project_id WHERE person_id={$id} AND projects.category_id={$cid} AND hours.date='{$sdate}'";
			}
			else
			{
				$sql = "SELECT SUM(hours.hours) AS hours FROM hours WHERE hours.project_id={$pid} AND person_id={$id} AND hours.date='{$sdate}'";
			}
			$res = $databaseConnection->query($sql);
			$row = $res->fetch_assoc();
			$hours = $row['hours'];
			if( $hours == '' )
			{
				$hours = '&nbsp;';
			}
			else
			{
				$total += $hours;
				
				if( isset( $daytotal[$date] ) )
				{
					$daytotal[$date] += $hours;
				}
				else
				{
					$daytotal[$date] = $hours;
				}
			}
			echo "<td class=\"ReportColumn ReportRow\">{$hours}</td>";
		}
		echo "<td class=\"ReportRow\"><b>{$total}</b></td>\n";
		echo "\t</tr>\n";
	}

	echo "\t<tr>\n";
	echo "\t\t<td class=\"ReportColumn\"><b>Total</b></td>";
	
	$total = 0;
	foreach( $days as $date => $sdate )
	{
		if( isset( $daytotal[$date] ) )
		{
			echo "<td class=\"ReportColumn\"><b>{$daytotal[$date]}</b></td>";
			$total += $daytotal[$date];
		}
		else
		{
			echo "<td class=\"ReportColumn\">&nbsp;</td>";
		}
	}
	echo "<td><b>{$total}</b></td>\n\t</tr>\n";
	?>
</table>

</body>
</html>

<?php
?>