<?php

function show_form_add_user()
{
	echo "<form method=\"post\" action=\"user.php\">\n";
	echo "<input type=\"hidden\" name=\"action\" value=\"add\">\n";
	echo "User Name:<input type=\"text\" name=\"name\"><BR>\n";
	echo "Password:<input type=\"password\" name=\"pass\"><BR>\n";
	echo "Confirm Password:<input type=\"password\" name=\"pass2\"><BR>\n";
	echo "Write Access:<input type=\"checkbox\" name=\"write\" value=\"T\"><BR>\n";
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

// connect the database
function connect_to_db()
{
	$conn_string = "host=carving.postgres.database.azure.com port=5432 user=dbuser@carving dbname=carving password=Asda67as";
	return pg_connect($conn_string);
}

function start_page($title)
{
	echo "<HTML>\n";
	//echo "<head><title>Add community</title></head>\n";
	echo "<body>\n";
}

function stop_page()
{
	echo "</body>\n</HTML>\n";
}

// start output
start_page("User Management");

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

