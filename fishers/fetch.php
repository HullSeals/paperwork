<?php
//UserSpice Required
require_once '../../users/init.php';
//Set Current User for Paperwork into NOT
$curruser45 = $user->data()->id;
define ('DB_USER', "");
define ('DB_PASSWORD', "");
define ('DB_DATABASE', "");
define ('DB_HOST', "localhost");

  $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_DATABASE);

//Get all valid records, minus "null" records, the Seal's own secondary assists, and not array of zero.
	$sql = "SELECT seal_name, seal_id FROM staff
			WHERE (seal_name LIKE '%".$_GET['query']."%') AND (seal_name <> 'Null') AND (seal_id NOT IN (0,'$curruser45'))
      ORDER BY seal_name ASC
			LIMIT 10";

	$result = $mysqli->query($sql);

//encode the results.
	$json = [];
	while($row = $result->fetch_assoc()){
	     $json[] = $row['seal_name'];
	}
	echo json_encode($json);
?>
