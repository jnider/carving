<?php

function menu()
{
	echo "<div class='menu'>";

	// 'add community' button
	echo "<form method=\"post\" action=\"art.php\">\n";
	echo "<input type=\"hidden\" name=\"action\" value=\"form_add\" />\n";
	echo "<input type=\"submit\" value=\"Add Art Item\" />\n";
	echo "</form>\n";

	// 'add community' button
	echo "<form method=\"post\" action=\"community.php\">\n";
	echo "<input type=\"hidden\" name=\"action\" value=\"form_add\" />\n";
	echo "<input type=\"submit\" value=\"Add Community\" />\n";
	echo "</form>\n";

	// 'add user' button
	echo "<form method=\"post\" action=\"user.php\">\n";
	echo "<input type=\"hidden\" name=\"action\" value=\"form_add\" />\n";
	echo "<input type=\"submit\" value=\"Add User\" />\n";
	echo "</form>\n";

	// 'logout' button
	echo "<form method=\"post\" action=\"login.php\">\n";
	echo "<input type=\"hidden\" name=\"action\" value=\"logout\" />\n";
	echo "<input type=\"submit\" value=\"Logout\" />\n";
	echo "</form>\n";

	echo "</div>\n";
}

?>

