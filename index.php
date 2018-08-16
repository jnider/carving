<?php

include('db.php');
include('login.php');
include('menu.php');

// check to see if a user is logged in
if (is_logged_in())
{
	start_page("Actions");
	menu();
	echo "Content";
}

end_page();
?>
