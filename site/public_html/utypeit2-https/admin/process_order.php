<?

//ini_set('display_errors',1);
//error_reporting(-1);

session_start();

if(!$_SESSION['login'] == true) {
	header("Location: index.php");
}

require_once('../src/globals.php');
require_once(SERVICES.'Transmit.php');
//require_once(INCLUDES.'MakeXML.php');

$action = '';
if(isset($_REQUEST['action'])) {
	$action = $_REQUEST['action'];	
}
$path = 'CBSOAP';
if($_REQUEST['path']) {
	$path = $_REQUEST['path'];
}

$content;
switch($action) {
	case 'finished':
		$panel = 2;
		$title = "PROOF GENERATED";
		$out = "Click <a href='".UTI_URL."job_files/".$_SESSION['order_number'].".pdf?fno=".date('s')."' target='_blank'>HERE</a> to download the proof file for your order.<br />";
		$content = "<div style=\"padding: 4px; text-align: left;\">".$out."</div>";
		break;
	case 'error':
		$panel = 2;
		$title = 'TRANSMISSION ERROR';
		$content = 'There has been a problem with the transmission. Please use the back button on your browser to try again.';
		break;
	default:
		$transmit = new Transmit();
		$res = $transmit->_request($_SESSION['order_id'],$path,$_SESSION['user'],'wait');
		if($res) {
			header('Location: '.$_SERVER['PHP_SELF'].'?action=finished');
		} else {
			header('Location: '.$_SERVER['PHP_SELF'].'?action=error');
		}
		break;
}
?>

<!DOCTYPE html>
<html>
	<head></head>
	<body>
		<?=$content?>
	</body>
</html>
<?php
//$order_number = 'TEST';

/*$page_note = $_SESSION['utypeit_info']->welcome_note;



$params = array(
					'jobname'=>$order_number,
					'returnname'=>'/MILES/SPOOLDIR/'.$order_number.'.pdf',
					'unitname'=>'UNIT',
					'ftname'=>$path,
					'datapack'=>'U',
					'list-jobs'=>'0',
					'list-takes'=>'0',
					'overwrite'=>'0',
					'tidy-up-afterwards'=>'0',
					'textdump'=>'0',
					'textdump-option'=>'0',
					'compact'=>'0',
					'compact-char'=>'0',
					'eof-char'=>'-1',
					'header-length'=>'0',
					'security'=>'0',
					'creation-style'=>'3',
					'start-folio-no'=>'0',
					'auto-numbering'=>'15',
					'style'=>'0',
					'datafile'=>'/MILES/HANDOFFS/'.$order_number.'.xml',
					'suppress-do-mask'=>'0',
					'dip'=>'0',
					'first-job'=>'0',
					'this-job'=>'0'
				);

$theData1 = "\n<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n
						\t<SOAP-ENV:Envelope \n
						\txmlns:SOAP-ENV=\"http://schemas.xmlsoap.org/soap/envelope/\" \n
						\txmlns:SOAP-ENC=\"http://schemas.xmlsoap.org/soap/encoding/\" \n
						\txmlns:xsi=\"http://www.w3.org/2001/XMLSchema-instance\" \n 
						\txmlns:xsd=\"http://www.w3.org/2001/XMLSchema\" \n 
						\txmlns:ns1=\"urn:osoap\"> \n
						\t<SOAP-ENV:Body SOAP-ENV:encodingStyle=\"http://schemas.xmlsoap.org/soap/encoding/\"> \n
							\t\t<ns1:osoap>\n
								\t\t\t<in xsi:type=\"ns1:jqbuff\">\n
									\t\t\t\t<parsebuff>\n";

				
switch($action) {
	case 'finished':
		$panel = 2;
		$title = "PROOF GENERATED";
		$out = "Click <a href='../job_files/".$order_number.".pdf'>HERE</a> to download the proof file for order #".$_SESSION['order_number'].".<br />";
		echo "<div style=\"padding: 4px; text-align: left;\">".$out."</div>";
		break;
	default: 
		$content = '<img src="images/ajax-loader.gif">';			
		$make = new MakeXML();
		$xml = $make->makeMilesXML($_SESSION['order_id'],$path);
		$xml = htmlentities($xml,ENT_QUOTES,'utf-8',false);
		
		$theData = $theData1;
		foreach($params AS $k=>$v) {
			$theData .= "\t\t\t\t\t<".$k.">".$v."</".$k.">\n";
		}
		$theData .= "\t\t\t\t</parsebuff>\n
					\t\t\t\t<input-type xsi:type=\"InputType\">REMOTE</input-type>\n
					\t\t\t\t<fcnts>\n
					\t\t\t\t".$xml."\n
					\t\t\t\t</fcnts>\n\t\t\t</in>\n\t\t</ns1:osoap>\n\t</SOAP-ENV:Body>\n</SOAP-ENV:Envelope>";
		//echo $theData;
		$url = 'http://64.126.95.169:18081';
		$req = new HTTP_Request($url);
		$req->setMethod('POST');
		$req->addHeader("content-type", 'text/xml');
		$req->addHeader("encoding", 'UTF-8');
		$req->setBody($theData);
		$result = $req->sendRequest();

		$message = $req->getResponseBody();
		//print_r($message);
		$p = xml_parser_create();
		xml_parse_into_struct($p, $message, $vals, $index);
		xml_parser_free($p);
		
		$out = "";
		for($v=0;$v<count($vals);$v++) {
			$set = $vals[$v];
			$file = '';
			if($set['tag'] == "FCNTS" && $set['type'] == 'complete') {
				$file = base64_decode($set['value']);
				if($file) {
					$fp = fopen(UTI_URI.'job_files/'.$order_number.'.pdf','wb');
					fwrite($fp,$file);
					fclose($fp);
					header('Location: '.$_SERVER['PHP_SELF'].'?action=finished');
				} else {
					echo $set['value'];
				}
			} else {
				echo $set['value'];
			}
		}
		break;
}*/
?>
