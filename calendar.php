<?php

include_once( 'Includes/common.php' );

check_user_session();

if( !$GLOBALS['user']['loggedin'] )
{
	redirect_by_url( 'index.php' );
	exit();
}

$user = $GLOBALS['user'];
$pid = $user['person_id'];

print_header( "Time Records for {$user['person_firstname']} {$user['person_lastname']}" );

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
<style type="text/css">
	.cal_day, .cal_date, .cal_date_grey{
		display:inline;
	}
	.cal_day{
		display: table-cell;
		width: 4em;
		height: 3em;
		border: 1px solid #006666;
		margin: .1em;
		padding: .2em;
	}
	.day_header{
		padding-left: .6em;
		border: none !important;
		display: inline;
	}
	.cal_date_grey{
		color:#555555;
	}
	.cal_row{
		
	}
	.cal_date{
		
	}
</style>
<p><a href="details.php">Log New Hours</a> | <a href="time.php">Show List View</a></p>
<form method="get" action="calendar.php">
<p>View Month: <?php echo make_month_year_combo( 9, 2007, $now['mon'], $now['year'], $month ) ?>
<input type="submit" value="View" />
</p>
THE CALENDAR MIGHT NOT WORK RIGHT NOW!
</form>


<?php

	$sdate = mktime( 0, 0, 0, $now['mon'] + $month, 1, $now['year'] );
	$edate = mktime( 0, 0, 0, $now['mon'] + $month + 1, 0, $now['year'] ) + 86400; //add a day
	$res = db_get_hours_in_range( $pid, $sdate, $edate );

	if( $err = mysql_error() )
	{
		echo $err;
	}

	//PRINT THE CALENDAR
	$daysArray = array('Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday');
	foreach($daysArray as $dayTitle){
		echo('<div class="day_header">');
		echo($dayTitle);
		echo('</div>');
	}
	echo('<div class="cal_row">');
	$day = date("w", $sdate);
	if($day == 7){
			echo('</div><div class="cal_row">');
			$day = 0;
	}
	for($filler = 0; $filler < $day; $filler ++){
	?>
		<div class="cal_day">
			<div class="cal_date_grey">...</div>
			<div class="cal_hr">...</div>
		</div>
	<?php
	}
	for($d = 0; $d < numDays($sdate, $edate); $d++){
		$row = $sdate + ($d * 86400);
		$day ++;
		?>
		<div class="cal_day">
			<div class="cal_date"><?php echo(date("n/d", $sdate + ($d * 86400))); ?></div>
			<div class="cal_hr"><a href="<?php echo('details.php?cal_date=' . date("m/d/Y",$row)); ?>" id="<?php echo(date("m/d", $row)); ?>">0</a></div>
		</div>
		<?php
		if($day == 7){
			echo('</div><div class="cal_row">');
			$day = 0;
		}
	}
	echo('</div>');

	//ADD THE HOURS
	$arrstr = "";
	while( $row = $res->fetch_assoc() ){
		$arrstr .= date("m/d", strtotime($row['date']));
		$arrstr .= ('#');
		$arrstr .= ('<a href=\'details.php?hours_id=' . $row['hours_id'] . '\'>');
		$arrstr .= ($row['hours']);
		$arrstr .= '</a>';
		$arrstr .= ('%');
	}
	?>
		<script type="text/javascript">
			var arrstr = "<?php echo($arrstr); ?>";
			spl = arrstr.split("%");
			for (var f = 0; f < spl.length; f++){
				spl[f] = spl[f].split("#");
				if(spl[f][0] != null && spl[f][1] != null){
					inh = document.getElementById(spl[f][0]);
					inh.href = getA(spl[f][1], spl[f][0]);
					inh.innerHTML = (getNum(spl[f][1]) * 1.0) + (getNum(inh.innerHTML) * 1.0);
				}
			}
			function getNum(h){
				var re= /<\S[^><]*>/g;
				return(h.replace(re,""));				
			}
			function getA(a, dat){
				var re= /<\S[^><]*>/g;
				var b = "" + (a.match(re));
				if(b != null) return b.substr(9, b.length-16);
				return "";
			}
		</script>
	<?php
	print_footer();

	function numDays($start, $end) {
		$diff = $end - $start;
		return round($diff / 86400);
	}
?>
