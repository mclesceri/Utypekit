<?php
/*
 * 
 * DataList
 * by William Logan, 2012
 * 
 * Constructs an AJAX enabled list, based on a structured query
 * Receives 
 * 	title,
 * 	page (the current working list page),
 * 	filter (draw list filter: true/false),
 * 	limit (draw list limit: true/false),
 * 	print (draw print list: true/false),
 * 	crumbs (draw bread crumbs: true/false)
 * 
 * Returns formatted HTML list.
 * 
 */
 require_once('../services/BaseService.php');
 require_once('Elements.php');
 require_once('AJAX.php');
 require_once('ListFilter.php');
 require_once('BreadCrumb.php');
 require_once('ListPrint.php');
 require_once('ListLimit.php');
 
 class DataList
 {
	var $title = '';
	var $filter;
	var $limit;
	var $print;
	var $crumbs;
	
	var $ne;
	var $nf;
	var $nl;
	var $np;
	var $nb;
	
	function __construct($title,$page,$filter=false,$limit=false,$print=false,$crumbs=false) {
			
		$this->title = $title;
		$this->filter = $filter;
		$this->limit = $limit;
		$this->print = $print;
		$this->print = $crumbs;
		
		global $script;
		if(isset($scripts)) {
			$na = new AJAX();
			$script .= $na->_write('list');
		}
		
		$this->ne = new Elements();
		
		if($filter) {
			$this->nf = new ListFilter();
		}
		
		if($limit) {
			$this->nl = new ListLimit();
		}
		
		if($print) {
			$this->np = new ListPrint();
		}
		
		if($crumbs) {
			$this->nb = new BreadCrumb($page);
		}
	}
	
 	public function _draw($query,$start=null,$limit=null,$lpage=null,$orderby=null,$translate=null) {
 		
 		$data = $this->_get($query,$start,$limit,$orderby);
		
		$columns = array();
		foreach($data[0] AS $k=>$v) {
			$columns[$k] = ucwords(str_replace('_', ' ', $k));
		}
		$columns['edit'] = "Edit";
		
		$id = strtolower(str_replace(' ','_',$this->title));
		$list = $this->ne->createElement('div','id='.$id.'&class='.$id);
		$list_table = $this->ne->createElement('table');
		
		// Construct the header row
		$header_tr = $this->ne->createElement('tr');
		$header_td = $this->ne->createElement('td','colspan='.count($columns).'&class=listHeader');
		$header_tr->appendChild($header_td);
		$list_table->appendChild($header_tr);
		
		// Construct the column th row
		$th_row = $this->ne->createElement('tr');
		foreach($columns AS $k=>$v) {
			$onclick = urlencode("start=".$start."&limit=".$limit."&lpage=1&orderby=".$k);
			$th = $this->ne->createElement('th');
			$a = $this->ne->createElement('a','href=#&onclick=setList(\''.$id.'\',\''.$onclick.'\')',$v);
			$th->appendChild($a);
			$th_row->appendChild($th);
		}
		$list_table->appendChild($th_row);
		
		// Construct the list
		for($d=0;$d<count($data);$d++) {
			if($d%2 == 0) {
				$class = 'listEvenCell';
			} else {
				$class = 'listOddCell';
			}
			$tr = $this->ne->createElement('tr');
			foreach($data[$d] AS $k=>$v) {
				$td = $this->ne->createElement('td','class='.$class,$v);
				$tr->appendChild($td);
			}
			$list_table->appendChild($tr);
		}	
		
		$list->appendChild($list_table);
		$this->ne->appendChild($list);
		
		return($this->ne->saveHTML());
		
 	}
	
	protected function _get($query,$start,$limit,$orderby) {
		if($orderby) {
			$query .= " ORDER BY ".$orderby;
		}
		if($limit) {
			$query .= " LIMIT ".$start.','.$limit;
		}
		
		$nb = new BaseService();
		$results = $nb->sendAndGetMany($query);
		return($results);
	}
 }
?>