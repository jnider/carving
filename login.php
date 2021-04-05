<?php

// Used to track the user's session by logging in and out
// users are authenticated against the local database so
// all credentials are kept during snapshots.

function show_login_form()
{
echo <<< HTML
	<div id="instructions">
		Enter your username and password
	</div>

	<div class="center">
		<form method="post">
		<input type="hidden" name="action" value="login" />
		<table>
			<tr><td>Username:<td><input type="text" name="username" /></tr>
			<tr><td>Password:<td><input type="password" name="password" /></tr>
		</table>
		<input type="submit" value="Login" />
		</form>
	</div>
HTML;
}

function login($db, $username, $password)
{
	$query = "select * from users where name = '$username'";
	$res = pg_query($db, $query);
	if (!$res)
	{
		//$err = pg_last_error();
		return false;
	}

	$stored = pg_fetch_assoc($res);
	if (!$stored)
		return false;
	if (password_verify($password, $stored['pass']))
	{
		session_start();
		$_SESSION['username'] = $username;
		$_SESSION['privilege'] = $stored['write'];
		return true;
	}
	return false;
}

function start_page($title)
{
echo <<< HTML
	<!doctype html public "-//w3c//dtd html 4.0 transitional//en">
	<HTML>
	<head>
HTML;

	echo "<title>$title</title>\n";

echo <<< HTML
	<link rel="stylesheet" href="styles.css">
	</head>
	<body>
	<div id='wrapper'>
HTML;

	menu();
}

function end_page()
{
echo <<< HTML
	</div>
	</body>\n</HTML>
HTML;
}

function is_logged_in()
{
	session_start();
	if (isset($_SESSION['username']))
	{
		$username = $_SESSION['username'];
		return TRUE;
	}
	return FALSE;
}

// figure out what we are supposed to do
if (isset($_POST['action']))
{
	$page_action = $_POST['action'];
	$user_id = $_POST['user_id'];
}

	switch($page_action)
	{
	case "login":
		$db = connect_to_db();
		if ($db && login($db, $_POST['username'], $_POST['password']))
				header('Location: index.php');
		else
		{
			start_page("Login");
			echo "ERROR: can't log in<BR>";
			end_page();
		}
		break;
	}

?>
