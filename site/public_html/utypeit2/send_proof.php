<?

session_start();

if(!$_SESSION['login'] == true) {
	header("Location: index.php");
}

require_once('src/globals.php');

require_once(SERVICES.'Transmit.php');

$action = '';
if($_REQUEST['action']) {
	$action = $_REQUEST['action'];	
}

$page = 'send_proof';
$order_number = $_SESSION['order_number'];
$page_note = $_SESSION['utypeit_info']->welcome_note;

$demo = false;
if($order_number == 1) {
	$demo = true;
}

$script = "document.observe('dom:loaded',function() {
    fancyNav();
});";

switch($action) {
	case 'proof':
		$panel = 2;
		$title = "GENERATING PROOF";
		$responder = $_POST['proof_type'];	
		$nt = new Transmit();
		if($responder == 'wait') {
			$content = '<img src="images/ajax-loader.gif"> Thank You. Your recipe proof is being prepared. This will take a moment.';
			$res = $nt->_request($_SESSION['order_id'],'SOAPRF',$_SESSION['user'],$responder);
			if($res) {
				header('Location: '.$_SERVER['PHP_SELF'].'?action=finished');
			} else {
				header('Location: '.$_SERVER['PHP_SELF'].'?action=error');
			}
		} else {
			$res = $nt->_request($_SESSION['order_id'],'SOAPRF',$_SESSION['user'],$responder);
			header('Location: '.$_SERVER['PHP_SELF'].'?action=sent');
		}
		break;
	case 'finished':
		$panel = 2;
		$title = "PROOF GENERATED";
		$out = "Click <a href='".UTI_URL."job_files/".$order_number.".pdf?fno=".date('s')."' target='_blank'>HERE</a> to download the proof file for your order.<br />";
		$content = "<div style=\"padding: 4px; text-align: left;\">".$out."</div>";
		break;
	case 'sent':
		$panel = 2;
		$title = "GENERATING PROOF";
		$content = '<p>Your request has been sent. Please check your email in a few minutes for the link to your proof.</p>';
		break;
	case 'error':
		$panel = 2;
		$title = 'TRANSMISSION ERROR';
		$content = 'There has been a problem with the transmission. Please use the back button on your browser to try again.';
		break;
	default:
		$panel = 2;
		$title = 'SEND FOR PROOF';
		$out = '
		<div class="special">
			<h1>IMPORTANT!</h1>
			<p>Recipes will be shown in the recipe format you have chosen.</p>
			<h2>ONLY RECIPES WITH <span class="emphasis">APPROVED</span> STATUS WILL BE IN YOUR PROOF.</h2>
			<p>If you need assistance, please call our Customer Service Specialists at 1-800-227-7282 or contact us <a href="http://dev.cbp.ctcsdev.com/utypeit2/message_center.php?action=contact_cpi">here</a>.</p>
		</div>
		<form action="'.$_SERVER['PHP_SELF'].'" method="POST">
			<input type="hidden" name="action" value="proof">
			<table id="proof_table">
				<tr style="display:none;"><td>Our Online proofing is disabled at the moment. Contact us by email or 1.800.227.7282 during business hours and we can provide you with a proof.</td>
				</tr>
				<tr>
					<td>Wait while my proof is being generated</td>
					<td><input type="radio" name="proof_type" value="wait"></td>
				</tr>
				<tr>
					<td>Send me an email when it\'s done</td>
					<td><input type="radio" name="proof_type" value="email"></td>
				</tr>
				<tr>
					<td colspan="2">&nbsp;</td>
				</tr>';
				if($_SESSION['user']->order_level == '5') {
					if($demo) {
					$out .= '
				<tr>
					<td colspan="2">Live account members can submit for proofs online</td>
				</tr>';
					} else {
						$out .= '<tr>
					<td colspan="2" class="submit"><button type="submit">Send For Proof</button></td>
				</tr>';
					}
				}
			$out .= '</table>
		</form>';
		
		$content = $out;
		break;
}

require_once(TEMPLATES.'main.tpl');

?>
