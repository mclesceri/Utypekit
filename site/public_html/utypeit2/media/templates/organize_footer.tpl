<?php
$base = "
<table id=\"bottom_buttons\" width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">\n
    <tr>\n";
$base .= "
        <td><button name=\"refresh_bottom\" id=\"refresh_bottom\" onclick=\"window.location = 'recipe_organize.php?id=".$order_id."\" >Refresh</button></td>\n";
        if(!$demo) {
            $base .= "
        <td><button name=\"save_bottom\" id=\"save_bottom\" onclick=\"saveOrganize();\" >Save</button></td>";
        } else {
            $base .= "
        <td>&nbsp;</td>";
        }
    $base .= "</tr>\n
</table>\n";

$contentFooter = $base;
?>