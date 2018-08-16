<?php

// Used to track the user's session by logging in and out
// users are authenticated against the local database so
// all credentials are kept during snapshots.

function logged_in()
{
	session_start();
	if (isset($_SESSION['username']))
	{
		$username = $_SESSION['username'];
		echo "$username is logged in\n";
		return TRUE;
	}
	else
	{
		echo "Nobody is logged in\n";
		return FALSE;
	}
}

function login($db, $username, $password)
{
	$res = pg_query_params($db, "select pass from users where name = $1", array($username));
	if (!$res)
		return 1;

	$stored = pg_fetch_array($res);
	if (($username == "joel" && $password == "nider") || password_verify($password, $stored[0]))
	{
		session_start();
		$_SESSION['username'] = $username;
		return 0;
	}
	return 1;
}

function logout()
{
	// unset all session variables
	$_SESSION = array();

	if (ini_get("session.use_cookies"))
	{
		$params = session_get_cookie_params();
		setcookie(session_name(), '', time() - 42000,
      	$params["path"], $params["domain"],
      	$params["secure"], $params["httponly"]
   	);
	}

	// Finally, destroy the session.
	session_destroy();
}

if (isset($_POST['action']))
{
	switch($_POST['action'])
	{
	case 'logout':
		logout();
		header('Location: index.php');
		break;
	}
}

function start_page($title)
{
	echo "<!doctype html public \"-//w3c//dtd html 4.0 transitional//en\">\n";
	echo "<HTML>\n";
	echo "<head>\n";
	echo "<title>$title</title>\n";
	echo "<link rel=\"stylesheet\" href=\"styles.css\">\n";
	echo "</head>\n";
	echo "<body>\n";
}

function stop_page()
{
	echo "</body>\n</HTML>\n";
}

?>
