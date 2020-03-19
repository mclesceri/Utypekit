<?
session_start();

if(!$_SESSION['login'] == true) {
	die;
}

require_once('HTTP/Request.php');
require_once('../services/MakeXML.php');

$make = new MakeXML();
$xml = htmlspecialchars($make->makeMilesXML($_POST['id']));

$order_number = $_POST['order_number'];
$sub = explode('_',$order_number);
$jobname = $sub[0];

			
$theData = "\n<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n
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

$params = array(
					'jobname'=>$jobname,
					'returnname'=>'/MILES/SPOOLDIR/'.$order_number.'.pdf',
					'unitname'=>'UNIT',
					'ftname'=>'CBSOAP',
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
					'this-job'=>'0',
				);
				
foreach($params AS $k=>$v) {
	$theData .= "\t\t\t\t\t<".$k.">".$v."</".$k.">\n";
}
$theData .= "\t\t\t\t</parsebuff>\n";
$theData .= "\t\t\t\t<input-type xsi:type=\"InputType\">REMOTE</input-type>\n";
$theData .= "\t\t\t\t<fcnts>\n";
$theData .= "\t\t\t\t".$xml."\n";
$theData .= "\t\t\t\t</fcnts>\n\t\t\t</in>\n\t\t</ns1:osoap>\n\t</SOAP-ENV:Body>\n</SOAP-ENV:Envelope>";

$url = 'http://64.126.95.169:18081';
$req = new HTTP_Request($url);
$req->setMethod('POST');
$req->addHeader("content-type", 'text/xml');
$req->setBody($theData);
$result = $req->sendRequest();

$message = $req->getResponseBody();

//echo $result;

$p = xml_parser_create();
xml_parse_into_struct($p, $message, $vals, $index);
xml_parser_free($p);

//print_r($vals);

$res = "Click <a href='../../job_files/".$order_number.".pdf'>HERE to download the proof file for order #".$order_number.".<br />";
$out = "";
for($v=0;$v<count($vals);$v++) {
	$set = $vals[$v];
	$file = '';
	if($set['tag'] == "FCNTS" && $set['type'] == 'complete') {
		$file = base64_decode($set['value']);
		if($file != '') {
			$fp = fopen('../../job_files/'.$order_number.'.pdf','wb');
			fwrite($fp,$file);
			fclose($fp);
			$out = $res;
		} else {
			print_r($vals);
		}
	}
}

?>

<div style="background-color: #00CC00"><?=$out?></div>