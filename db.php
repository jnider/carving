<?php

// connect the database
function connect_to_db()
{
	$conn_string = "host=carving.postgres.database.azure.com port=5432 user=dbuser@carving dbname=carving password=Asda67as";
	return pg_connect($conn_string);
}

?>

