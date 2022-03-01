<?php
ini_set('display_errors', 1); 

$host = 'localhost';
$port = '5432';
$dbname = 'mig_stats';
$user = 'postgres';
$password = 'admin';

$conn = pg_connect("host=$host port=$port dbname=$dbname user=$user password=$password");
if (!$conn) {
	echo "Not connected : " . pg_error();
	exit;
}

$table = $_GET['table'];
$fields = $_GET['fields'];

$fieldstr = "";
foreach ($fields as $i => $field){
	$fieldstr = $fieldstr . "l.$field, ";
}

$fieldstr = $fieldstr . "ST_AsGeoJSON(ST_Transform(l.geom,4326))";

$sql = "SELECT $fieldstr FROM $table l";


if (isset($_GET['featname'])){
	$featname = $_GET['featname'];
	$distance = $_GET['distance'] * 1000; 
	$sql = $sql . " LEFT JOIN $table r ON ST_DWithin(l.geom, r.geom, $distance) WHERE r.featname = '$featname';";
}


if (!$response = pg_query($conn, $sql)) {
	echo "A query error occured.\n";
	exit;
}

while ($row = pg_fetch_row($response)) {
	foreach ($row as $i => $attr){
		echo $attr.", ";
	}
	echo ";";
}

?>