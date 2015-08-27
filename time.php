<?php

include_once( 'Includes/common.php' );

check_user_session();

if( !$GLOBALS['user']['loggedin'] )
{
	redirect_by_url( 'logon.php' );
	exit();
}

$user = $GLOBALS['user'];
$pid = $user['person_id'];

print_header( "Time Records for {$user['person_firstname']} {$user['person_lastname']}" );

date_default_timezone_set('America/Denver');
$now = getdate();
if( isset( $_REQUEST['month'] ) )
{
	$month = $_REQUEST['month'];
}
else
{
	$month = 0;
}
?>

<p><a href="details.php">Log New Hours</a> | <a href="calendar.php">Show Calendar View</a></p>
<form method="get" action="time.php">
<p>View Month: <?php echo make_month_year_combo( 9, 2007, $now['mon'], $now['year'], $month ) ?>
<input type="submit" value="View" />
</p>
</form>

<div class="DataRow">
<div class="DataValue" style="min-width: 75px;">Date</div>
<div class="DataValue" style="min-width: 50px;">Hours</div>
<div class="DataValue" style="min-width: 200px;">Project</div>
</div>

<?php

$sdate = mktime( 0, 0, 0, $now['mon'] + $month - 1, 1, $now['year'] );
$edate = mktime( 0, 0, 0, $now['mon'] + $month + 1, 0, $now['year'] );
$res = db_get_hours_in_range( $pid, $sdate, $edate );

while( $row = $res->fetch_assoc() )
{
	?>
	<div class="DataRow">
	<div class="DataValue"><a href="details.php?hours_id=<?php echo $row['hours_id'] ?>"><?php echo date("m-d-Y", strtotime($row['date'])) ?></a></div>
	<div class="DataValue" style="margin-left:10px; min-width:40px;"><?php echo $row['hours'] ?></div>
	<div class="DataValue" style="margin-left:10px;"><?php echo $row['project_name'] ?></div>
	</div>
	<?php
}

print_footer();

//<p>I added a calendar! <a href="./calendar.php">Check it out.</a> --Erty </p>?>

