<?php

include('login.php');

function show_controls($filter)
{
	echo "<h2>Filter Controls</h2>\n";
	echo "<form name='filter_form' autocomplete='off'>\n";
	echo "<input type=hidden name='action' value='filter'>\n";
	echo "<table>\n";
	echo "\n";
	echo "<tr><td>Artist Name<td><div class='autocomplete' style='width:300px;'><input type='text' id='artist' name='artist' placeholder='artist name' value=\"${filter['artist']}\"></div></tr>\n"; 
	echo "<tr><td>Pictures<td><input type=checkbox id='picture' class='picture' name='picture' ${filter['picture']}>With pictures</input>\n";
	echo "<input type=checkbox id='no_picture' name='no_picture' ${filter['no_picture']}>Without pictures</input></tr>\n";
	echo "</table>\n";
	echo "</form>\n";

	// show a list of active filter criteria
	echo "<div class='active_filters'>\n";
	if ($filter['picture'] == "checked")
		echo "<div class='filter' onclick=\"cb_uncheck('picture');\">With pictures &#x2715</div>\n";
	if ($filter['no_picture'] == "checked")
		echo "<div class='filter' onclick=\"cb_uncheck('no_picture');\">Without pictures &#x2715</div>\n";
	if (isset($filter['artist']))
		echo "<div class='filter' onclick=\"remove_filter('artist');\">${filter['artist']} &#x2715</div>\n";
	echo "</div>\n";
}

/****************************************/
// make sure the user is logged in
if (!is_logged_in())
	header('Location: index.php');

// start output
$db = connect_to_db();
start_page("Search Artwork");

// figure out why the page was loaded
if (isset($_GET['action']))
{
	$page_action = $_GET['action'];
}

if (isset($page_action))
{
	switch($page_action)
	{	
	case "filter":
		if (isset($_GET['picture']) && $_GET['picture'] == "on")
			$filter['picture'] = "checked";
		if (isset($_GET['no_picture']) && $_GET['no_picture'] == "on")
			$filter['no_picture'] = "checked";
		if (isset($_GET['artist']) && $_GET['artist'] != '')
			$filter['artist'] = $_GET['artist'];
		break;
	}
}

// show filter controls
show_controls($filter);

// display results in a grid
$items = get_art($db);

$artists = get_artists($db);

echo "<h2>Results</h2>\n";
echo "<div class='grid'>\n";
foreach ($items as &$item)
{
	$url = '';
	$tag = '';

	// get the first picture (if there is one)
	$pictures = get_pictures($db, $item['id']);
	if ($pictures != FALSE)
		$url = $pictures[0][url];

	// apply filters
	if ($filter['picture'] == "checked" && $pictures == FALSE)
		continue;

	if ($filter['no_picture'] == "checked" && $pictures != FALSE)
		continue;

	// check for an exact match with the artist name
	if (isset($filter['artist']) && $item['artist'] != $filter['artist'])
		continue;

	// check if there is a tag id
	$tag = $item['reg_tag'];
	$id_line = "ID: ${item['book_id']}";
	if ($tag != NULL)
		$id_line .= " (tag $tag)";
	else
		$id_line .= " (no tag)";
	$id_line .= "<BR>\n";
	
	echo "<a class='item' href=art.php?action=show&item_id=${item['id']}>\n";
	// wrap each item in a div
	echo "<div class='art_thumb'>\n";
	if ($url != '')
		echo "<img width=200px src=pictures/$url></img><BR>\n";
	echo $id_line;
	echo "Artist:${item['artist']}";
	echo "</div></a>\n";
}
echo "</div>\n";

// scripts
echo <<< HTML
<script type="text/javascript">
var all_artists=[''];

// submit the filter from js
var filter_submit = function() {
	document.filter_form.submit();
}

// uncheck this checkbox and resubmit the form
var cb_uncheck = function(element_id) {
	document.getElementById(element_id).checked = false;
	document.filter_form.submit();
}

function remove_filter(element_id) {
	document.getElementById(element_id).value = '';
	document.filter_form.submit();
}

// set the onclick handler without inline js
document.getElementById('picture').onclick = filter_submit;
document.getElementById('no_picture').onclick = filter_submit;

HTML;

// generate the js array of artists dynamically from PHP
foreach ($artists as &$artist)
	echo "all_artists.push('$artist');\n";

echo <<< HTML
	/*
		borrowed from w3schools.com
		the autocomplete function takes two arguments, the text field element and
		an array of possible autocompleted values
	*/
function autocomplete(inp, arr)
{
	var currentFocus;

	/* execute a function when someone writes in the text field */
	inp.addEventListener("input", function(e)
	{
      var a, b, i, val = this.value;

      /* close any already open lists of autocompleted values */
      closeAllLists();
      if (!val)
			return false;
      currentFocus = -1;

      /* create a DIV element that will contain the items (values) */
      a = document.createElement("DIV");
      a.setAttribute("id", this.id + "autocomplete-list");
      a.setAttribute("class", "autocomplete-items");
      /*append the DIV element as a child of the autocomplete container:*/
      this.parentNode.appendChild(a);

		// search for a match
      for (i = 0; i < arr.length; i++)
		{
			/*check if the item starts with the same letters as the text field value:*/
			if (arr[i].substr(0, val.length).toUpperCase() == val.toUpperCase())
			{
				// create a div for each match
				b = document.createElement("DIV");
				b.innerHTML = "<strong>" + arr[i].substr(0, val.length) + "</strong>";
				b.innerHTML += arr[i].substr(val.length);

				// insert a input field that will hold the current array item's value
				b.innerHTML += "<input type='hidden' value='" + arr[i] + "'>";
				/*execute a function when someone clicks on the item value (DIV element):*/
				b.addEventListener("click", function(e)
				{
					// insert the value and submit the form to update the filter
					inp.value = this.getElementsByTagName("input")[0].value;
					closeAllLists();
					document.filter_form.submit();
				});
				a.appendChild(b);
			}
		}
  });

	// keypress
  inp.addEventListener("keydown", function(e) {
      var x = document.getElementById(this.id + "autocomplete-list");
      if (x) x = x.getElementsByTagName("div");
      if (e.keyCode == 40) {
        /*If the arrow DOWN key is pressed,
        increase the currentFocus variable:*/
        currentFocus++;
        /*and and make the current item more visible:*/
        addActive(x);
      } else if (e.keyCode == 38) { //up
        /*If the arrow UP key is pressed,
        decrease the currentFocus variable:*/
        currentFocus--;
        /*and and make the current item more visible:*/
        addActive(x);
      } else if (e.keyCode == 13) {
        /*If the ENTER key is pressed, prevent the form from being submitted,*/
        e.preventDefault();
        if (currentFocus > -1) {
          /*and simulate a click on the "active" item:*/
          if (x) x[currentFocus].click();
        }
      }
  });
  function addActive(x) {
    /*a function to classify an item as "active":*/
    if (!x) return false;
    /*start by removing the "active" class on all items:*/
    removeActive(x);
    if (currentFocus >= x.length) currentFocus = 0;
    if (currentFocus < 0) currentFocus = (x.length - 1);
    /*add class "autocomplete-active":*/
    x[currentFocus].classList.add("autocomplete-active");
  }
  function removeActive(x) {
    /*a function to remove the "active" class from all autocomplete items:*/
    for (var i = 0; i < x.length; i++) {
      x[i].classList.remove("autocomplete-active");
    }
  }

    /*close all autocomplete lists in the document,
    except the one passed as an argument:*/
  function closeAllLists(elmnt) {
    var x = document.getElementsByClassName("autocomplete-items");
    for (var i = 0; i < x.length; i++) {
      if (elmnt != x[i] && elmnt != inp) {
      x[i].parentNode.removeChild(x[i]);
    }
  }
}

// Cancel all autocompletes when someone clicks in the document
document.addEventListener("click", function (e) {
    closeAllLists(e.target);
});
}

autocomplete(document.getElementById("artist"), all_artists);
</script>
HTML;

// end output
stop_page();

?>
