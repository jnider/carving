<?php

include('login.php');

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

if (isset($_GET['action']))
{
	$page_action = $_GET['action'];
	$user_id = $_GET['user_id'];
}

switch($page_action)
{
case "logout":
	logout();
	header('Location: index.php');
	break;
}

?>
