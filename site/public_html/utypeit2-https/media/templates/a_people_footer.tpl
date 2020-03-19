<?php
$base = "<table id=\"bottom_buttons\" width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">
    <tr>";
/*if($_SESSION['user']->level > 7 && $_SESSION['user']->level >= $level && substr($action,-5) =='_edit') {
    if($action == 'customer_edit') {
        $delete_response = 'customers';
    } elseif($action == 'user_edit') {
        $delete_response = 'users';
    } elseif($action == 'contractor_edit') {
        $delete_response = 'contractors';
    }
    $base .= "<td  colspan='2' class='formInput'><button id='customer_edit_submit' onclick=\"sendDelete('".$person_id."','".$delete_response."')\">Delete</button></td>
    <td  colspan='2' class='formSubmit'><button type='submit' onclick=\"$('people_edit').submit();\">Save</button></td>";
} else {*/
//    if($_SESSION['user']->level > $level) {
        $base .= "<td  colspan='4' class='formSubmit'><button type='submit' onclick=\"$('people_edit').submit();\">Save</button></td>";
//    }
//}
 $base .=  "
    </tr>
</table>";

$contentFooter = $base;