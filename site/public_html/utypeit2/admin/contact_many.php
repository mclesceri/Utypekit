<?php

session_start();

if(!$_SESSION['login'] == true) {
	die;
}

require_once('../src/globals.php');

require_once(SERVICES.'People.php');
require_once(SERVICES.'Email.php');


if($_REQUEST['action']) {
	$action = $_REQUEST['action'];
} else {
	$action = null;
}

$people = array();

$form_action = '';
$response_action = '';

$out = '&nbsp;';
$msg = '';

$tab = 5;

$header_left = '&nbsp;';
$header_right = '&nbsp;';

switch($action) {
	case 'contact_send':
		$ne = new Email();
		$res = $ne->sendMail($_POST);
		if($res) {
			$refstr = 'Location: '.$_SERVER['PHP_SELF'].'?action=contact_confirm&reponse='.$res;
			header($refstr);
		} else {
			$msg = 'There has been a problem with the transmission. Please use the back button on your browser to try again. If this problem persists, please contact technical support.';
		}
		break;
	case 'contact_confirm':
		$header_right = '<input type="button" id="add_new" name="add_new" onclick="window.location=\'contact_one.php\'" value="Send Another Message"/>';
		$response = $_GET['response'];
		if($response == 'success') {
			$title = 'CONTACT SENT';
			$msg = 'The message was successfully sent. Please <a href="#"onclick="setContent(\'contact_many\',{mode:\'redirect\'})">click here</a> to send another message.';
		} else {
			$title = 'MESSAGE ERROR';
			$msg = 'There was an error sending the message. Please check the recipient\'s address and try again.<br />';	
		}
		break;
	case 'contact_compose':
		$title = "CONTACT COMPOSE";
		$header_left = '&nbsp;';
		$header_right = '&nbsp;';
		$form_action = "contact_send";
		$recipient = $_POST['id'];
		$np = new People();
		for($r=0;$r<count($recipient);$r++) {
			$rec =  $np->getPeopleById($recipient[$r]);
			if($rec->email) {
				$rec_string .= $recipient_name = $rec->first_name.' '.$rec->last_name;
				$rec_string .= "<".$recipient_email = $rec->email.">";
				if($r+1 != count($recipient)) {
					$rec_string .= ', ';
				}
			}
		}		
		break;
	default:
		$title = "CONTACT MANY";
		$header_left = '&nbsp;';
		$header_right = '&nbsp;';
		$form_action = "contact_compose";
		$response_action = "";
}

$out = "<script type='text/javascript'>
	document.observe('dom:loaded', function() {
		
		showSet(currentSet);
		
		tinyMCE.init({
			theme : 'advanced',
			mode : 'exact',
			theme_advanced_buttons1 : 'bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,formatselect,fontselect,fontsizeselect|,undo,redo,',
			theme_advanced_buttons2 : 'cut,copy,paste,pastetext,pasteword,|,bullist,numlist,|,link,unlink,anchor,image,cleanup,code,|,insertdate,inserttime,preview,|,forecolor,backcolor,|,help,',
			theme_advanced_buttons3 : '',
			skin : 'o2k7',
			skin_variant : 'black',
			theme_advanced_toolbar_location : 'top',
			theme_advanced_toolbar_align : 'left',
			elements : 'message'
		});";
		if(!$action) {
			$out .= "function setRecipientList(type) {
			var url = 'includes/form_elements.php?list=recipient&type=' + type;
			$('recipient').update('');
			var b = new Ajax.Request(url,{ onSuccess: function (transport){ var data = transport.responseText; $('recipient').update(data); } });
		}
					
		$('contact_group').observe('change', function(event){
			var select = $( 'contact_group' );
			var val = select.selectedIndex >=0 && select.selectedIndex ? select.options[select.selectedIndex].value : undefined;
			setRecipientList(val);
		});";
		}
	$out .= "});
</script>
<form id='contact_many' action='".$_SERVER['PHP_SELF']."' method='POST'>
<input type='hidden' name='action' value='".$form_action."'>";
if($action == 'contact_compose') {
	$out .= "<input type='hidden' name='sender_name' value='".$_SESSION['user']->first_name." ".$_SESSION['user']->last_name."'>
	<input type='hidden' name='sender_email' value='".$_SESSION['user']->email."'>";
}
$out .= "<div id='feedback'>&nbsp;</div>
<table width='100%' border='0' cellspacing='0' cellpadding='4'>";
	if($action == 'contact_confirm') {
		$out .= '<tr><td>'.$msg.'</td></tr>';
	} elseif($action == 'contact_compose') {
		$out .= "<tr>
			<td class='formLabel'>Sender:</td>
			<td class='formInput'>".$_SESSION['user']->first_name.' '.$_SESSION['user']->last_name."</td>
		</tr>
		<tr>
			<td class='formLabel'>Reply To:</td>
			<td class='formInput'><input type='text' name='reply_to' id='reply_to' value='";
			if($_SESSION['user']->email) { 
				$out .= $_SESSION['user']->email; 
			} else {
				$out .= $generic_reply_to; 
			}
			$out .= "'/></td>
		</tr>
		<tr>
			<td class='formLabel' style='vertical-align: top; padding-top: 5px'>Recipients:</td>
			<td class='formInput'><textarea name='recipient_email' id='recipient_email' cols=60 rows=5>".$rec_string."</textarea><a href='#' onclick=\"setContent('contact_many',{mode:'redirect'})\">Select Different Recipients</a></td>
		</tr>
		<tr>
			<td class='formLabel'>Subject:</td>
			<td class='formInput'><input type='text' name='subject' id='subject' size='40'></td>
		</tr>
		<tr>
			<td class='formLabel' style='vertical-align: top; padding-top: 5px'>Message:</td>
			<td class='formInput'>
			<textarea name='message' id='message' cols='100' rows='20' style='border: 1px #333333 solid'>Some sample text</textarea>
			</td>
		</tr>";
	} else {
		$out .= "<tr>
			<td class='formLabel' style='width: 40%'>Recipient Group:</td>
			<td class='formInput'>
				<select name='contact_group' id='contact_group'>
					<option value=''> -- </option>
					<option value='customer'>Customers</option>
					<option value='user'>Users</option>
					<option value='contractor'>Contractors</option>
				</select>
				</td>
		</tr>
		<tr>
			<td class='formLabel' style='width: 40%'>Message Recipients:</td>
			<td class='formInput'><select name='recipient[]' id='recipient' multiple='multiple' style='width: 150px; height: 200px'>
			</select></td>
		</tr>";
	}
	$out .= "<tr>";
	if($action == 'contact_compose') {
		$out .= "<td  colspan='2' class='formRight'><input type='submit' id='send_email' value='Send' ></td>";
	} elseif($action != 'contact_confirm') {
		$out .= "<td  colspan='2' class='formRight'><input type='submit' id='compose_email' value='Compose Message' ></td>";
	}
	$out .= "</tr>
	<tr>
		<td colspan='2'>&nbsp;</td>
	</tr>
</table>
</form>";

$header_left = '&nbsp;';
$header_middle = "<div class=\"headerPageTitle\">MESSAGE CENTER</div>";
$header_right = '&nbsp;';
$content = $out;

include(TEMPLATES.'admin.php');

?>