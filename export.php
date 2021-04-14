<?php

include_once('login.php');

function cleanData(&$str)
{
	$str = preg_replace("/\t/", "\\t", $str);
	$str = preg_replace("/\r?\n/", "\\n", $str);
	$str = preg_replace("/,/", "\,", $str);
}

/****************************************/
// make sure the user is logged in
if (!is_logged_in() && is_admin())
	header('Location: index.php');

if (isset($_GET['action']))
{
	$page_action = $_GET['action'];
}

if (isset($page_action))
{
	// read the entire art table
	$res = pg_query($db, "select * from art order by book_id asc");
	if (!$res)
	{
		$err = pg_last_error($db);
		return FALSE;
	}
	$items = pg_fetch_all($res);

	// clean the data for CSV format
	array_walk($row, __NAMESPACE__ . '\cleanData');

	header("Content-type: text/csv");
	header("Content-disposition: attachment; filename=carvings.csv");

	//$num_items = count($items);
	echo "book_id,tag,artist,material,description\n";

	// print a row at a time
	foreach ($items as &$item)
	{
		echo "${item['book_id']},${item['reg_tag']},${item['artist']},\"${item['material']}\",\"${item['description']}\"\n"; 
	}
}
