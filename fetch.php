<?php
//UserSpice Required
require_once '../users/init.php';
//Set Current User for Paperwork into NOT
$curruser45 = $user->data()->id;
//fetch.php;
$db = include 'db.php';
$mysqli = new mysqli($db['server'], $db['user'], $db['pass'], "sealsudb", $db['port']);
	$sql = "SELECT seal_name, seal_id FROM staff
			WHERE (seal_name LIKE '%".$_GET['query']."%') AND (seal_id NOT IN (0,'$curruser45')) AND del_flag <> 1
      ORDER BY seal_name ASC
			LIMIT 10";

	$result = $mysqli->query($sql);

	$json = [];
	while($row = $result->fetch_assoc()){
	     $json[] = $row['seal_name'];
	}
	echo json_encode($json);
?>
