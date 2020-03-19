<?php
require_once('HTTP/Request.php');
require_once('../services/MakeXML.php');

$x = new MakeXML();
$xml = $x->makeMilesXML($order_id);

$theData = '<?xml version="1.0" encoding="UTF-8"?>
						<SOAP-ENV:Envelope 
						xmlns:SOAP-ENV="http://schemas.xmlsoap.org/soap/envelope/" 
						xmlns:SOAP-ENC="http://schemas.xmlsoap.org/soap/encoding/" 
						xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" 
						xmlns:xsd="http://www.w3.org/2001/XMLSchema" 
						xmlns:ns1="urn:osoap">
						<SOAP-ENV:Body SOAP-ENV:encodingStyle="http://schemas.xmlsoap.org/soap/encoding/">
							<ns1:osoap>
								<in xsi:type="ns1:jqbuff">
									<parsebuff>';

$params = array(
					'takename'=>'0',
					'custname'=>'0',
					'sara1name'=>'0',
					'sara2name'=>'0',
					'sara3name'=>'0',
					'ulnaname'=>'0',
					'dumpname'=>'0',
					'returndir'=>'0',
					'ptname'=>'0',
					'syspack'=>'0',
					'doctype'=>'0',
					'dependent-take'=>'0',
					'datafile'=>'0',
					'datadir'=>'0',
					'datalist'=>'0',
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
					'take-dependency'=>'0',
					'suppress-do-mask'=>'0',
					'dip'=>'0',
					'first-job'=>'0',
					'this-job'=>'0',
					'style'=>'0',
					'fileext'=>'PS',
					'returnname'=>'test3.xml.pdf',
					'ftname'=>'CBSOAP',
					'unitname'=>'unit',
					'datapack'=>'U',
					'jobname'=>'0'
				);
				
foreach($params AS $k=>$v) {
	$theData .= '<'.$k.'>'.$v.'</'.$k.'>';
}
$theData .= '</parsebuff>';
$theData .= '<input-type xsi:type="InputType">REMOTE</input-type>';
$theData .= '<fcnts>';
$theData .= $xml;
$theData .= '</fcnts></in></ns1:osoap></SOAP-ENV:Body></SOAP-ENV:Envelope>';

$url = 'http://173.197.15.58:18081';
$req = new HTTP_Request($url);
$req->setMethod('POST');
$req->addHeader("content-type", 'text/xml');
$req->setBody($theData);
$result = $req->sendRequest();

echo 'REQUEST: <br />';print_r($req);

echo 'RESULT: <br />'.$result.'<br />';
?>
