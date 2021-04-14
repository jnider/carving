<?php

include_once('login.php');
include_once('menu.php');


/**
	The form for adding a new art type
*/
function show_form_add()
{
echo <<< HTML
	<h1>Art Types</h1>
	<form method="get" action="art_type.php">
	<input type="hidden" name="action" value="add">
	<table>
		<tr><td>Name:<td><input type="text" name="name"></tr>
	</table>
	<input type="submit" value="Add">
	</form>
HTML;
}

/**
	The form for modifying an existing art type
*/
function show_form_modify($db, $id)
{
	$art_type = get_art_type($db, $id);

echo <<< HTML
	<form method="post" action="community.php">
	<input type="hidden" name="action" value="update">
	<table>
HTML;
	echo "<input type=\"hidden\" name=\"id\" value=\"$id\">\n";
	echo "<tr><td>Name:<td><input type=\"text\" name=\"name\" value=\"$art_type[type]\"></tr>\n";
echo <<< HTML
	</table>
	<input type="submit" value="Modify">
	</form>
HTML;
}

/****************************************/
// make sure the user is logged in
if (!is_logged_in())
	header('Location: index.php');

// start output
$db = connect_to_db();
start_page("Art Type Maintenance");

// figure out why the page was loaded
if (isset($_POST['action']))
{
	$page_action=$_POST['action'];
	$id=$_POST['id'];
}
else if (isset($_GET['action']))
{
	$page_action = $_GET['action'];
	$id = $_GET['id'];
	$art_type=$_GET['name'];
}

switch($page_action)
{
	case "form_add":
		show_form_add();
		break;

	case "form_modify":
		show_form_modify($db, $id);
		break;

	case "add":
		if (add_art_type($db, $art_type) == FALSE)
			echo "<div class=response>Art type \"$art_type\" not added: $ret</div>\n";
		else
			echo "<div class=response>\"$art_type\" was added succesfully</div>\n";
		break;

	case "update":
		if (update_art_type($db, $id, $community_name, $alt_name) == FALSE)
			echo "<div class=response>Update failed</div>\n";
		else
			echo "<div class=response>Updated successfully</div>\n";
		break;

	case "delete":
		if (delete_art_type($db, $id) == FALSE)
			echo "<div class=response>Delete failed</div>\n";
		else
			echo "<div class=response>Deleted successfully</div>\n";
		break;
}

$art_types = get_art_types($db);
echo "<H2>Art Types</H2>\n";
echo "<table>\n";
echo "<tr id='content'><td>Name<td>Modify<td>Delete</tr>\n";
foreach ($art_types as &$art_type)
	echo "<tr id='content'><td>${art_type['type']}
		<td><a href=\"?action=form_modify&id=${art_type['id']}\"><img src=images/edit.png></a>
		<td><a href=\"?action=delete&id=${art_type['id']}\" onclick=\"return confirm('Are you sure you want to delete this art _type?')\"><img src=images/delete.png></a></tr>\n";
echo "</table>\n";

stop_page();

?>
