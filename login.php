<?php

// Used to track the user's session by logging in and out
// users are authenticated against the local database so
// all credentials are kept during snapshots.

function logged_in()
{
	session_start();
	if (isset($_SESSION['username'])
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

function login()
{
	echo "Logging in\n";
}

?>
