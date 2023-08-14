<?php

include_once('login.php');

/****************************************/
// make sure the user is logged in
if (!is_logged_in() || !is_admin())
	header('Location: index.php');

// start output
$db = connect_to_db();
start_page("Admin");

echo "<H1>Administration</H1>\n";

// show some statistics
$num_items = count_items($db);
$num_collections = count_collections($db);
$total_purchase_price = sum_purchase_price($db);
$total_eval_price = eval_price($db);

echo "<h2>Statistics</h2>\n";
echo "<table>\n";
echo "<tr><td>There are $num_items entries in the database</tr>\n";
echo "<tr><td>There are $num_collections collections in the database</tr>\n";
echo "<tr><td>Total original purchase price: \$$total_purchase_price.00 </tr>\n";
echo "<tr><td>Currently evaluated price: \$$total_eval_price.00 </tr>\n";
echo "</table>\n";

echo "<h2>Content Management</h2>\n";
echo "<table>\n";
echo "<tr><td><a class='button' href=\"community.php?action=form_add\">Manage Communities</a></tr>\n";
echo "<tr><td><a class='button' href=\"art_type.php?action=form_add\">Manage Art Type</a></tr>\n";
echo "</table>\n";

echo "<h2>Database Maintenance</h2>\n";
echo "<table>\n";
echo "<tr><td><a class='button' href=\"user.php\">User Accounts</a></tr>\n";
echo "<tr><td><a class='button' href=\"?action=backup\">Back up database</a></tr>\n";
echo "<tr><td><a class='button' href=\"export.php?action=export_csv\">Export to CSV</a></tr>\n";
echo "</table>\n";

// figure out why the page was loaded
if (isset($_POST['action']))
{
	$page_action=$_POST['action'];
}
else if (isset($_GET['action']))
{
	$page_action = $_GET['action'];
}

if (isset($page_action) && is_admin())
{
	switch($page_action)
	{
	case "backup":
		$ret = shell_exec("./backup.sh");
		echo "<div class=response_ok>Backup written to $ret</div>\n";
		break;
	}
}

end_page();

?>
