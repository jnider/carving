<?php

include('login.php');
include('db.php');
include('menu.php');

/*
 region         | integer          |           |          |
*/

function show_form_add_art($db)
{
	echo "<h1>Add Art Item</h1>\n";

	// now output the form
	echo "<form method=\"post\">\n";
	echo "<input type=\"hidden\" name=\"action\" value=\"add\">\n";
	echo "<table>\n";

	// get list of art types
	$res = pg_query($db, 'select * from art_type');
	if (!$res)
	{
		echo "Error building art type list";
		return FALSE;
	}
	echo "<tr><td>Art Type:<td><select name=\"art_type\">";
	while ($art_type = pg_fetch_assoc($res))
	{
		$id = $art_type['id'];
		$type = $art_type['type'];
		echo "<option value=\"$id\">$type\n";
	}
	echo "</select></tr>\n";

	echo "<tr><td>Material:<td><input type=\"text\" name=\"material\"></tr>\n";
	echo "<tr><td>Artist:<td><input type=\"text\" name=\"artist\"></tr>\n";

	// get list of communitites
	$res = pg_query($db, 'select * from community_lu');
	if (!$res)
	{
		echo "Error building community list";
		return FALSE;
	}
	echo "<tr><td>Community:<td><select name=\"community\">";
	while ($community = pg_fetch_assoc($res))
	{
		$id = $community['id'];
		$name = $community['name'];
		echo "<option value=\"$id\">$name\n";
	}
	echo "</select></tr>\n";
	echo "<tr><td>Book ID:<td><input type=\"text\" name=\"book_id\"></tr>\n";
	echo "<tr><td>Original Tag ID:<td><input type=\"text\" name=\"reg_tag\"></tr>\n";
	echo "<tr><td>Description:<td><input type=\"text\" name=\"description\"></tr>\n";
	echo "</table>\n";

	echo "<h2>Dimensions</h2>\n";
	echo "<table>\n";
	echo "<tr><td>Height:<td><input type=\"text\" name=\"height\"> cm</tr>\n";
	echo "<tr><td>Width:<td><input type=\"text\" name=\"width\"> cm</tr>\n";
	echo "<tr><td>Depth:<td><input type=\"text\" name=\"depth\"> cm</tr>\n";
	echo "</table>\n";

	echo "<h2>Price</h2>\n";
	echo "<table>\n";
	echo "<tr><td>Purchase Price<td><input type=\"text\" name=\"purchase_price\"><td>Year<input type=\"week\" name=\"purchase_date\"></tr>\n";
	echo "<tr><td>Appraisal Price<td><input type=\"text\" name=\"appraisal\"><td>Year<input type=\"week\" name=\"appraisal_year\"></tr>\n";
	echo "<tr><td>Current Price<td><input type=\"text\" name=\"current_price\"></tr>\n";
	echo "</table>\n";
	echo "<input type=\"submit\" value=\"Add\">\n";
	echo "</form>\n";
}

function show_art($db)
{
	echo "<div class=\"left\">\n";
	echo "<img src=\"images/left-arrow.png\" width=50%>\n";
	echo "</div>\n";

	echo "Some text\n";

	echo "<div class=\"right\">\n";
	echo "<img src=\"images/right-arrow.png\" width=50%>\n";
	echo "</div>\n";
}

// use this to validate the input before inserting into the db
function art_looks_ok($db, $item)
{
	echo "<table>\n";
	echo "<tr><td>art_type<td>$item['art_type']</tr>\n";
	echo "</table>\n";
	return TRUE;
}

function add_art($db, $item)
{
	echo "You are adding a new art item\n";
}

// start output
start_page("User Management");
menu();

// figure out why the page was loaded
if (isset($_POST['action']))
{
	$db = connect_to_db();
	switch($_POST['action'])
	{	
	case "show":
		show_art($db);
		break;

	case "form_add":
		show_form_add_art($db);
		break;

	case "add":
		if ($db)
		{
			$item['art_type'] = $_POST['art_type'];
			if (art_looks_ok($db, $item))
				add_art($db, $item);
		}
		else
		{
			echo "Can't connect to the database\n";
		}
		break;

default:
	echo "$action is not a valid action\n";
	}
}

// end output
stop_page();

?>

