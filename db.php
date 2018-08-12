<?php

function connect_to_db()
{
	$conn_string = "host=carving.postgres.database.azure.com port=5432 user=dbuser@carving dbname=carving password=Asda67as";
	$dbconn = pg_connect($conn_string);
	return $dbconn;
}

?>

