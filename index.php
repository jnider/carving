<?php

include('db.php');
include('login.php');
include('menu.php');

// check to see if a user is logged in
if (is_logged_in())
{
	start_page("Welcome");
	echo "<div>\n";
	echo "This is the Nider Carving Database";
	echo "</div>\n";
}
else
{
	start_page("Carving and Art Collection - Login");
	show_login_form();
}

end_page();
?>
