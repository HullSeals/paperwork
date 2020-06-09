<?php
//UserSpice Required
require_once '../../users/init.php';
//Set Current User for Paperwork into NOT
$curruser45 = $user->data()->id;
//fetch.php;
define ('DB_USER', "UNM");
define ('DB_PASSWORD', "PASS");
define ('DB_DATABASE', "DB");
define ('DB_HOST', "localhost");

  $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_DATABASE);
	$sql = "SELECT seal_name, seal_id FROM staff
			WHERE (seal_name LIKE '%".$_GET['query']."%') AND (seal_name <> 'Null') AND (seal_id NOT IN (0,'$curruser45'))
      ORDER BY seal_name ASC
			LIMIT 10";

	$result = $mysqli->query($sql);

	$json = [];
	while($row = $result->fetch_assoc()){
	     $json[] = $row['seal_name'];
	}
	echo json_encode($json);
?>
