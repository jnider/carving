<?php

/**
	Draw the menu at the top of screen
*/
function menu()
{
	// don't show anything if the user is not logged in
	if (!isset($_SESSION['username']))
		return;

	echo "<div class='menu'>\n";
	// These are the actions allowed by all users
echo <<< HTML
	<ul class='menu'>
		<li class='menu'><a class='menu' href="search.php">Search</a></li>
		<li class='menu'><a class='menu' href="logout.php?action=logout">Logout</a></li>
	</ul>
HTML;

	if ($_SESSION['privilege'] == 1)
	{
echo <<< HTML
	<ul class='menu'>
		<li class='menu'><a class='menu' href="art.php?action=form_add">Add Art Item</a></li>
		<li class='menu'><a class='menu' href="admin.php">Admin</a></li>
	</ul>
HTML;
	}

	echo "</div>\n";
}

?>

