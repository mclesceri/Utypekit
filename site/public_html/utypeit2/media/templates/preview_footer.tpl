<?php
$base = "<div id=\"bottom_buttons\">\n";
if(!$demo) {
$base .= "
        <input type=\"submit\" name=\"save_bottom\" id=\"save_bottom\" value=\"Save\" onclick=\"_saveRecipe(); return false;\" />";
}
$base .= "
        <input type=\"button\" name=\"cancel_bottom\" id=\"cancel_bottom\" value=\"Close\" onclick=\"closeMe();\" />\n
</div>\n";

$contentFooter = $base;
?>