<?php

include('login.php');
include('db.php');

// declare the functions

function show_form_add()
{
	echo "<form method=\"post\" action=\"community.php\">\n";
	echo "<input type=\"hidden\" name=\"action\" value=\"add\">\n";
	echo "Name:<input type=\"text\" name=\"name\">\n";
	echo "Alternative Name:<input type=\"text\" name=\"alt_name\"><BR>\n";
	echo "<input type=\"submit\" value=\"Add\">\n";
	echo "</form>\n";
}

function add($db, $name, $alt_name)
{
	$query = 'insert into community_lu (name, alt_name) values ($1, $2)';
	$res = pg_query_params($db, "insert into community_lu (name, alt_name) values ($1, $2)", array($name, $alt_name));
	if ($res == FALSE)
		return -1;
	return 0;
}

// make sure we're logged in

// start output
start_page("Communities");

// connect the database
$db = connect_to_db();
if ($db)
{
	// figure out why the page was loaded
	if (isset($_POST['action']))
	{
		$action=$_POST['action'];
		switch($action)
		{
		case "form_add":
			show_form_add();
			break;

		case "add":
			$community_name=$_POST['name'];
			$alt_name=$_POST['alt_name'];
			$ret=add($db, $community_name, $alt_name);
			if ($ret)
				echo "Community not added: $ret\n";
			break;

		default:
			echo "$action is not a valid action\n";
		}
	}
}
else
{
	echo "Can't connect to the database\n";
}

stop_page();

?>
