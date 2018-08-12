<?php

// declare the functions

function show_form_add()
{
	echo "<head><title>Add community</title></head>\n";
	echo "<body>\n";
	echo "<form method=\"post\">\n";
	echo "<input type=\"hidden\" value=\"action\" name=\"add\" />\n";
	echo "Name:<input type=\"text\" name=\"name\">\n";
	echo "Alternative Name:<input type=\"text\" name=\"alt_name\">\n";
	echo "<input type=\"submit\" value=\"Add\">\n";
	echo "</form>\n";
	echo "</body>\n";
}

function add($name, $alt_name)
{
	echo "<body>\n";
	echo "Adding community $name, $alt_name<BR>\n";
	echo "</body>\n";
	return 0;
}

// start output
echo "<HTML>\n";

// figure out why the page was loaded
$action=$_POST['action'];

switch($action)
{
case "form_add":
	show_form_add();
	break;

case "add":
	$community_name=$_POST['name'];
	$alt_name=$_POST['alt_name'];
	$ret=add($community_name, $alt_name);
	if ($ret==0)
		echo "Community added ok\n";
	else
		echo "Community not added: $ret\n";
	break;

default:
	echo "No action selected\n";
}

// end output
echo "</HTML>\n";

?>
