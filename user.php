<?php

function show_form_add_user()
{
	echo "<form method=\"post\" action=\"user.php\">\n";
	echo "<input type=\"hidden\" name=\"action\" value=\"add\">\n";
	echo "User Name:<input type=\"text\" name=\"name\"><BR>\n";
	echo "Password:<input type=\"password\" name=\"pass\"><BR>\n";
	echo "Confirm Password:<input type=\"password\" name=\"pass2\"><BR>\n";
	echo "<input type=\"submit\" value=\"Add\">\n";
	echo "</form>\n";
}

function add_user($db, $user, $pass)
{
	echo "adding user $user with pass $pass\n";
	return TRUE;
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
			if (!add_user($db, $_POST['name'], $_POST['pass']))
				echo "User not added\n";
		}
	break;

default:
	echo "$action is not a valid action\n";
	}
}

// end output
stop_page();

?>

