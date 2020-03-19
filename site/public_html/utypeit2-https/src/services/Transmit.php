<?

session_start();

if ( !defined('SRC') ) require_once('../globals.php');

require_once(SERVICES.'Email.php');
require_once(SERVICES.'BaseService.php');
require_once(INCLUDES.'MakeXML.php');
require_once('HTTP/Request.php');

class Transmit
{

	var $user;
	var $order;
	var $file;
	var $responder;

	public function _request($id,$path,$user,$responder) {
		
		$nb = new BaseService();
		$this->order = $nb->sendAndGetOne('SELECT * FROM Orders WHERE id="'.$id.'"');
		$this->user = $user;
		$this->responder = $responder;
		
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
					'jobname'=>0,
					'returnname'=>'/MILES/SPOOLDIR/'.$this->order->order_number.'.pdf',
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
					'datafile'=>'/MILES/HANDOFFS/'.$this->order->order_number.'.xml',
					'suppress-do-mask'=>'0',
					'dip'=>'0',
					'first-job'=>'0',
					'this-job'=>'0'
				);
		$make = new MakeXML();
		$xml = $make->makeMilesXML($id,$path);
		$xml = htmlentities($xml,ENT_QUOTES,'UTF-8',true);
		
		foreach($params AS $k=>$v) {
			$theData .= "\t\t\t\t\t<".$k.">".$v."</".$k.">\n";
		}
		$theData .= "\t\t\t\t</parsebuff>\n
					\t\t\t\t<input-type xsi:type=\"InputType\">REMOTE</input-type>\n
					\t\t\t\t<fcnts>\n
					\t\t\t\t".$xml."\n
					\t\t\t\t</fcnts>\n\t\t\t</in>\n\t\t</ns1:osoap>\n\t</SOAP-ENV:Body>\n</SOAP-ENV:Envelope>";
		//echo $theData;
		$url = 'http://173.197.15.58:18081';
		$req = new HTTP_Request($url);
		$req->setMethod('POST');
		$req->addHeader("content-type", 'text/xml');
		$req->addHeader("encoding", 'UTF-8');
		$req->setBody($theData);
		$result = $req->sendRequest();
		$message = $req->getResponseBody();
		//print_r($message);
		//return(true);
		$p = xml_parser_create();
		xml_parse_into_struct($p, $message, $vals, $index);
		xml_parser_free($p);
			
		//print_r($vals);
		for($v=0;$v<count($vals);$v++) {
			$set = $vals[$v];
			if($set['tag'] == "FCNTS" && $set['type'] == 'complete') {
				$this->file = base64_decode($set['value']);
			}
		}

		if($this->file) {
			if($this->_write()) {
				if($this->_report()) {
					return true;
				} else {
					throw new Exception("Error Processing Request: Unable to send new cookbook reporting email Transmit:149", 1);
				}

			} else {
				throw new Exception("Error Processing Request: Unable to write the cookbook file. Transmit:103", 1);
			}
		} else {
			return(false);
		}

	}

	private function _write() {

		$fp = fopen(UTI_URI.'job_files/'.$this->order->order_number.'.pdf','wb');
		if(fwrite($fp,$this->file)) {
			fclose($fp);
			return true;
		}

		return false;		

	}

	private function _report() {

		if($this->responder == 'wait') {
			return(true);
		} else {
			$ne = new Email();
			$message = "<p>Your proof is ready!</p>
							<p>Your Project: ".stripslashes(urldecode($this->order->title))."<br />
							Click <a href=\"".UTI_URL."job_files/".$this->order->order_number.".pdf?fno=".date('s')."\">HERE</a> to download the proof file.</p>
							<p>Click <a href=\"http://dev.cbp.ctcsdev.com/utypeit2/\">HERE</a> to log in to your account at U-Type-It Online&trade;</p>";
			// vars = array(recipient_email(s),reply_to,sender_email,sender_name,subject,message)
			$vars = array(
				'recipient_email'=>stripslashes(urldecode($this->user->first_name)).' '.stripslashes(urldecode($this->user->last_name)).' <'.stripslashes(urldecode($this->user->email)).'>',
				'reply_to'=>'info@dev.cbp.ctcsdev.com',
				'sender_email'=>'info@dev.cbp.ctcsdev.com',
				'sender_name'=>'UTypeIt Customer Support',
				'subject'=>'Your Cookbook Proof is Ready',
				'message'=>$message);
			if($ne->_mail($vars)) {
				return true;
			}
			
			return false;
			
		}

	}
}

?>
