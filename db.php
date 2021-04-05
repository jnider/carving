<?php

require '../db-credentials.php';

// connect the database
function connect_to_db()
{
	global $conn_string;

	return pg_connect($conn_string);
}

?>

