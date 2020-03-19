<?php

$head = '
<div class="contentHeaderBlock" id="contentHeaderBlock">
    <div class="contentHeaderLeft">'.$title.'</div>
    <div class="contentHeaderRight">
        <ul>';
            if(!$demo) {
                if($action == 'message_compose') {
                $head .= '
            <li onclick="window.location=\''.$_SERVER['PHP_SELF'].'?action=send_message\'">Send Message</li>
            <li class="blank">&nbsp;</li>';
                }
                if($action == 'user_messages') {
                    $head .= '
            <li onclick="$(\'member_options\').submit();">Save Options</li>
            <li class="blank">&nbsp;</li>';
                }
            }
            $head .= '
            <li onclick="window.location=\''.$_SERVER['PHP_SELF'].'?action=contact_one\'">Contact One</li>
            <li onclick="window.location=\''.$_SERVER['PHP_SELF'].'?action=contact_many\'">Contact Many</li>
            <li onclick="window.location=\''.$_SERVER['PHP_SELF'].'?action=contact_cpi\'">Contact Support</li>';
        $head .= '
        </ul>
    </div>
</div>';

$contentHeader = $head;
?>