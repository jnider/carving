<?php

echo "<!doctype html public \"-//w3c//dtd html 4.0 transitional//en\">\n";
echo "<HTML>\n";
echo "<head>\n";
echo "<title>Actions Menu</title>\n";
echo "</head>\n";
echo "<body>\n";
echo "<form method=\"post\" action=\"community.php\">\n";
echo "<input type=\"hidden\" name=\"action\" value=\"form_add\" />\n";
echo "<input type=\"submit\" value=\"Add Community\" />\n";
echo "</form>\n";
#echo "<a href=\"community.php?action=form_add\">Add Community</a><BR>\n";
echo "</body>\n";
echo "</HTML>\n";
?>
