<?php

include('login.php');

/**
	Validate an uploaded picture and add it to the database.
*/
function add_uploaded_picture($db, $item_id, $picture)
{ 
	$uploaddir = '/opt/carving/www/pictures/';

	// make sure it is really a JPEG with exif_imagetype()
	$ret = exif_imagetype($picture['tmp_name']);
	if ($ret == false)
	{
		echo "<div class='response_bad'>Can't read the uploaded file</div>\n";
		return;
	}

	if ($ret != IMAGETYPE_JPEG)
	{
		echo "<div class='response_bad'>Only JPEGs are supported</div>\n";
		return;
	}

	// generate a unique name
	$unique_name = sha1_file($picture['tmp_name']) . ".jpg";
	$uploadfile = $uploaddir . $unique_name;

	// resize the image if necessary, by restricting the width
	$max_width = 4048;
	$image_info = getimagesize($picture['tmp_name']);
	$width = $image_info[0];
	$height = $image_info[1];
	echo "width $width height $height<BR>";
	if ($width > $max_width)
	{
		$factor =  $max_width / $width;
		echo "resizing by $factor<BR>";
		$new_width = intval($width * $factor);
		$new_height = intval($height * $factor);
		echo "new width $new_width $new_height<BR>";
		$orig_image = imagecreatefromjpeg($picture['tmp_name']);
		if (!$orig_image)
		{
			echo "<div class='response_bad'>Error creating image</div>\n";
			return;
		}
		echo "1<BR>";
		$tmp_img = imagecreatetruecolor($new_width, $new_height);
		if (!$tmp_img)
		{
			echo "<div class='response_bad'>Error creating tmp image</div>\n";
			return;
		}
		echo "2<BR>";
		if (!imagecopyresampled($tmp_image, $orig_image, 0, 0, 0, 0, $new_width, $new_height, $width, $height))
		{
			echo "<div class='response_bad'>Error resampling image</div>\n";
			return;
		}
		echo "3<BR>";
		if (!imagejpeg($tmp_image, $uploadfile, 96))
		{
			echo "<div class='response_bad'>Error writing image</div>\n";
			return;
		}
		echo "OK!<BR>";
	}
	else
	{
		// move it to the correct location
		if (!move_uploaded_file($picture['tmp_name'], $uploadfile))
		{
			echo "<div class='response_bad'>Error storing the picture</div>\n";
			return;
		}
	}

	// if everything is ok, add it to the database
	$values = array(
		$item_id,
		$unique_name);

	$query = "insert into photos (art_id, url) values ($1, $2)";

	// if everything is ok, execute the query
	return pg_query_params($db, $query, $values);
}

function get_item($db, $item_id)
{
	if (!isset($item_id))
		$res = pg_query($db, "select * from art order by book_id desc limit 1");
	else
		$res = pg_query($db, "select * from art where id = '$item_id'");
	if (!$res)
	{
		$err = pg_last_error($db);
		echo "Error retrieving art item_id=$item_id: $err";
		return FALSE;
	}
	$item = pg_fetch_assoc($res);

	return $item;
}

/**
	Find the id of the previous item in the database.

	This should take into account the 'collection' of the current user
	(currently it doesn't)

	Returns an item id (not book id)
*/

function get_previous_item_by_book_id($db, $curr_book_id)
{
	$res = pg_query($db, "select * from art where book_id < '$curr_book_id' order by book_id desc limit 1");
	if ($res)
	{
		$temp_item = pg_fetch_assoc($res);
		if ($temp_item != false)
			return $temp_item['id'];
	}
	return -1;
}

/**
	Find the id of the next item in the database.

	This should take into account the 'collection' of the current user
	(currently it doesn't)

	Returns an item id (not book id)
*/
function get_next_item_by_book_id($db, $curr_book_id)
{
	$res = pg_query($db, "select * from art where book_id > '$curr_book_id' order by book_id asc limit 1");
	if ($res)
	{
		$temp_item = pg_fetch_assoc($res);
		if ($temp_item != false)
			return $temp_item['id'];
	}
	return -1;
}

function get_last_item_book_id($db)
{
	$temp_item = get_item($db, NULL);
	if ($temp_item != false)
		return $temp_item['book_id'];
	return -1;
}

/**
	Look up a string describing the type of art, based on an id.

	The id is an integer which uniquely identifies the art type
	entry in the database. This lookup depends on the art_type
	table in the database, which is retrieved using get_art_type().
*/
function lookup_art_type($art_types, $type)
{
	foreach ($art_types as &$art_type)
	{
		if ($type == $art_type['id'])
			return $art_type['type'];
	}
	return "Unknown";
}

function lookup_community($communities, $id)
{
	foreach ($communities as &$community)
	{
		if ($id == $community['id'])
			return $community['name'];
	}
	return "Unknown";
}

/**
	Display a form for adding a new art item
*/
function show_form_add_art($db)
{
	// get list of art types
	$art_types = get_art_types($db);

	// get list of communitites
	$communities = get_communities($db);

echo <<< HTML
	<h1>Add Art Item</h1>
	<form method="post">
	<input type="hidden" name="action" value="insert">
	<table>
	<tr><td>Art Type:<td><select name="art_type">
HTML;

	foreach ($art_types as &$art_type)
	{
		$id = $art_type['id'];
		$type = $art_type['type'];
		echo "<option value=\"$id\">$type\n";
	}

echo <<< HTML
	</select></tr>

	<tr><td>Artist:<td><input type="text" name="artist"></tr>
	<tr><td>Community:<td><select name="community">
HTML;

	foreach ($communities as &$community)
	{
		$id = $community['id'];
		$name = $community['name'];
		echo "<option value=\"$id\">$name\n";
	}

echo <<< HTML
	</select></tr>
	<tr><td>Book ID:<td><input type="text" name="book_id"></tr>
	<tr><td>Original Tag ID:<td><input type="text" name="reg_tag"></tr>
	<tr><td>Material:<td><input type="text" name="material"></tr>
	<tr><td>Description:<td><textarea cols="50" rows="6" name="description"></textarea></tr>
	</table>

	<h2>Dimensions</h2>
	<table>
	<tr><td>Height:<td><input type="text" name="height"> cm</tr>
	<tr><td>Width:<td><input type="text" name="width"> cm</tr>
	<tr><td>Depth:<td><input type="text" name="depth"> cm</tr>
	<tr><td>Number of pieces:<td><input type="text" name="pieces"></tr>
	</table>

	<h2>Price</h2>
	<table>
	<tr><td>Purchase Date<td><input type="date" name="purchase_date"></tr>
	<tr><td>Purchase Price<td><input type="text" name="purchase_price"></tr>
	<tr><td>Appraisal Date<td><input type="date" name="appraisal_date"></tr>
	<tr><td>Appraisal Price<td><input type="text" name="appraisal"></tr>
	</table>
	<input type="submit" value="Add">
	</form>
HTML;
}

/**
	Display a form for modifying an existing art item

	This is also used to add/modify pictures.
*/
function show_form_modify_art($db, $item_id, $group)
{
	// get the item
	$item = get_item($db, $item_id);
	if ($item == false)
		return;

	//echo "DB ID=$item_id Group=$group\n";

	// Pictures are handled differently than the rest of the entries
	// They are all separate files and are stored in a different table
	if ($group == "pictures")
	{
		$pictures = get_pictures($db, $item_id);
		echo "<h1>Modify Pictures</h1>\n";
		echo "<h2>Add New Picture</h2>\n";
		echo "<form method=\"post\" enctype=\"multipart/form-data\">\n";
		echo "<input type=\"hidden\" name=\"action\" value=\"add_picture\">\n";
		echo "<input type=\"hidden\" name=\"item_id\" value=\"$item_id\">\n";
		echo "<input type=\"file\" name=\"picture\" accept=\"image/png, image/jpeg\">\n";
		echo "<input type=\"submit\" value=\"Upload\">\n";
		echo "</form>\n";

		if ($pictures)
		{
			echo "<h2>Existing Pictures For This Item</h2>\n";
			echo "<table>\n";
			foreach ($pictures as &$picture)
				echo "<tr><td><img width=300px src=\"pictures/${picture['url']}\"></img><td><a href=?action=delete_picture&item_id=$item_id&photo_id=${picture['photo_id']} onclick=\"return confirm('Are you sure you want to delete this picture?')\">Remove</a></tr>\n";
			echo "</table>\n";
		}

		return;
	}

	echo "<h1>Modify Art Item</h1>\n";
	echo "<form method=\"post\">\n";
	echo "<input type=\"hidden\" name=\"action\" value=\"modify\">\n";
	echo "<input type=\"hidden\" name=\"item_id\" value=\"$item_id\">\n";
	echo "<input type=\"hidden\" name=\"group\" value=\"$group\">\n";

	if ($group == "details")
	{
		// get list of art types
		$art_types = get_art_types($db);

		// get list of communitites
		$communities = get_communities($db);

		echo "<table>\n";
		echo "<tr><td>Art Type:<td><select name=\"art_type\">\n";

		foreach ($art_types as &$art_type)
		{
			$id = $art_type['id'];
			$type = $art_type['type'];
			if ($id == $item['art_type'])
				echo "<option selected value=\"$id\">$type\n";
			else
				echo "<option value=\"$id\">$type\n";
		}
		echo "</select></tr>\n\n";

		echo "<tr><td>Artist:<td><input type=\"text\" name=\"artist\" value=\"${item['artist']}\"></tr>\n";
		echo "<tr><td>Community:<td><select name=\"community\">\n";

		foreach ($communities as &$community)
		{
			$id = $community['id'];
			$name = $community['name'];
			if ($id == $item['community'])
				echo "<option selected value=\"$id\">$name\n";
			else
				echo "<option value=\"$id\">$name\n";
		}
		echo "</select></tr>\n\n";

		echo "<tr><td>Book ID:<td><input type=\"text\" name=\"book_id\" value=${item['book_id']}></tr>\n";
		echo "<tr><td>Original Tag ID:<td><input type=\"text\" name=\"reg_tag\" value=${item['reg_tag']}></tr>\n";
		echo "<tr><td>Material:<td><input type=\"text\" name=\"material\" value=\"${item['material']}\"></tr>\n";
		echo "<tr><td>Description:<td><textarea cols=\"50\" rows=\"6\" name=\"description\">${item['description']}</textarea></tr>\n";
		echo "</table>\n";
	}

// Dimensions
	if ($group == "dimensions")
	{
	echo "<h2>Dimensions</h2>\n";
	echo "<table>\n";
	echo "<tr><td>Height:<td><input type=\"text\" name=\"height\" value=\"${item['height']}\"> cm</tr>\n";
	echo "<tr><td>Width:<td><input type=\"text\" name=\"width\" value=\"${item['width']}\"> cm</tr>\n";
	echo "<tr><td>Depth:<td><input type=\"text\" name=\"depth\" value=\"${item['depth']}\"> cm</tr>\n";
	echo "<tr><td>Number of pieces:<td><input type=\"text\" name=\"pieces\" value=\"${item['pieces']}\"></tr>\n";
	echo "</table>\n";
	}

// Price
	if ($group == "price")
	{
		echo "<h2>Price</h2>\n";
		echo "<table>\n";
		echo "<tr><td>Purchase Date<td><input type=date name=\"purchase_date\" value=\"${item['purchase_date']}\"></tr>\n";
		echo "<tr><td>Purchase Price<td><input type=\"text\" name=\"purchase_price\" value=\"${item['purchase_price']}\">$</tr>\n";
		echo "<tr><td>Appraisal Date<td><input type=date name=\"appraisal_date\" value=\"${item['appraisal_date']}\"></tr>\n";
		echo "<tr><td>Appraisal Price<td><input type=\"text\" name=\"appraisal\" value=\"${item['appraisal']}\">$</tr>\n";
		echo "</table>\n";
	}

	echo "<input type=\"submit\" value=\"Update\">\n";
	echo "</form>\n";
}

/**
	Shows the details of a specific art item

	db connection to database
	item_id integer item id
*/
function show_art($db, $item_id)
{
	// if no item is specified, just grab the first valid one
	$item = get_item($db, $item_id);
	if ($item == false)
	{
		echo "<div class='response_bad'>No items in database!</div>\n";
		return;
	}
	if (!isset($book_id))
		$book_id = $item['book_id'];

	// find the previous item
	$prev_item_id = get_previous_item_by_book_id($db, $book_id);

	// find the next item
	$next_item_id = get_next_item_by_book_id($db, $book_id);

	// Replace art_type id with string
	$art_types = get_art_types($db);
	$art_type = lookup_art_type($art_types, $item['art_type']);

	// Replace community id with string
	$communities = get_communities($db);
	$community = lookup_community($communities, $item['community']);

	// get pictures
	$pictures = get_pictures($db, $item_id);

	echo "<div>\n";
	echo "<div class='leftpane'>\n";
	if ($prev_item_id == -1)
		echo "<img class=disabled src=\"images/left-arrow.png\" width=50px></img>\n";
	else
		echo "<a href=\"?item_id=$prev_item_id\"><img src=\"images/left-arrow.png\" width=50px></img></a>\n";
	echo "</div>\n";

	echo "<div class='middlepane'>\n";
	echo "<h2>Details</h2>\n";
	if (is_admin())
		echo "<a class=edit href=\"?action=form_modify&group=details&item_id=${item['id']}\"><img src=images/edit.png></a>";
	echo "<table>\n";
	echo "<tr><td>Book ID:<td>${item['book_id']}</tr>";
	echo "<tr><td>Tag ID:<td>${item['reg_tag']}</tr>";
	echo "<tr><td>Art Type:<td>$art_type</tr>";
	echo "<tr><td>Artist:<td><a href=search.php?action=filter&artist=", urlencode("${item['artist']}"),">${item['artist']}</a></tr>";
	echo "<tr><td>Material:<td>${item['material']}</tr>";
	echo "<tr><td>Community:<td>$community</tr>";
	echo "<tr><td>Description:<td id='description'>${item['description']}</tr>";
	echo "</table>\n";

	echo "<h2>Dimensions</h2>\n";
	if (is_admin())
		echo "<a class=edit href=\"?action=form_modify&group=dimensions&item_id=${item['id']}\"><img src=images/edit.png></a>";
	echo "<table>\n";
	echo "<tr><td>Height:<td>${item['height']} cm</tr>";
	echo "<tr><td>Width:<td>${item['width']} cm</tr>";
	echo "<tr><td>Depth:<td>${item['depth']} cm</tr>";
	echo "<tr><td>Number of pieces:<td>${item['pieces']}</tr>";
	echo "</table>\n";

	echo "<h2>Price</h2>\n";
	if (is_admin())
		echo "<a class=edit href=\"?action=form_modify&group=price&item_id=${item['id']}\"><img src=images/edit.png></a>";
	echo "<table>\n";
	echo "<tr><td>Purchase Date:<td>${item['purchase_date']}</tr>\n";
	echo "<tr><td>Purchase Price:<td>$${item['purchase_price']}</tr>\n";
	echo "<tr><td>Appraisal Date:<td>${item['appraisal_date']}</tr>\n";
	echo "<tr><td>Appraisal Price:<td>$${item['appraisal']}</tr>\n";
	echo "</table>\n";

	echo "<h2>Pictures</h2>\n";
	if (is_admin())
		echo "<a class=edit href=\"?action=form_modify&group=pictures&item_id=${item['id']}\"><img src=images/edit.png></a>";

	echo "<table>\n";
	foreach ($pictures as &$picture)
		echo "<tr><td><img width=300px src=\"pictures/${picture['url']}\"></img></tr>\n";
	echo "</table>\n";
	echo "</div class='middlepane'>\n";

	echo "<div class='rightpane'>\n";
	if ($next_item_id == -1)
		echo "<img class=disabled src=\"images/right-arrow.png\" width=50px></img>\n";
	else
		echo "<a href=\"?item_id=$next_item_id\"><img src=\"images/right-arrow.png\" width=50px></img></a>\n";
	echo "</div>\n";

	echo "</div>\n";
}

function modify_art_item($db, $item_id, $group)
{
	// make sure the user is allowed to update items
	if ($_SESSION[privilege] != 1)
	{
		echo "<div class='response_bad'>You do not have permission to modify items</div>\n";
		return FALSE;
	}

	//echo "item_id=$item_id group=$group";

	switch($group)
	{
	case "details":
		// read the variables from the posted form
		$item['art_type'] = $_POST['art_type'];
		$item['artist'] = $_POST['artist'];
		$item['community'] = $_POST['community'];
		$item['book_id'] = $_POST['book_id'];
		$item['reg_tag'] = $_POST['reg_tag'];
		$item['material'] = $_POST['material'];
		$item['description'] = $_POST['description'];

		// build the query
		$values = array($item['art_type'],
			$item['artist'],
			$item['community'],
			$item['book_id'],
			$item['reg_tag'],
			$item['material'],
			$item['description'],
			$item_id);

		$query = "update art set art_type = $1, artist = $2, community = $3, book_id = $4, reg_tag = $5, material = $6, description = $7 where id = $8";
		break;


	case "dimensions":
		// read the variables from the posted form
		$item['height'] = $_POST['height'];
		$item['width'] = $_POST['width'];
		$item['depth'] = $_POST['depth'];
		$item['pieces'] = $_POST['pieces'];

		// build the query
		$values = array(
			$item['height'],
			$item['width'],
			$item['depth'],
			$item['pieces'],
			$item_id);

		$query = "update art set height = $1, width = $2, depth = $3, pieces = $4 where id = $5";
		break;

	case "price":
		// read the variables from the posted form
		$item['purchase_price'] = $_POST['purchase_price'];
		$item['purchase_date'] = $_POST['purchase_date'];
		$item['appraisal'] = $_POST['appraisal'];
		$item['appraisal_date'] = $_POST['appraisal_date'];

		// date fields are sensitive to an empty string (must be NULL instead)
		if ($item['purchase_date'] == '')
			$item['purchase_date'] = NULL;
		if ($item['appraisal_date'] == '')
			$item['appraisal_date'] = NULL;

		// build the query
		$values = array(
			$item['purchase_price'],
			$item['purchase_date'],
			$item['appraisal'],
			$item['appraisal_date'],
			$item_id);

		$query = "update art set purchase_price = $1, purchase_date = $2, appraisal = $3, appraisal_date = $4 where id = $5";
		break;

	default:
		return false;
	}

	// if everything is ok, execute the query
	return pg_query_params($db, $query, $values);
}

function insert_art_item($db)
{
	// make sure the user is allowed to insert items
	if ($_SESSION[privilege] != 1)
	{
		echo "<div class='response'>You do not have permission to add items</div>\n";
		return FALSE;
	}

	// read the variables from the posted form
	$item['art_type'] = $_POST['art_type'];
	$item['artist'] = $_POST['artist'];
	$item['community'] = $_POST['community'];
	$item['book_id'] = $_POST['book_id'];
	$item['reg_tag'] = $_POST['reg_tag'];
	$item['material'] = $_POST['material'];
	$item['description'] = $_POST['description'];
	$item['height'] = $_POST['height'];
	$item['width'] = $_POST['width'];
	$item['depth'] = $_POST['depth'];
	$item['pieces'] = $_POST['pieces'];
	$item['purchase_price'] = $_POST['purchase_price'];
	$item['purchase_date'] = $_POST['purchase_date'];
	$item['appraisal'] = $_POST['appraisal'];
	$item['appraisal_date'] = $_POST['appraisal_date'];

	if ($item['book_id'] == '')
	{
		echo "<div class='response_bad'>You must enter a book id.<BR>A book id must be a number (integer) and it must be unique</div>\n";
		return FALSE;
	}

	// date fields are sensitive to an empty string (must be NULL instead)
	if ($item['purchase_date'] == '')
		$item['purchase_date'] = NULL;
	if ($item['appraisal_date'] == '')
		$item['appraisal_date'] = NULL;

	// build the query
	$values = array(
		intval($item['art_type']),
		$item['material'],
		$item['artist'],
		intval($item['community']),
		intval($item['book_id']),
		$item['reg_tag'],
		$item['description'],
		intval($item['height']),
		intval($item['width']),
		intval($item['depth']),
		intval($item['pieces']),
		intval($item['purchase_price']),
		$item['purchase_date'],
		intval($item['appraisal']),
		$item['appraisal_date']);
	$query = "insert into art (art_type, material, artist, community, book_id, reg_tag, description, height, width, depth, pieces, purchase_price, purchase_date, appraisal, appraisal_date) values ($1, $2, $3, $4, $5, $6, $7, $8, $9, $10, $11, $12, $13, $14, $15)";

	return pg_query_params($db, $query, $values);
}

/****************************************/
// make sure the user is logged in
if (!is_logged_in())
	header('Location: index.php');

// start output
$db = connect_to_db();
start_page("Carving Details");

// figure out why the page was loaded
if (isset($_POST['action']))
{
	$page_action = $_POST['action'];
}
else if (isset($_GET['action']))
{
	$page_action = $_GET['action'];
}

switch($page_action)
{	
case "form_add":
	show_form_add_art($db);
	break;

case "form_modify":
	$item_id = $_GET['item_id'];
	$group = $_GET['group'];
	show_form_modify_art($db, $item_id, $group);
	break;

case "insert":
	if (insert_art_item($db))
	{
		echo "<div class='response_ok'>Item added successfully</div>\n";
	}
	else
	{
		$err = pg_last_error($db);
		echo "<div class='response_bad'>Error adding item<BR>$err</div>\n";
	}
	break;

case "modify":
	$item_id = $_POST['item_id'];
	$group = $_POST['group'];
	if (!modify_art_item($db, $item_id, $group))
		echo "<div class='response_bad'>Error updating item</div>\n";
	show_art($db, $item_id);
	break;

case "add_picture":
	$item_id = $_POST['item_id'];
	$picture = $_FILES['picture'];
	if (!add_uploaded_picture($db, $item_id, $picture))
		echo "<div class='response_bad'>Error adding picture</div>\n";
	show_art($db, $item_id);
	break;

case "delete_picture":
	$item_id = $_GET['item_id'];
	$photo_id = $_GET['photo_id'];
	delete_picture($db, $item_id, $photo_id);
	show_form_modify_art($db, $item_id, "pictures");
	break;

case "show":
	$item_id = $_GET['item_id'];
	show_art($db, $item_id);
	break;

default:
	$item_id = $_GET['item_id'];
	show_art($db, $item_id);
	break;
}

// end output
stop_page();

?>
