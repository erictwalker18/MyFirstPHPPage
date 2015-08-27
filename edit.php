<?php
require_once ("Includes/simpledb-config.php"); 
require_once  ("Includes/connectDB.php");
require_once ("Includes/common.php");
require_once ("Functions/database.php");

check_user_session();

if( !$GLOBALS['user']['loggedin'] )
{
	redirect_by_url( 'logon.php' );
	exit();
}

$user = $GLOBALS['user'];
$pid = $user['person_id'];
$person_id = db_get_first_id('people', 'person_id');
$project_id = db_get_first_id('projects', 'project_id');

print_header("Edit Hours"); 

date_default_timezone_set('America/Denver');
$now = getdate();
$params = null;

if( isset( $_POST['month'] ) )
{
	$month = $_POST['month'];
    $sdate = mktime( 0, 0, 0, $now['mon'] + $month - 1, 1, $now['year'] );
    $edate = mktime( 0, 0, 0, $now['mon'] + $month + 1, 0, $now['year'] );
    $params['startdate'] = $sdate;
    $params['enddate'] = $edate;
}
else
{
	$month = $now['month'];
}
if( isset( $_POST['person_id'] ) )
{
	$params['person_id'] = $_POST['person_id'];
    $person_id = $_POST['person_id'];
}
if( isset( $_POST['project_id'] ) )
{
	$params['project_id'] = $_POST['project_id'];
    $project_id = $_POST['project_id'];
}

//Check for mass deletion
if ( isset( $_POST['deleteSelection']) )
{
    $checks = $_POST['formCheck'];
    if(empty($checks)) 
    {
?>
        <script> alert("No hours were selected to delete."); </script>
<?php
    } 
    else
    {
        $N = count($checks);
?>
        <script> alert("Deleting <?php echo $N ?> entries..."); </script>
<?php
        for($i=0; $i < $N; $i++)
        {
            db_delete_hours($checks[$i]);
        }
    }

}

if ( is_admin() )
{
?>

<form method="post" action="edit.php">
<p>View Month: <?php echo make_month_year_combo( 9, 2007, $now['mon'], $now['year'], $month ) ?>
<input type="submit" name ="Month" value="View" />
</p>
</form>
<form method="post" action="edit.php">
<p>View Project: <?php echo make_project_combo($project_id) ?>
<input type="submit" name="Project" value="View" />
</p>
</form>
<form method="post" action="edit.php">
<p>View Person: <?php echo make_person_combo($person_id) ?>
<input type="submit" name="Person" value="View" />
</p>
</form>
<form action="edit.php" method="post">

<div class="DataRow">
<div class="DataValue" style="margin-left:20px; min-width: 75px;">Date</div>
<div class="DataValue" style="margin-left:10px; min-width: 80px;">Person</div>
<div class="DataValue" style="margin-left:10px; min-width: 40px;">Hours</div>
<div class="DataValue" style="margin-left:10px; min-width: 200px;">Project</div>
</div>

<?php
$res = db_get_hours_for_blank( $params );

while( $res != null  && $row = $res->fetch_assoc() )
{
    $person_name = db_get_person($row['person_id']);
    $person_name = $person_name['person_lastname']. ', ' .$person_name['person_firstname'];
	$row['project_name'] = db_get_project($row['project_id'])['project_name'];
	?>
	<div class="DataRow">
    <div class="DataValue"><input type="checkbox" name="formCheck[]" value="<?php echo $row['hours_id'] ?>" /></div>
	<div class="DataValue"><a href="details.php?hours_id=<?php echo $row['hours_id'] ?>"><?php echo date("m-d-Y", strtotime($row['date'])) ?></a></div>
    <div class="DataValue" style="margin-left:10px; min-width:80px;"><?php echo $person_name ?></div>
	<div class="DataValue" style="margin-left:10px; min-width:40px;"><?php echo $row['hours'] ?></div>
	<div class="DataValue" style="margin-left:10px;"><?php echo $row['project_name'] ?></div>
	</div>
<?php
}
?>
    <input type="submit" name="deleteSelection" value="Delete Selected Entries" />
</form>
<?php
}
else
{
    redirect_by_url("index.php");
}
include ("Includes/footer.php");
?>
