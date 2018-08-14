<?php

function show_login_form()
{
echo "<head>\n";
echo "<title>Login</title>\n";
echo "</head>\n";
echo "<body>\n";
echo "<form method=\"post\" action=\"index.php\">\n";
echo "<input type=\"hidden\" name=\"action\" value=\"login\" />\n";
echo "<input type=\"text\" name=\"username\" />\n";
echo "<input type=\"password\" name=\"password\" />\n";
echo "<input type=\"submit\" value=\"Login\" />\n";
echo "</form>\n";
echo "</body>\n";
}

function login()
{
	if ($user == "joel" && $password == "nider")
		return 0;
	return 1;
}

if (isset($_POST['action']))
{
	if ($_POST['action'] == "login")
	{
		$ret = login($_POST['username'], $_POST['password']);
		if ($ret)
			echo "Error logging in\n";
		else
			header('Location: index.php');
	}
}

session_start();

echo "<!doctype html public \"-//w3c//dtd html 4.0 transitional//en\">\n";
echo "<HTML>\n";
if (!isset($_SESSION['username']))
{
	show_login_form();
}
else
{
echo "<head>\n";
echo "<title>Actions Menu</title>\n";
echo "</head>\n";
echo "<body>\n";
echo "<form method=\"post\" action=\"community.php\">\n";
echo "<input type=\"hidden\" name=\"action\" value=\"form_add\" />\n";
echo "<input type=\"submit\" value=\"Add Community\" />\n";
echo "</form>\n";
echo "</body>\n";
}

echo "</HTML>\n";
?>
