<?php
/*
 * 
 * ListLimit
 * by William Logan, 2012
 * 
 * Sets the limit on the number of results returned in a list
 * Receives an optional array of increment options.
 * Returns a select menu of options. 
 * 
 */
require_once(INCLUDES.'Elements.php');

class ListLimit
{
	var $options = array();
	var $ne = null;
	
	function __construct($options=null) {
		if($options) {
			$this->options = $options;
		} else {
			$this->options = array(25,50,100,200);
		}
		$this->ne = new Elements('div');
	}
	
	public function _draw() {
		$options = $this->options;
		$limit_label = $this->ne->createElement('label','','Limit Results to: ');
		$limit_select = $this->ne->createElement('select','id=list_limit&name=list_limit');
		for($o=0;$o<count($options);$o++) {
			if($_SESSION['list_limit'] == $options[$o]) {
				$selected = '&selected=selected';
			} else {
				$selected = '';
			}
			$limit_option = $this->ne->createElement('option','value='.$options[$o].$selected,$options[$o]);
			$limit_select->appendChild($limit_option);
		}
		$limit_label->appendChild($limit_select);
		$this->ne->appendChild($limit_label);
		return($this->ne->saveHTML());
	}
}
?>