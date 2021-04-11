<?php

include('db.php');

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

	// don't show the menu if the user is not logged in
	if (isset($_SESSION['username']))
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
	if (!session_id())
		session_start();
	if (isset($_SESSION['username']))
	{
		$username = $_SESSION['username'];
		return TRUE;
	}
	return FALSE;
}

/**
	Does the current user have admin privileges
*/
function is_admin()
{
	if (isset($_SESSION['username']) && $_SESSION['privilege'] == 1)
		return TRUE;

	return FALSE;
}

$db = connect_to_db();

// figure out what we are supposed to do
if (isset($_POST['action']))
{
	$page_action = $_POST['action'];
}

if (isset($page_action))
{
	switch($page_action)
	{
	case "login":
		if ($db && login($db, $_POST['username'], $_POST['password']))
		{
				$login_failed = false;
				header('Location: index.php');
		}
		else
		{
			$login_failed = true;
		}
		break;
	}
}

?>
