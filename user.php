<?php

include('login.php');
include('db.php');
include('menu.php');

function show_form_add_user()
{
	echo "<h1>Add a new user account</h1>\n";
	echo "<form method=\"post\" action=\"user.php\">\n";
	echo "<input type=\"hidden\" name=\"action\" value=\"add\">\n";
	echo "<table>\n";
	echo "<tr><td>User Name:<td><input type=\"text\" name=\"name\"></tr>\n";
	echo "<tr><td>Password:<td><input type=\"password\" name=\"pass\"></tr>\n";
	echo "<tr><td>Confirm Password:<td><input type=\"password\" name=\"pass2\"></tr>\n";
	echo "<tr><td>Write Access:<td><input type=\"checkbox\" name=\"write\" value=\"T\"></tr>\n";
	echo "</table>\n";
	echo "<input type=\"submit\" value=\"Add\">\n";
	echo "</form>\n";
}

function add_user($db, $user, $pass, $write)
{
	$hash = password_hash($pass, PASSWORD_DEFAULT);
	echo "adding user $user with write=$write\n";
	if (!pg_query_params($db, 'insert into users (name, read, write, pass) values ($1, $2, $3, $4)', array($user, 'T', $write, $hash)))
		return FALSE;
	return TRUE;
}

// start output
start_page("User Management");
menu();

// figure out why the page was loaded
if (isset($_POST['action']))
{
	switch($_POST['action'])
	{	
	case "form_add":
		show_form_add_user();
		break;

	case "add":
		if ($_POST['pass'] !== $_POST['pass2'])
			echo "Passwords don't match\n";
		else
		{
			$db = connect_to_db();
			if ($db)
			{
				if (!add_user($db, $_POST['name'], $_POST['pass'], $_POST['write']))
					echo "User not added\n";
			}
			else
			{
				echo "Can't connect to the database\n";
			}
		}
	break;

default:
	echo "$action is not a valid action\n";
	}
}

// end output
stop_page();

?>

