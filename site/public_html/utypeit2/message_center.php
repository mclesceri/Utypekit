<?php

session_start();

if(!$_SESSION['login'] == true) {
    die;
}

$page = 'message_center';
$title = "Message Center";
$header_left = '';
$header_middle = $_SESSION['order_title'];
$header_right = '';

require_once('src/globals.php');

require_once(SERVICES.'BaseService.php');
$nb = new BaseService();
require_once(SERVICES.'Orders.php');
$no = new Orders();

require_once(INCLUDES."Warnings.php");

$action = '';
if(isset($_REQUEST['action'])) {
    $action = $_REQUEST['action'];
}

$order_id = $_SESSION['order_id'];
$demo = false;
if($order_id == 1) {
    $demo = true;
}

switch($action) {
    case 'message_send':
        require_once(SERVICES."Email.php");

        $ne = new Email();
        
        $tmp = $_POST['message'];
        $message = $_POST['sender_name'].'<br />';
        $message .= $_POST['sender_email'].'<br />';
        $message .= 'Order #: '.$_SESSION['order_number'].'<br />';
        $message .= stripslashes($tmp);
        
        $_POST['message'] = $message;
        
        $res = $ne->_mail($_POST);
        if($res != true) {
            $out = $res;
        } else {
            $out = "<div style=\"margin: 10px 0 0 0;\">Your message has been sent.</div>";
        }
        
        $title = "Message Delivered";
        $script = "document.observe('dom:loaded',function() {
            fancyNav();
        });";
        require_once(TEMPLATES.'message_header.tpl');
        break;
    case 'save_options':
        // attempt to get the meta for this order...
        $query = 'SELECT id,value from Order_Meta WHERE order_id="'.$order_id.'" AND name="utypeit_info"';
        $res = $nb->sendAndGetOne($query);
        $meta_id = null;
        $uti_data = array();
        if($res) {
            $new_meta = '';
            $meta_id = $res->id;
            $array = explode('|',$res->value);
            foreach($array AS $a) {
               $sub = explode(':',$a);
                if($_POST[$sub[0]] != '') {
                    $val = $_POST[$sub[0]];
                    $new_meta .= $sub[0].':'.urlencode($val).'|';
                } else {
                    $val = 0;
                    $new_meta .=$sub[0].':0|';
                }
                $uti_data[$sub[0]] = $val;
            }
            $new_meta = substr($new_meta, 0, -1);
        } else {
            // an array of all possible utypeit_info values...
            $all = array('recipe_deadline','max_recipes_ea','welcome_note','max_recipes','recipe_note');
            $new_meta = '';
            foreach($all AS $a) {
                $match = false;
                foreach($_POST AS $key=>$val) {
                    if($key == $a) {
                        $match = true;
                        $uti_data[$a] = $val;
                        $new_meta .= $a.':'.urlencode($val).'|';
                    }
                }
                if(!$match) {
                    $new_meta .= $a.':0|';
                    $uti_data[$a] = 0;
                }
            }
            $new_meta = substr($new_meta, 0, -1);
        }
        $date_modified = date('Y-m-d H:i:s');
        if($meta_id) {
            $query = 'UPDATE Order_Meta SET date_modified="'.$date_modified.'",value="'.$new_meta.'" WHERE id="'.$meta_id.'"';
            $nb->sendAndGetOne($query);
        } else {
            $query = 'INSERT INTO Order_Meta (date_modified,order_id,name,value) VALUES ("'.$date_modified.'","'.$order_id.'","utypeit_info","'.$new_meta.'")';
            $nb->insertAndGetOne($query);
        }
        
        $_SESSION['utypeit_info'] = $uti_data;
        $_SESSION['warning'] = null;
        header('Location: '.$_SERVER['PHP_SELF'].'?action=user_messages');
        require_once(TEMPLATES.'u_people_header.tpl');
        break;
    case 'contact_cpi':
        $title = 'Contact Cookbook Publishers Support';
        $script = "document.observe('dom:loaded',function() {
            fancyNav();
        });";
        $out .= "
        <form id=\"contact_cpi\" action=\"".$_SERVER['PHP_SELF']."?action=message_send\" method=\"POST\">
        <div id=\"feedback\">&nbsp;</div>
        <table width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"4\">
        <table width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"4\">
        <input type=\"hidden\" name=\"sender_name\" value=\"".stripslashes(urldecode($_SESSION['user']->first_name))." ".stripslashes(urldecode($_SESSION['user']->last_name))."\">
        <input type=\"hidden\" name=\"sender_email\" value=\"".stripslashes(urldecode($_SESSION['user']->email))."\">
        <input type=\"hidden\" name=\"recipient_email\" value=\"CPI Info <info@dev.cbp.ctcsdev.com>\">";
        $out .= "<tr>
            <td class=\"formLabel\">Sender:</td>
            <td class=\"formInput\">".stripslashes(urldecode($_SESSION['user']->first_name))." ".stripslashes(urldecode($_SESSION['user']->last_name))."</td>
        </tr>
        <tr>
            <td class=\"formLabel\">Reply To:</td>
            <td class=\"formInput\"><input type=\"text\" name=\"reply_to\" id=\"reply_to\" value=\"";
            if(isset($_SESSION['user']->email)) { 
                $out .= stripslashes(urldecode($_SESSION['user']->email)); 
            } else {
                $out .= $generic_reply_to;
            } 
            $out .= "\" /></td>
        </tr>
        <tr>
            <td class=\"formLabel\">Subject:</td>
            <td class=\"formInput\"><input type=\"text\" name=\"subject\" id=\"subject\" size=\"40\"></td>
        </tr>
        <tr>
            <td class=\"formLabel\" style=\"vertical-align: top; padding-top: 5px\">Message:</td>
            <td class=\"formInput\">
            
            <textarea name=\"message\" id=\"message\" cols=\"80\" rows=\"20\" style=\"border: 1px #333333 solid\">Some sample text</textarea>
            </td>
        </tr>
        <tr>
            <td  colspan=\"2\" class=\"formSubmit\"><button type=\"submit\" id=\"send_email\">Send</button></td>
        </tr>
        </table>
        </form>";
        require_once(TEMPLATES.'message_header.tpl');
        break;
    case 'contact_one':
        $title = 'Contact a Member';
        $script = "
        function setRecipientList(type) {
            var url = window.includes + 'process_list.php?type=recipient&switch=' + type;
            $('recipientTD').update('');
            var b = new Ajax.Request(url,{ onSuccess: function (transport){ var data = transport.responseText; $('recipientTD').update(data); } });
        }
        
        document.observe('dom:loaded', function() {
                
            fancyNav();
            
            $('contact_one').observe('submit',function(event){
            	Event.stop(event);
            	var count = 0;
            	$('recipient').select('option').each(function(ea){
            		if(ea.selected) {
            			count = count + 1;
            		}
            	});
            	if(!count) {
            		alert('Please select at lease one recipient from the list before continuing.');
            	} else {
            		$('contact_one').submit();
            	}
            });
            
            $('contact_group').observe('change', function(event){
                var select = $( 'contact_group' );
                var val = select.selectedIndex >=0 && select.selectedIndex ? select.options[select.selectedIndex].value : undefined;
                setRecipientList(val);
            })
        });
        ";
        $out = "<form id=\"contact_one\" action=\"".$_SERVER['PHP_SELF']."\" method=\"POST\">
        <input type=\"hidden\" name=\"action\" value=\"message_compose\">
        <input type=\"hidden\" name=\"sender\" value=\"contact_one\">
        <div id=\"feedback\">&nbsp;</div>
        <table width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"4\">
            <tr>
                <td class=\"formLabel\" style=\"width: 40%\">Recipient Group:</td>
                <td class=\"formInput\">
                    <select name=\"contact_group\" id=\"contact_group\">
                        <option value=\"\"> -- </option>
                        <option value=\"2\">Contributors</option>
                        <option value=\"3\">Committee</option>
                        <option value=\"4\">Cochair</option>
                        <option value=\"5\">Chair</option>
                    </select>
                    </td>
            </tr>
            <tr>
                <td class=\"formLabel\" style=\"width: 40%\">Message Recipient:</td>
                <td class=\"formInput\" id='recipientTD'>
                    <select name=\"recipient\" id=\"recipient\" class=\"disabled\" disabled=\"disabled\"></select>
                </td>
            </tr>
            <tr>
                <td  colspan=\"2\" class=\"formSubmit\"><button type=\"submit\" id=\"compose_email\">Compose Message</button></td>
            </tr>
        </table>
        </form>";
        require_once(TEMPLATES.'message_header.tpl');
        break;
    case 'contact_many':
        $title = 'Contact Selected Members';
        $script = "
        function setRecipientList(type) {
            var url = window.includes + 'process_list.php?type=recipient&switch=' + type + '&value=multiple';
            $('recipientTD').update('');
            var b = new Ajax.Request(url,{ onSuccess: function (transport){ var data = transport.responseText; $('select_all').enable(); $('recipientTD').update(data); $('recipient[]').setAttribute('style','width: 150px; height: 200px'); } });
        }
        
        document.observe('dom:loaded', function() {
            
            fancyNav();
            
            $('contact_many').observe('submit',function(event){
            	Event.stop(event);
            	var count = 0;
            	$('recipient').select('option').each(function(ea){
            		if(ea.selected) {
            			count = count + 1;
            		}
            	});
            	if(!count) {
            		alert('Please select at lease one recipient from the list before continuing.');
            	} else {
            		$('contact_many').submit();
            	}
            });
            
            $('select_all').observe('click',function(){
            	if($('select_all').checked) {
            		$('recipient').select('option').each(function(ea){ea.selected = 'selected';});
            	} else {
            		$('recipient').select('option').each(function(ea){ea.selected = '';});
            	}
            });
            
            $('contact_group').observe('change', function(event){
                var select = $( 'contact_group' );
                var val = select.selectedIndex >=0 && select.selectedIndex ? select.options[select.selectedIndex].value : undefined;
                setRecipientList(val);
            })
        });";
        $out = "<form id=\"contact_many\" action=\"".$_SERVER['PHP_SELF']."\" method=\"POST\">
        <input type=\"hidden\" name=\"action\" value=\"message_compose\">
        <input type=\"hidden\" name=\"sender\" value=\"contact_many\">
        <div id=\"feedback\"></div>
        <p>To send a message to many users, first use the <strong>Recipient Group</strong> select to designate<br /> the group. When the list of names appears in the box below, choose the members of<br />that group by clicking on their name. To select multiple people, use either Control+Click<br />(Command + Click on the Mac), or use the <strong>Select All</strong> button to select everyone.</p>
        <table width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"4\">
            <tr>
                <td class=\"formLabel\" style=\"width: 40%\">Recipient Group:</td>
                <td class=\"formInput\">
                    <select name=\"contact_group\" id=\"contact_group\">
                        <option value=\"\"> -- </option>
                        <option value=\"0\">All Members</option>
                        <option value=\"2\">Contributors</option>
                        <option value=\"3\">Committee</option>
                        <option value=\"4\">Cochair</option>
                        <option value=\"5\">Chair</option>
                    </select>
                </td>
            </tr>
            <tr>
            	<td style=\"text-align: center;\" colspan=\"2\">Select All <input type=\"checkbox\" id=\"select_all\" disabled=\"disabled\"></td>
            </tr>
            <tr>
                <td class=\"formLabel\" style=\"width: 40%; vertical-align: top; padding-top: 5px;\">Message Recipients:</td>
                <td class=\"formInput\" id='recipientTD' style=\"height: 208px \">
                    <select name=\"recipient\" id=\"recipient\" multiple=\"multiple\" style=\"width: 150px; height: 200px\">
                </td>
            </tr>
            <tr>
                <td  colspan=\"2\" class=\"formSubmit\"><button type=\"submit\" id=\"compose_email\" >Compose Message</button></td>
            </tr>
        </table>
        </form>";
        require_once(TEMPLATES.'message_header.tpl');
        break;
    case 'message_compose':
        $title = "Message Compose";
        $script = "document.observe('dom:loaded',function() {
            fancyNav();
        });";
        $out .= "
        <form id=\"message_send\" action=\"".$_SERVER['PHP_SELF']."?action=message_send\" method=\"POST\">
        <p>PLEASE NOTE: This email transmission will be sent from UTypeIt 2 Mailer&lt;no-reply@dev.cbp.ctcsdev.com&gt;. However, members may still use the \"Reply To:\" function of their email program to respond to you. Please be sure that the \"Reply To\" address below is the email where you want your members to respond.</p>
        <div id=\"feedback\">&nbsp;</div>
        <table width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"4\">
        <input type=\"hidden\" name=\"sender_name\" value=\"".stripslashes(urldecode($_SESSION['user']->first_name))." ".stripslashes(urldecode($_SESSION['user']->last_name))."\">
        <input type=\"hidden\" name=\"sender_email\" value=\"".stripslashes(urldecode($_SESSION['user']->email))."\">";
        $out .= "<tr>
            <td class=\"formLabel\">Sender:</td>
            <td class=\"formInput\">".stripslashes(urldecode($_SESSION['user']->first_name))." ".stripslashes(urldecode($_SESSION['user']->last_name))."</td>
        </tr>
        <tr>
            <td class=\"formLabel\">Reply To:</td>
            <td class=\"formInput\"><input type=\"text\" name=\"reply_to\" id=\"reply_to\" value=\"";
            if(isset($_SESSION['user']->email)) { 
                $out .= stripslashes(urldecode($_SESSION['user']->email)); 
            } else {
                $out .= $generic_reply_to;
            } 
            $out .= "\" /></td>
        </tr>
        <tr>
            <td class=\"formLabel\" style=\"vertical-align: top; padding-top: 5px\">Recipient:</td>";
            $rec = $_REQUEST['recipient'];
            $rec_string = '';
            if(is_array($rec)) {
                for($r=0;$r<count($rec);$r++) {
                    $res = $nb->sendAndGetOne("SELECT first_name,last_name,email FROM People WHERE id='".$rec[$r]."'");
                    $rec_string .= stripslashes(urldecode($res->first_name)).' '.stripslashes(urldecode($res->last_name)).' <'.stripslashes(urldecode($res->email)).'>';
                    if($r < (count($rec)-1)) {
                        $rec_string .= ',';
                    }
                }
            } else {
                $res = $nb->sendAndGetOne("SELECT first_name,last_name,email FROM People WHERE id='".$rec."'");
                $rec_string .= stripslashes(urldecode($res->first_name)).' '.stripslashes(urldecode($res->last_name)).' <'.stripslashes(urldecode($res->email)).'>';
            }
            $out .= "<td class=\"formInput\"><input type=\"text\" name=\"recipient_email\" id=\"recipient_email\" value=\"".$rec_string."\" size=\"40\">&nbsp;<a href=\"".$_SERVER['PHP_SELF']."?action=".$_POST['sender']."\">Choose Another Recipient</a></td>
        </tr>
        <tr>
            <td class=\"formLabel\">Subject:</td>
            <td class=\"formInput\"><input type=\"text\" name=\"subject\" id=\"subject\" size=\"40\"></td>
        </tr>
        <tr>
            <td class=\"formLabel\" style=\"vertical-align: top; padding-top: 5px\">Message:</td>
            <td class=\"formInput\">
            <textarea name=\"message\" id=\"message\" cols=\"80\" rows=\"10\" style=\"border: 1px #333333 solid\">Some sample text</textarea>
            </td>
        </tr>
        </table>
        </form>";
        require_once(TEMPLATES.'message_header.tpl');
        break;
    case 'user_messages':
    		$title = 'User Messages for Order #'.$_SESSION['order_number'];
            $meta = $nb->sendAndGetOne('SELECT id,value FROM Order_Meta WHERE name="utypeit_info" AND order_id="'.$_SESSION['order_id'].'"');
            $meta_arr = explode('|',$meta->value);
            $meta_data = array();
            foreach($meta_arr AS $d) {
                $data = explode(':',$d);
                $meta_data[$data[0]] = $data[1];
            }
            foreach($meta_data AS $key=>$val) {
                if($val) {
                    $$key = stripslashes(urldecode($val));
                } else {
                    $$key = '';
                }
            }
            $script = "
                    function setupCalendars() {
                        // Popup Calendar
                        Calendar.setup( {dateField: \"recipe_deadline\",triggerElement: \"calendar\"});
                    }
    
                    document.observe(\"dom:loaded\", function() {
                        fancyNav();
                        setupCalendars();
                    });";
                $out = '
                <form name="member_options" id="member_options" action="'.$_SERVER['PHP_SELF'].'" method="POST">
                <input type="hidden" name="action" value="save_options">
                <input type="hidden" name="id" value="'.$meta->id.'">
                <table width="100%" border="0" cellpadding="0" cellspacing="0">
                    <tr>
                        <td class="formLabel" style="width: 40%">Recipe Entry Deadline: </td>
                        <td class="formInput"><input type="text" name="recipe_deadline" id="recipe_deadline" size="10" value="'.$recipe_deadline.'"> <img src="'.IMAGES.'calendar.png" border="0" id="calendar"></td>
                    </tr>
                    <tr>
                        <td class="formLabel" style="width: 40%">Max Recipes: </td>
                        <td class="formInput"><input type="text" size="10" name="max_recipes" id="max_recipes" value="'.$max_recipes.'">&nbsp;<a href="'.HELP.'max_recipes.html"  title="Max Recipes" class="lightwindow help" params="lightwindow_width=500,lightwindow_height=300">?</a></td>
                    </tr>
                    <tr>
                        <td class="formLabel" style="width: 40%">Max Recipes Per User: </td>
                        <td class="formInput"><input type="text" size="10" name="max_recipes_ea" id="max_recipes_ea" value="'.$max_recipes_ea.'">&nbsp;<a href="'.HELP.'max_recipes_ea.html" title="Max Recipes Each" class="lightwindow help" params="lightwindow_width=500,lightwindow_height=300">?</a></td>
                    </tr>
                    <tr>
                        <td colspan="2">&nbsp;</td>
                    </tr>
                    <tr>
                        <td class="formLabel" style="width: 40%">Welcome message to users: &nbsp;<a href="'.HELP.'welcome_message.html" title="Welcome Message" class="lightwindow help" params="lightwindow_width=500,lightwindow_height=300">?</a></td>
                        <td class="formInput">&nbsp;</td>
                    </tr>
                    <tr>
                        <td colspan="2" class="formCenter"><textarea name="welcome_note" id="welcome_note" cols="80" rows="8">'.$welcome_note.'</textarea></td>
                    </tr>
                    <tr>
                        <td class="formLabel" style="width: 40%">Recipe entry note to users: &nbsp;<a href="'.HELP.'recipe_note.html" class="lightwindow help" title="Recipe Note" params="lightwindow_width=500,lightwindow_height=300">?</a></td>
                        <td class="formInput">&nbsp;</td>
                    </tr>
                    <tr>
                        <td colspan="2" class="formCenter"><textarea name="recipe_note" id="recipe_note" cols="80" rows="8">'.$recipe_note.'</textarea></td>
                    </tr>
                </table>
                </form>
            ';
		require_once(TEMPLATES.'u_people_header.tpl');
		break;
    default:
        $script = "document.observe('dom:loaded',function(){
            fancyNav();
        });";
        $title = 'Message Center';
        $out = '
                    <p><strong>Welcome to the Message Center</strong></p>
                    <p>Select from the options above to send messages to the users associated with this order, or to contact support for help.</p>';
                    if($_SESSION['user']->order_level > 3) {
                        $out .= '
                    <p></p>
		<p>To modify the messages your users see when they log into your account, select "Edit My Info" button on the left navigation pane. Then select the "User Messages" tab at the top of the page and edit the copy.</p>';
                    }
        require_once(TEMPLATES.'message_header.tpl');
        break;
}

require_once(TEMPLATES.'message_footer.tpl');
$content = $out;

/*
 * 
 *  Set up the warnings to be displayed for:
 *  recipe count, individual recipe count, entry deadline
 * 
 */
$warning = '';
$warn = '';

if(isset($_SESSION['utypeit_info'])) {
	$x = new Warnings($_SESSION['utypeit_info']);
	$warn = $x->_warnings($_SESSION['order_id'],$_SESSION['user']->id);
	$warning = $warn->display;
}
 
include(TEMPLATES.'main.tpl');

?>
