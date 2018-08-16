<?php

include('login.php');
include('db.php');

/*
book_id        | integer          |           |          |
 art_type       | integer          |           |          |
 community      | integer          |           |          |
 region         | integer          |           |          |
 height         | double precision |           |          |
 width          | double precision |           |          |
 depth          | double precision |           |          |
 purchase_price | double precision |           |          |
 appraisal      | double precision |           |          |
 current_price  | double precision |           |          |
 purchase_year  | integer          |           |          |
 appraisal_year | integer          |           |          |
 id             | integer          |           | not null | nextval('art_id_seq'::regclass)
 reg_tag        | text             |           |          |
*/

function show_form_add_art($db)
{
	// now output the form
	echo "<form method=\"post\">\n";
	echo "<input type=\"hidden\" name=\"action\" value=\"add\">\n";
	echo "Material:<input type=\"text\" name=\"material\"><BR>\n";
	echo "Artist:<input type=\"text\" name=\"artist\"><BR>\n";
	echo "Description:<input type=\"text\" name=\"description\"><BR>\n";

	// get list of communitites
	$res = pg_query($db, 'select * from community_lu'))
	if (!$res)
	{
		echo "Error building community list";
		return FALSE;
	}
	echo "Community:<select name=\"community\">";
	while ($community = pg_fetch_assoc($res))
	{
		$id = $community['id'];
		$name = $community['name'];
		echo "<option value=\"$id\">$name\n";
	}
	echo "</select>\n";
	echo "<input type=\"submit\" value=\"Add\">\n";
	echo "</form>\n";
}

// start output
start_page("User Management");
menu();

// figure out why the page was loaded
if (isset($_POST['action']))
{
	$db = connect_to_db();
	switch($_POST['action'])
	{	
	case "show":
		break;

	case "form_add":
		show_form_add_art($db);
		break;

	case "add":
		{
			if ($db)
			{
			}
			else
			{
				echo "Can't connect to the database\n";
			}
		}
		break;

default:
	echo "$action is not a valid action\n";
	}
}

// end output
stop_page();

?>

