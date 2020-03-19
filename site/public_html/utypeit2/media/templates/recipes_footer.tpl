<?php
$base = "
<table id=\"bottom_buttons\" width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">\n
    <tr>\n";
if ($action == 'recipe_edit') {
    if ($_SESSION['user'] -> level > 7) {
        $base .= "
        <td><button name=\"delete_bottom\" id=\"delete_bottom\" onclick=\"sendDelete('".$recipe_id."'); return false;\">Delete</button></td>";
    }
}
if(!$demo) {
$base .= "
        <td><button name=\"preview_bottom\" id=\"preview_bottom\" onclick=\"sendPreview(); return false;\">Preview</button></td>
        <td><button name=\"save_andadd_bottom\" id=\"save_andadd_bottom\" onclick=\"_saveAndAdd(this,'".$recipe_id."'); return false;\">Save and Add Another</button></td>
        <td><button name=\"save_bottom\" id=\"save_bottom\" onclick=\"_saveRecipe(this,'".$recipe_id."'); return false;\">Save</button></td>
    ";
}
$base .= "
    </tr>\n
</table>\n";

$contentFooter = $base;
?>