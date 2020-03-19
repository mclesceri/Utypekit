<?php
class Elements extends DOMDocument {
    
	public function createElement($name,$args=null,$value = null) {
		
		$attr = array();
		if($args) {
			parse_str($args,$attr);
		}
		
		$orphan = new extDOMElement($name, $value);
		// new  sub-class object
		
		$docFragment = $this->createDocumentFragment();
		// lightweight container maintains "ownerDocument"
		$docFragment->appendChild($orphan);
		// attach
		$ret = $docFragment->removeChild($orphan);
		// remove
		
		if($attr) {
			foreach($attr AS $key=>$val) {
				$ret->setAttribute($key,$val);
			}
		}
		
		return $ret;
		// ownerDocument set; won't be destroyed on method exit
	}

}

class extDOMElement extends DOMElement {
	function __construct($name, $value = '', $namespaceURI = null) {
		parent::__construct($name, $value, $namespaceURI);
	}
	//  ... more class definition here
}

/* 
 * 
 * TEST FUNCITON
 * 
 * $doc = new Elements('test');
 * $el = $doc -> createElement('tagname','id=val','test');
 * $doc -> appendChild($el);
 * 
 * // append discards the DOMDocumentFragment and just adds its child nodes, but ownerDocument is maintained.
 * echo get_class($el) . "<br/>";
 * echo get_class($doc -> documentElement) . "<br/>";
 * echo "<xmp>" . $doc -> saveXML() . "</xmp>";
 * 
 */
?>