<?php
$base = "
<table id=\"bottom_buttons\" width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">
    <tr>
        <td>&nbsp;</td>
    </tr>
    <tr>";
    if(!$demo) {
        if(!$action) {
            $base .= "
        <td>&nbsp;</td>";
        }
        if($action == 'message_compose') {
            $base .= "
        <td class='formSubmit'><button type='submit' onclick=\"$('message_send').submit();\">Send Message</button></td>";
        }
        if($action == 'user_messages') {
            $base .= "
        <td class='formSubmit'><button type='submit' onclick=\"$('member_options').submit();\">Save User Options</button></td>";
        }
    } else {
        $base .= "
        <td>&nbsp;</td>";
    }
 $base .=  "
    </tr>
</table>";

$contentFooter = $base;