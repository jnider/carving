<?php

include('login.php');
include('db.php');
include('menu.php');

/*
 region         | integer          |           |          |
*/

function show_form_add_art($db, $item)
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

	echo "<tr><td>Material:<td><input type=\"text\" name=\"material\" value=\"${item['material']}\"></tr>\n";
	echo "<tr><td>Artist:<td><input type=\"text\" name=\"artist\" value=\"${item['artist']}\"></tr>\n";

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
	echo "<tr><td>Book ID:<td><input type=\"text\" name=\"book_id\" value=\"${item['book_id']}\"></tr>\n";
	echo "<tr><td>Original Tag ID:<td><input type=\"text\" name=\"reg_tag\" value=\"${item['reg_tag']}\"></tr>\n";
	echo "<tr><td>Description:<td><input type=\"text\" name=\"description\" value=\"${item['description']}\"></tr>\n";
	echo "</table>\n";

	echo "<h2>Dimensions</h2>\n";
	echo "<table>\n";
	echo "<tr><td>Height:<td><input type=\"text\" name=\"height\"> cm</tr>\n";
	echo "<tr><td>Width:<td><input type=\"text\" name=\"width\"> cm</tr>\n";
	echo "<tr><td>Depth:<td><input type=\"text\" name=\"depth\"> cm</tr>\n";
	echo "</table>\n";

	echo "<h2>Price</h2>\n";
	echo "<table>\n";
	echo "<tr><td>Purchase Price<td><input type=\"text\" name=\"purchase_price\">";
	echo"<td>Year<input type=\"week\" name=\"purchase_year\"></tr>\n";
	echo "<tr><td>Appraisal Price<td><input type=\"text\" name=\"appraisal\">";
	echo "<td>Year<input type=\"week\" name=\"appraisal_year\"></tr>\n";
	echo "<tr><td>Current Price<td><input type=\"text\" name=\"current_price\"></tr>\n";
	echo "</table>\n";
	echo "<input type=\"submit\" value=\"Add\">\n";
	echo "</form>\n";
}

function show_art($db, $id)
{
	echo "<div class=\"left\">\n";
	echo "<img src=\"images/left-arrow.png\" width=50%>\n";
	echo "</div>\n";

	echo "<div class=\"center\">\n";
	$res = pg_query($db, "select * from art where id = '$id'");
	if (!$res)
	{
		$err = pg_last_error($db);
		echo "Error retrieving art id=$id: $err";
		return FALSE;
	}
	$item = pg_fetch_assoc($res);
	echo "<table>\n";
	echo "<tr><td>Type<td>${item['type']}</tr>\n";
	echo "<tr><td>Artist<td>${item['artist']}</tr>\n";
	echo "<tr><td>Materials<td>${item['material']}</tr>\n";
	echo "<tr><td>Community<td>${item['community']}</tr>\n";
	echo "</table>\n";
	echo "Description: ${item['description']}\n";
	echo "</div>\n";

	echo "<div class=\"right\">\n";
	echo "<img src=\"images/right-arrow.png\" width=50%>\n";
	echo "</div>\n";
}

// use this to validate the input before inserting into the db
function art_looks_ok($db, $item)
{
	echo "<table>\n";
	//echo "<tr><td>art_type<td>${item['art_type']}</tr>\n";
	//echo "<tr><td>material<td>${item['material']}</tr>\n";
	//echo "<tr><td>artist<td>${item['artist']}</tr>\n";
	//echo "<tr><td>community<td>${item['community']}</tr>\n";
	//echo "<tr><td>book_id<td>${item['book_id']}</tr>\n";
	echo "<tr><td>reg_tag<td>${item['reg_tag']}</tr>\n";
	echo "<tr><td>description<td>${item['description']}</tr>\n";
	echo "<tr><td>height<td>${item['height']}</tr>\n";
	echo "<tr><td>width<td>${item['width']}</tr>\n";
	echo "<tr><td>depth<td>${item['depth']}</tr>\n";
	echo "<tr><td>purchase_price<td>${item['purchase_price']}</tr>\n";
	echo "<tr><td>purchase_year<td>${item['purchase_year']}</tr>\n";
	echo "<tr><td>appraisal<td>${item['appraisal']}</tr>\n";
	echo "<tr><td>appraisal_year<td>${item['appraisal_year']}</tr>\n";
	echo "<tr><td>current_price<td>${item['current_price']}</tr>\n";
	echo "</table>\n";
	return TRUE;
}

function add_art($db, $item)
{
/*
	return pg_query($db, "insert into art (art_type, material, artist, community, book_id, reg_tag, /
		description, height, width, depth, purchase_price, purchase_year, appraisal, appraisal_year, current_price) /
		values (\"${item['art_type']}\", \"${item['material']}\", \"${item['artist']}\", \"${item['community']}\", /
		\"${item['book_id']}\", \"${item['reg_tag']}\", \"${item['description']}\", \"${item['height']}\", \"${item['width']}\", /
		\"${item['depth']}\", \"${item['purchase_price']}\", \"${item['purchase_year']}\", \"${item['appraisal']}\", /
		\"${item['appraisal_year']}\", \"${item['current_price']}\")");
*/
	$values = array($item['art_type'], $item['material'], $item['artist'], $item['community'], $item['book_id'], $item['reg_tag'],
		$item['description']);
	$query = "insert into art (art_type, material, artist, community, book_id, reg_tag, description)" .
		" values ($1, $2, $3, $4, $5, $6, $7)";

	return pg_query_params($db, $query, $values);
}

// start output
start_page("Carving Details");
menu();

// figure out why the page was loaded
if (isset($_POST['action']))
{
	$db = connect_to_db();
	switch($_POST['action'])
	{	
	case "show":
		show_art($db, $_POST['id']);
		break;

	case "form_add":
		show_form_add_art($db, $item);
		break;

	case "add":
		if ($db)
		{
			$item['art_type'] = $_POST['art_type'];
			$item['material'] = $_POST['material'];
			$item['artist'] = $_POST['artist'];
			$item['community'] = $_POST['community'];
			$item['book_id'] = $_POST['book_id'];
			$item['reg_tag'] = $_POST['reg_tag'];
			$item['description'] = $_POST['description'];
			$item['height'] = $_POST['height'];
			$item['width'] = $_POST['width'];
			$item['depth'] = $_POST['depth'];
			$item['purchase_price'] = $_POST['purchase_price'];
			$item['purchase_year'] = $_POST['purchase_year'];
			$item['appraisal'] = $_POST['appraisal'];
			$item['appraisal_year'] = $_POST['appraisal_year'];
			$item['current_price'] = $_POST['current_price'];
			if (art_looks_ok($db, $item))
			{
				$res = add_art($db, $item);
				if (!$res)
				{
					$err = pg_last_error($db);
					echo "Something went wrong: $err\n";
					show_form_add_art($db, $item);
				}
				else
					echo "Added ok\n";
			}
			else
				show_form_add_art($db, $item);
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

