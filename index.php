<?php

include_once('login.php');
include_once('menu.php');

// check to see if a user is logged in
if (is_logged_in())
{
	$db = connect_to_db();

	start_page("Welcome");
	echo "<H1>The Nider Carving Database</H1>";

	if (is_admin())
	{
		// show some statistics
		$num_items = count_items($db);
		$num_collections = count_collections($db);
		echo "There are $num_items entries in the database<BR>\n";
		echo "There are $num_collections collections in the database<BR>\n";
	}
}
else
{
	start_page("Carving and Art Collection - Login");

	if (isset($login_failed) && $login_failed)
		echo "<div class=response>Login failed - check your username and password</div>\n";

	show_login_form();
}

end_page();
?>
