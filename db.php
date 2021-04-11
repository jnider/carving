<?php

require '../db-credentials.php';

// connect the database
function connect_to_db()
{
	global $conn_string;

	return pg_connect($conn_string);
}


/**
	Count the number of art items in the database
*/
function count_items($db)
{
	$query = "select count(id) from art";

	$res = pg_query($db, $query);
	if (!$res)
	{
		$err = pg_last_error($db);
		echo "Error counting items in db: $err";
		return FALSE;
	}

	$all = pg_fetch_assoc($res);
	return $all['count'];
}

/**
	Count the number of collections in the database
*/
function count_collections($db)
{
	$query = "select count(collection_id) from collection";

	$res = pg_query($db, $query);
	if (!$res)
	{
		$err = pg_last_error($db);
		echo "Error counting collections in db: $err";
		return FALSE;
	}

	$all = pg_fetch_assoc($res);
	return $all['count'];
}

/**
	Retrieve the community_lu table from the database.
*/
function get_communities($db)
{
	$res = pg_query($db, 'select * from community_lu order by name asc');
	if (!$res)
	{
		echo "Error building community list";
		return FALSE;
	}
	$communities = pg_fetch_all($res);
	return $communities;
}

?>
