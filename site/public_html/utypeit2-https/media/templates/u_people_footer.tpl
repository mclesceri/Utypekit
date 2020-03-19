<?php
$base = "<table id=\"bottom_buttons\" width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">
    <tr>";
    if($action != 'user_add') {
        $base .= '<td colspan="2"><button type="button" onclick="window.location=\'people_list.php\';return false;">Cancel</button></td>';
    }
    if(!$demo) {
    	if($_SESSION['user']->order_level >= $order_level) {
        	if($action != 'save') {
        	    $base .= "<td  colspan='2' class='formSubmit'><button type='submit' onclick=\"sendMe(event); return false;\">Save</button></td>";
			} else {
        	    $base .= "<td  colspan='2' class='formSubmit'>&nbsp;</td>";
			}
		}
    } else {
        $base .= '<td colspan="2">&nbsp;</td>';
    }
 $base .=  "
    </tr>
</table>";

$contentFooter = $base;