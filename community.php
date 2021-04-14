<?php

include_once('login.php');
include_once('menu.php');

// declare the functions

function show_form_add()
{
echo <<< HTML
	<h1>Add New Community</h1>
	<form method="post" action="community.php">
	<input type="hidden" name="action" value="add">
	<table>
		<tr><td>Name:<td><input type="text" name="name"></tr>
		<tr><td>Alternative Name:<td><input type="text" name="alt_name"></tr>
	</table>
	<input type="submit" value="Add">
	</form>
HTML;
}

function show_form_modify($db, $id)
{
	$query = 'select * from community_lu where id=$1';
	$res = pg_query_params($db, $query, array($id));
	if ($res == FALSE)
		return FALSE;
	$row = pg_fetch_assoc($res);

echo <<< HTML
	<form method="post" action="community.php">
	<input type="hidden" name="action" value="update">
	<table>
HTML;
	echo "<input type=\"hidden\" name=\"id\" value=\"$id\">\n";
	echo "<tr><td>Name:<td><input type=\"text\" name=\"name\" value=\"$row[name]\"></tr>\n";
	echo "<tr><td>Alternative Name:<td><input type=\"text\" name=\"alt_name\" value=\"$row[alt_name]\"></tr>\n";
echo <<< HTML
	</table>
	<input type="submit" value="Modify">
	</form>
HTML;
}

/**
	Add a new community
*/
function add($db, $name, $alt_name)
{
	$query = "insert into community_lu (name, alt_name) values ($1, $2)";
	$res = pg_query_params($db, $query, array($name, $alt_name));
	if ($res == FALSE)
		return -1;
	return 0;
}

/**
	Write the updated fields to the database
*/
function update_community($db, $id, $name, $alt_name)
{
	$query = "update community_lu set name=$1,alt_name=$2 where id=$3";
	$res = pg_query_params($db, $query, array($name, $alt_name, $id));
	if ($res == FALSE)
		return false;
	return true;
}

/****************************************/
// make sure the user is logged in
if (!is_logged_in())
	header('Location: index.php');

// start output
$db = connect_to_db();
start_page("Communities");

// figure out why the page was loaded
if (isset($_POST['action']))
{
	$page_action=$_POST['action'];
	$community_name=$_POST['name'];
	$alt_name=$_POST['alt_name'];
	$id=$_POST['id'];
}
else if (isset($_GET['action']))
{
	$page_action = $_GET['action'];
	$id = $_GET['id'];
}

switch($page_action)
{
	case "form_add":
		show_form_add();
		break;

	case "add":
		$ret=add($db, $community_name, $alt_name);
		if ($ret)
			echo "<div class=response>Community not added: $ret</div>\n";
		else
			echo "<div class=response>\"$community_name\" was added succesfully</div>\n";
		break;

	case "modify":
		show_form_modify($db, $id);
		break;

	case "update":
		if (!update_community($db, $id, $community_name, $alt_name))
			echo "<div class=response>Update failed</div>\n";
		else
			echo "<div class=response>Updated succesfully</div>\n";
		break;
}

$communities = get_communities($db);
echo "<H2>Communitites</H2>\n";
echo "<table>\n";
echo "<tr id='content'><td>Name<td>Alternate Name<td>Modify<td>Delete</tr>\n";
foreach ($communities as &$community)
	echo "<tr id='content'><td>$community[name]<td>$community[alt_name]<td><a href=\"?action=modify&id=$community[id]\"><img src=images/edit.png></a><td>X</tr>\n";
echo "</table>\n";

stop_page();

?>
