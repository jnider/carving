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

/**
	Retrieve the art_type table from the database.

	The art type describes the type of art - painting, carving, etc.
	This can be used to show all of the possible types (like in a dropdown
	selection box) or looking up a particular string.
*/
function get_art_types($db)
{
	$res = pg_query($db, 'select * from art_type order by type asc');
	if (!$res)
	{
		echo "Error building art type list";
		return FALSE;
	}
	$art_types = pg_fetch_all($res);
	return $art_types;
}

/**
	Retrieve a specific art type from the database.

	@param id specifies the id of the art_type entry
*/
function get_art_type($db, $id)
{
	$query = 'select * from art_type where id=$1';
	$res = pg_query_params($db, $query, array($id));
	if ($res == FALSE)
		return FALSE;
	$row = pg_fetch_assoc($res);
	return $row;
}

/**
	Add a new art type
*/
function add_art_type($db, $type)
{
	$query = "insert into art_type (type) values ($1)";
	$res = pg_query_params($db, $query, array($type));
	if ($res == FALSE)
		return FALSE;
	return TRUE;
}

/**
	Write the updated fields to the database
*/
function update_art_type($db, $id, $name, $alt_name)
{
	$query = "update art_type set type=$1 where id=$2";
	$res = pg_query_params($db, $query, array($name, $id));
	if ($res == FALSE)
		return false;
	return true;
}

function delete_art_type($db, $id)
{
	$query = 'delete from art_type where id=$1';
	$res = pg_query_params($db, $query, array($id));
	if ($res == FALSE)
		return FALSE;
	return TRUE;
}

?>
