<?php

include 'db.php';

// declare the functions

function show_form_add()
{
	//echo "<head><title>Add community</title></head>\n";
	echo "<form method=\"post\" action=\"community.php\">\n";
	echo "<input type=\"hidden\" name=\"action\" value=\"add\">\n";
	echo "Name:<input type=\"text\" name=\"name\">\n";
	echo "Alternative Name:<input type=\"text\" name=\"alt_name\"><BR>\n";
	echo "<input type=\"submit\" value=\"Add\">\n";
	echo "</form>\n";
}

function add($db, $name, $alt_name)
{
	//echo "<body>\n";
	//echo "Adding community $name, $alt_name<BR>\n";
	$query = 'insert into community_lu (name, alt_name) values ($1, $2)';
	$res = pg_query_params($db, $query, array($name, $alt_name));
	//echo "</body>\n";
	if ($res == FALSE)
		return -1;
	return 0;
}

// start output
echo "<HTML>\n<body>\n";

// make sure we're logged in

// connect the database
//$db = connect_to_db();
	$conn_string = "host=carving.postgres.database.azure.com port=5432 user=dbuser@carving dbname=carving password=Asda67as";
	$db = pg_connect($conn_string);
if (!$db)
{
	echo "Can't connect to the database\n";
	goto done;
}

// figure out why the page was loaded
$action=$_POST['action'];
$community_name=$_POST['name'];
$alt_name=$_POST['alt_name'];

switch($action)
{
case "form_add":
	show_form_add();
	break;

case "add":
	$ret=add($db, $community_name, $alt_name);
	if (!$ret)
		echo "Community added ok\n";
	else
		echo "Community not added: $ret\n";
	break;

default:
	echo "$action is not a valid action\n";
}

done:
// end output
echo "</body>\n</HTML>\n";

?>
