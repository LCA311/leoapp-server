<?php
	
	require_once('../dbconfig.php');

	$db = new mysqli(dbhost, dbuser, dbpass, dbname);

	if ($db->connect_error)
    	die("-connection failed: ".$db->connect_error);

	// mitgegebene Werte �ber get: cname, cid

	$cname = $db->real_escape_string($_GET['cname']);
	$cid = $_GET['cid'];

	$query = "UPDATE Chats SET cname = '".$cname."' WHERE cid = ".$cid;

	$result = $db->query($query);
	if ($result === false)
		die("-error in query");

	$db->close();

?>