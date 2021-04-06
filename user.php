<?php

include('login.php');
include('db.php');
include('menu.php');

function show_form_add_user()
{
	echo "<form method=\"post\">\n";
	echo "<input type=\"hidden\" name=\"action\" value=\"add\">\n";
	echo "<table>\n";
	echo "<tr><td>User Name:<td><input type=\"text\" name=\"name\"></tr>\n";
	echo "<tr><td>Password:<td><input type=\"password\" name=\"pass\"></tr>\n";
	echo "<tr><td>Confirm Password:<td><input type=\"password\" name=\"pass2\"></tr>\n";
	echo "<tr><td>Administrator Privileges:<td><input type=\"checkbox\" name=\"write\" value=\"1\"></tr>\n";
	echo "</table>\n";
	echo "<input type=\"submit\" value=\"Add\">\n";
	echo "</form>\n";
}

function add_user($db, $user, $pass, $write)
{
	$hash = password_hash($pass, PASSWORD_DEFAULT);
	if (!pg_query_params($db, 'insert into users (name, write, pass) values ($1, $2, $3)', array($user, $write, $hash)))
		return FALSE;
	return TRUE;
}

function delete_user($db, $user_id)
{
	if (!pg_query_params($db, 'delete from users where user_id=$1', array($user_id)))
		return false;
	return true;
}

/**
	Returns an array with all users
*/
function get_all_users($db)
{
	$res = pg_query($db, 'select * from users');
	if (!$res)
	{
		echo "Error getting users";
		return FALSE;
	}

	return pg_fetch_all($res);
}

// make sure the user is logged in
if (!is_logged_in())
	header('Location: index.php');

// start output
$db = connect_to_db();
start_page("User Management");

// figure out why the page was loaded
if (isset($_POST['action']))
{
	$page_action = $_POST['action'];
	$user_id = $_POST['user_id'];
}
else if (isset($_GET['action']))
{
	$page_action = $_GET['action'];
	$user_id = $_GET['user_id'];
}

if ($page_action)
{
	switch($page_action)
	{
	case "add":
		if ($_POST['pass'] !== $_POST['pass2'])
			echo "<div class=response>Passwords don't match</div>\n";
		else
		{
			if (!add_user($db, $_POST['name'], $_POST['pass'], $_POST['write']))
				echo "User not added\n";
			else
				echo "<div class=\'response\'>User $_POST[name] added succesfully</div>\n";
		}
		break;

	case "delete":
		if (delete_user($db, $user_id))
			echo "<div class=\'response'\>Deleted user $user_id</div>\n";
		else
			echo "<div class=\'response\'>Error while deleting user $user_id</div>\n";
		break;
	}
}

echo "<H1>User Management</H1>";

// show the form to add a new user
show_form_add_user();

// show a table containing all users
$users = get_all_users($db);
echo "<H2>Existing Users</H2>\n";
echo "<table>\n";
echo "<tr id='content'><td>Name<td>Administrator<td>Change Password<td>Delete</tr>\n";

foreach ($users as &$user)
{
	$admin = ($user['write'] == 1) ? "Yes": "";
	echo "<tr id='content'><td>${user['name']}<td>$admin<td>X<td><a href='?action=delete&user_id=${user['user_id']}' onclick=\"return confirm('Are you sure you want to delete this user?')\">X</a></tr>";
}

echo "</table>\n";

// end output
stop_page();

?>
