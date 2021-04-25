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

function sum_purchase_price($db)
{
	$query = "select sum(purchase_price) from art";

	$res = pg_query($db, $query);
	if (!$res)
	{
		$err = pg_last_error($db);
		echo "Error counting collections in db: $err";
		return FALSE;
	}

	$all = pg_fetch_assoc($res);
	return $all['sum'];
}

function eval_price($db)
{
	$total = 0;

	$query = "select id, purchase_price, purchase_date, appraisal, appraisal_date from art";
	$res = pg_query($db, $query);
	if (!$res)
	{
		$err = pg_last_error($db);
		echo "Error counting collections in db: $err";
		return FALSE;
	}

	$all = pg_fetch_all($res);
	foreach ($all as &$item)
	{
		if ($item['purchase_price'] == NULL)
			continue;

		if ($item['purchase_date'] && $item['appraisal'] != '0' && $item['appraisal_date'])
		{
			/* perform a 2-point 'linear regression' */

			// price difference
			$price_diff = $item['appraisal'] - $item['purchase_price'];

			// time difference
			//echo "Purchase date: ${item['purchase_date']}";
			$pdate = date_create_from_format("!Y-m-d", $item['purchase_date']);
			$adate = date_create_from_format("!Y-m-d", $item['appraisal_date']);
			//echo "Increase ${item['id']}: $price_diff<BR>\n";
		}
		else
			$total += $item['purchase_price'];
	}

	return $total;
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

/**
	Get all art items in the database
*/
function get_art($db)
{
	$query = "select * from art order by book_id asc";

	$res = pg_query($db, $query);
	if (!$res)
	{
		$err = pg_last_error($db);
		echo "Error getting art items: $err";
		return FALSE;
	}

	$items = pg_fetch_all($res);
	return $items;
}

/**
	Extract artist names from all art items
*/
function get_artists($db)
{
	$query = "select distinct artist from art order by artist asc";

	$res = pg_query($db, $query);
	if (!$res)
	{
		$err = pg_last_error($db);
		echo "Error getting art items: $err";
		return FALSE;
	}

	$artists = array();
	while ($row = pg_fetch_assoc($res))
	{
		if ($row['artist'] != '')
			$artists[] = $row['artist'];
	}
	return $artists;
}

function delete_picture($db, $item_id, $photo_id)
{
	$values = array($item_id, $photo_id);

	// verify that both art id and picture id match
	$query = "delete from photos where art_id = $1 and photo_id = $2";

	// if everything is ok, execute the query
	$res = pg_query_params($db, $query, $values);
	if (!$res)
	{
		$err = pg_last_error($db);
		echo "Error retrieving pictures id=$item_id: $err";
		return FALSE;
	}

	$pictures = pg_fetch_all($res);
	return $pictures;
}

/**
	Get all of the pictures associated with an item
*/
function get_pictures($db, $item_id)
{
	$values = array($item_id);

	$query = "select * from photos where art_id = $1";

	// if everything is ok, execute the query
	$res = pg_query_params($db, $query, $values);
	if (!$res)
	{
		$err = pg_last_error($db);
		echo "Error retrieving pictures id=$item_id: $err";
		return FALSE;
	}

	$pictures = pg_fetch_all($res);
	return $pictures;
}

?>
