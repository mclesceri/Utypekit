<?php

session_start();
if(!isset($_SESSION['security_number'])) {
	$_SESSION['security_number']=rand(10000,99999);
}

$page = 'lost_password';

if(isset($_POST['action'])) {
	$action = $_POST['action'];
} else {
	$action = '';
}

require_once('src/globals.php');

require_once(SERVICES."BaseService.php");
require_once(SERVICES."Email.php");

$nb = new BaseService();
$ne = new Email();



switch($action) {
	case 'send':
		$secret = $_SESSION['security_number'];
		if($_POST['captcha'] != $secret) {
			$out = 'Please enter the correct security number into the text box.';
			return;
		} else {
			foreach($_POST AS $k=>$v) {
				$$k = $v;
			}

			$query = 'SELECT id,first_name,last_name,email,login,password FROM People WHERE first_name="'.$first_name.'" AND last_name="'.$last_name.'" AND email="'.$email.'"';
			$user = $nb->sendAndGetOne($query);
			
			if($user) {
				$message = "<p>Attached is your information. Please print this email out and keep it with your records.</p>
				<p>Your Name: ".$user->first_name." ".$user->last_name."<br />";
				$message .= "Username: ".$user->login."<br />
				Password: ".$user->password."<br />
				Log in to your account at <a href='http://dev.cbp.ctcsdev.com/utypeit2/'>U-Type-It&trade; Online</a><br /></p>";
        		
        		$vars = array();
				$vars['recipient_email'] = $first_name.' '.$last_name.' <'.$email.'>';
				$vars['sender_name'] = 'Cookbook Publishers Inc. Customer Support';
				$vars['sender_email'] = 'info@dev.cbp.ctcsdev.com';
				$vars['subject'] = 'Lost Password Request';
				$vars['message'] = $message;
				if($ne->_mail($vars)) {
					$out = "<p>Your information was sent. Check your email for a response If you continue to experience problems, please contact Cookbook Publishers, Inc. at 1-800-227-7282.";
				} else {
					$out = 'There was a problem sending the email. Please check your information and try again.';
				}
			} else {
				$out = '
				<p>There is no user on record by that name, with that email address. Please check your entries and try again. If this problem persists, please contact Cookbook Publishers Inc.;</p>
				<p>Cookbook Publishers, Inc.<br />
				10800 Lakeview Avenue<br />
				P.O. Box 15920<br />
				Lenexa, KS 66285-5920<br />
				1-800-227-7282<br />
				<a href="mailto:info@dev.cbp.ctcsdev.com">info@dev.cbp.ctcsdev.com</a></p>';
			}
		}
		unset($_SESSION['security_number']);
		break;
	default:
		$title = 'Lost Password';
		$out = "<p style='font-weight: bold'>Lost your login information?</p>";
		
		$out .= "<form action='".$_SERVER['PHP_SELF']."' method='post'>\n
		<input type='hidden' name='action' value='send'>\n
		<div id='2' style='margin-top: 10px;'>\n
			\t<div class='optionsHeaderDiv'><strong>Enter Personal Info</strong><br/>Enter some personal information below and we'll send an email<br /> with your login to the email address on record.</div>\n
			<table width='100%' border='0' cellpadding='0' cellspacing='0'>\n
				\t<tr>\n
					\t\t<td class='formLabel' style='width: 40%'>Your Name(first,last): </td>\n
					\t\t<td class='formInput'><input type='text' name='first_name' id='lost_fname' size='15'> <input type='text' name='last_name' id='lost_lname' size='15'></td>\n
				\t</tr>\n
				\t<tr>\n
					\t\t<td class='formLabel' style='width: 40%'>Your Email: </td>\n
					\t\t<td class='formInput'><input type='text' name='email' id='lost_email'></td>\n
				\t</tr>\n
				\t<tr>\n
					\t\t<td class='formLabel' style='width: 40%'>Your Zip Code: </td>\n
					\t\t<td class='formInput'><input type='text' name='zip2' id='lost_zip'></td>\n
				\t</tr>\n
				\t<tr>\n
					\t\t<td class=\"formLabel\"><img src=\"".UTI_URL."src/includes/image.php\" style=\"float: right;\"/></td>
                	<td class=\"formInput\"><input type=\"text\" name=\"captcha\" id=\"captcha\" class=\"static\"></td>
					</td>\n
				\t</tr>\n
				\t<tr>\n
					\t\t<td class='formSubmit' colspan='2'><button type='submit'>Get Login</button></td>\n
				\t</tr>\n
			</table>\n
			</div>\n";
		$out .= $opt2div;
		$out .= '</form>';
}
$header_left = "Order #".$_SESSION['order_number'].'<br />'.stripslashes($_SESSION['order_title']);
$header_middle = $title;
$header_right = "&nbsp;";
$content = $out;

include(TEMPLATES.'login.tpl');
?>
