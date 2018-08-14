<?php

session_start();

echo "<!doctype html public \"-//w3c//dtd html 4.0 transitional//en\">\n";
echo "<HTML>\n";
echo "<head>\n";
echo "<title>Actions Menu</title>\n";
echo "</head>\n";
echo "<body>\n";
echo "<form method=\"post\" action=\"community.php\">\n";
echo "<input type=\"hidden\" name=\"action\" value=\"form_add\" />\n";
echo "<input type=\"submit\" value=\"Add Community\" />\n";
echo "</form>\n";

if (!isset($_SESSION['username'])
{
	echo "Not logged in\n";
}

echo "</body>\n";
echo "</HTML>\n";
?>
