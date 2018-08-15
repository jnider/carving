<?php

function show_login_form()
{
echo "<head>\n";
echo "<title>Login</title>\n";
echo "</head>\n";
echo "<body>\n";
echo "Login required<BR>\n";
echo "<form method=\"post\" action=\"index.php\">\n";
echo "<input type=\"hidden\" name=\"action\" value=\"login\" />\n";
echo "Username: <input type=\"text\" name=\"username\" /><BR>\n";
echo "Password: <input type=\"password\" name=\"password\" /><BR>\n";
echo "<input type=\"submit\" value=\"Login\" />\n";
echo "</form>\n";
echo "</body>\n";
}

function login($username, $password)
{
	if ($username == "joel" && $password == "nider")
	{
		session_start();
		$_SESSION['username'] = $username;
		return 0;
	}
	return 1;
}

// check to see if a user is logging in
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

	echo "</body>\n";
}

echo "</HTML>\n";
?>
