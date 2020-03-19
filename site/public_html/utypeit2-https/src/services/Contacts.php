<?php
if ( !defined('SRC') ) require_once('../globals.php');
require_once(SERVICES.'BaseService.php');
require_once(SERVICES.'Email.php');

class Contacts extends Email
{
	var $domain = "http://dev.cbp.ctcsdev.com/utypeit2/";
	var $title = '';
	
	function _welcome($type,$data) {
		//"{\"status\": \"true\", \"id\":\"".$order_id."\", \"user\":\"".$person_id."\", \"action\":\"order_list\", \"mode\":\"redirect\", \"message\": \"New Order Added\"}" );
		$info = json_decode($data);
		$person_id = $info->user;
		$nb = new BaseService();
		$user = $nb->sendAndGetOne('SELECT * FROM People WHERE id="'.$person_id.'"');
		if($type == 'demo') {
			$message = '
			<table>
				<tr>
					<td class="label">Your Name: </td>
					<td>'.stripslashes(urldecode($user['first_name'])).' '.stripslashes(urldecode($user->last_name)).'</td>
				</tr>
				<tr>
					<td class="label">Username: </td>
					<td>'.stripslashes(urldecode($user->login)).'</td>
				</tr>
				<tr>
					<td class="label">Password: </td>
					<td>'.stripslashes(urldecode($user->password)).'</td>
				</tr>
			</table>
			<h2>Your new account is ready...</h2>';
			$message .= str_replace('[[domain]]', $this->domain, file_get_contents(SRC.'data/messages/demo_welcome_message.html'));
		} else {
			$message = '
			<table>
				<tr>
					<td class="label" style="width: 150px">Your Name: </td>
					<td>'.stripslashes(urldecode($user->first_name)).' '.stripslashes(urldecode($user->last_name)).'</td>
				</tr>
				<tr>
					<td class="label">Username: </td>
					<td>'.stripslashes(urldecode($user->login)).'</td>
				</tr>
				<tr>
					<td class="label">Password: </td>
					<td>'.stripslashes(urldecode($user->password)).'</td>
				</tr>
			</table>
			<h2>Your new account is ready...</h2>';
			$message .= str_replace('[[domain]]', $this->domain, file_get_contents(SRC.'data/messages/live_welcome_message.html'));
		}
		$message_body = $this->_construct($message);
		
		$vars = array(
			'recipient_email'=>stripslashes(urldecode($user->first_name)).' '.stripslashes(urldecode($user->last_name)).' <'.stripslashes(urldecode($user->email)).'>',
			'reply_to'=>'info@dev.cbp.ctcsdev.com',
			'sender_email'=>'info@dev.cbp.ctcsdev.com',
			'sender_name'=>'U-Type-It Online Customer Support',
			'subject'=> 'Welcome to U-Type-It Online',
			'message'=>$message_body);
		if($this->_mail($vars)) {
			return('{"status":"true"}');
		} else {
			return('{"status":"false"}');
		}
		
	}
	
	protected function _construct($message) {
		$out = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd"><html><head><meta charset="UTF-8"><title>Welcome to U-Type-It&trade; Online</title></head><body leftmargin="0" marginwidth="0" topmargin="0" marginheight="0" offset="0"><table width="600" cellpadding="4" cellspacing="0" border="0" valign="top" align="center"><tr><td style="background-color: #7EC7D1; padding: 4px"><img src="http://dev.cbp.ctcsdev.com/utypeit2/media/images/cookbook-logo-new2.png" align="left" /></td><td style="background-color: #7EC7D1; padding: 4px">Welcome to...<br /><img src="http://dev.cbp.ctcsdev.com/utypeit2/media/images/utypeit_logo.png" /></td></tr><tr><td  valign="top" style="background-color: #4B63AE"><table width="100%" border="0" cellpadding="4" cellspacing="2" style="color: #FFFFFF"><tr><td><h3 style="line-height: 1em; text-align: center; font-family: sans-serif">Support</h3></font></td></tr><tr><td style="background-color: #749DD2"><a href="http://dev.cbp.ctcsdev.com/order-a-free-kit/" target="_blank" style="color: #FFFFFF; font-family: sans-serif">Request a Kit</a></td></tr><tr><td style="background-color: #749DD2"><a href="http://dev.cbp.ctcsdev.com/contact/" style="color: #FFFFFF; font-family: sans-serif">Contact Support</a></td></tr><tr><td style="background-color: #749DD2"><a href="http://dev.cbp.ctcsdev.com/order/faqs/" style="color: #FFFFFF; font-family: sans-serif">Help/FAQ</a></td></tr><tr><td>&nbsp;</td></tr><tr><td style="background-color: #FCBA63; text-align: center"><a href="http://dev.cbp.ctcsdev.com/utypeit2/"style="color: #000000; font-family: sans-serif">Log In</a></td></tr></table></td><td valign="top">'.$message.'</td></tr></table></body></html>';
		return($out);
	}

}

//$_POST['action'] = 'welcome';

if(isset($_POST['action'])) {
	$nc = new Contacts();
	switch($_POST['action']) {
		case 'welcome':
			//echo $nc->_welcome('live','first_name=William&last_name=Tester&login=william&password=tester&email=rakmasterb@gmail.com');
			echo $nc->_welcome($_POST['type'],$_POST['data']);
			break;
	}
}

?>
