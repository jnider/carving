<?php

function show_login_form()
{
echo "<HTML>\n";
echo "<head>\n";
echo "<title>Login</title>\n";
echo "</head>\n";
echo "<body>\n";
echo "<form method=\"post\" action=\"index.php\">\n";
echo "<input type=\"hidden\" name=\"action\" value=\"login\" />\n";
echo "<input type=\"text\" name=\"name\" />\n";
echo "<input type=\"password\" name=\"password\" />\n";
echo "<input type=\"submit\" value=\"Login\" />\n";
echo "</form>\n";
echo "</body>\n";
echo "</HTML>\n";
}

session_start();

if (!isset($_SESSION['username']))
{
	login();
}

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
echo "</body>\n";
echo "</HTML>\n";
?>
