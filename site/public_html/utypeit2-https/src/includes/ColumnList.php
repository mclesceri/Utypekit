<?
if ( !defined('SRC') ) require_once('../globals.php');
require_once(INCLUDES.'Elements.php');

class ColumnList
{
	
	var $ne;
	var $templates;
	var $subs = 'no';
	
	public function __construct($templates=null) {
		$this->ne = new Elements('div');
		if($templates) {
			$templates = json_decode($templates);
			$templates = $templates->templates;
			foreach($templates AS $t) {
				$t->template = file_get_contents($t->template);
			}
		}
		$this->templates = $templates;
	}

	public function _columnlist($data,$name=null,$tabindex=0) {
		
		// Start by building the column...
		$dom = new Elements();
        $column = $dom->createElement('div','class=orderListColumn&id=list_parent');
        //<button class="orderListButton" type="button" onclick="addCategory(this.next('div',0)); return false;">Add Category</button>
		$addButton = $dom->createElement('button','class=orderListButton&type=button&onclick='.urlencode('addCategory(this.next(\'div\',0)); return false;'),'Add Item');
		$column->appendChild($addButton);
		// Find out which template to use and match that template to the data element
		foreach($data as $k=>$v) {
			foreach($this->templates AS $t) {
				if($t->name == $k) {
					$template = $t->template;
				}
			}
			if(count($v) > 0) {
				foreach($v AS $s) {
					$s->template = $template;
				}
			}
		}
		
        //print_r($data);
        
		foreach($data AS $d) {
			foreach($d AS $v) {
				
				$template = $v->template;
				$parent = $v->parent;
				$newparse = $this->_replace($v, $template);// returns a dom document
				$newparse = $dom->importNode($newparse, true);
				
				if($parent) {
					foreach($column->childNodes AS $p) {
						if($p->firstChild->hasAttribute('number')) {
							$number = $p->firstChild->getAttribute('number');
							if($parent == $number) {
							    
								$all = $p->firstChild->getElementsByTagName('div');
								foreach($all AS $a) {
									$id = $a->getAttribute('id');
									if(substr($id,0,9) == 'childlist') {
										$a->appendChild($newparse);
									}
								}
							}
                            
						}
					}
				} else {
					$column->appendChild($newparse);
				}
			}
		}
		
		$dom->appendChild($column);
		return(array(stripslashes(urldecode($dom->saveHTML())),$tabindex));
	}

	public function _section($data) {
	    
		$node = file_get_contents(TEMPLATES.$data->template);
		// do the string replacements...
		$node = $this->_replace($data, $node); // returns dom node
		// nest subsections if
		$dom = new DOMDocument();
		$node = $dom->importNode($node, true);

		if($this->subs == 'yes') {
			$number = $data->number;
			$newsub = new stdClass();
			$newsub->title ='subcategories';
			$newsub->template = 'subcategory.tpl';
			$newsub->number = $number+1;
			$newsub->name = '';
			$newsub->parent = $number;
			$newsub->order = 1;
			$newsub->tabindex = 1;
			$newsub = $this->_subsection($newsub);
			$newsub = $newsub->documentElement->firstChild;
			$newsub = $dom->importNode($newsub,true);
			$divs = $node->getElementsByTagName('div');
			foreach($divs AS $d) {
				$id = $d->getAttribute('id');
				if(substr($id,0,9) == 'childlist') {
					$d->appendChild($newsub);
				}
			}
		}
		
		$dom->appendChild($node);
		return($dom);
	}
	
	public function _subsection($data) {
		$node = file_get_contents(TEMPLATES.$data->template);
		// do the string replacements...
		$node = $this->_replace($data, $node); // returns dom node
		$dom = new DOMDocument();
		$node = $dom->importNode($node,true);
		$dom->appendChild($node);
		return($dom);
	}
	
	protected function _replace($values,$subject) {
		$subject = str_replace($values->title, $values->title.'_'.$values->order,$subject);
        
		if($this->subs == 'yes') {
			if(strpos($subject, 'childlist')) {
				$subject = str_replace('childlist', 'childlist_'.$values->order, $subject);
			}
		}
		foreach($values AS $k=>$v) {
			$subject = str_replace('[['.$k.']]',$v,$subject);
		}
        if(isset($values->tabindex)) {
            $subject = str_replace('[[tabindex]]',$values->tabindex++,$subject);
        } else {
            $subject = str_replace('tabindex="[[tabindex]]"','',$subject);
        }
		if(strpos($subject, '[[controls]]')){
			$repstr ='<img src="'.IMAGES.'remove_button.png" onclick="cl._remove(this)">';
			if($this->subs == 'yes') {
			    $repstr .= '&nbsp;<a href="#" class="columnSelect" onclick="cl._show(this); return false;"> > </a>';
            }
			$subject = str_replace('[[controls]]',$repstr,$subject);
		}
		// convert to DOM
		$dom = new DOMDocument();
		$dom->validateOnParse = true;
   		$dom->loadHTML($subject);
		
		// IF no subs, then strip out the childlist and subcategory button
		if($this->subs != 'yes') {
			foreach($dom->documentElement->firstChild->childNodes AS $s) {
				foreach($s->childNodes AS $c) {
					if($c->nodeName == 'button') {
						$c->parentNode->removeChild($c);
					}
				}
				foreach($s->childNodes AS $c) {
					$attrs = $c->attributes;
					if($attrs) {
						foreach($attrs AS $k=>$v) {
							if($v->nodeValue == 'childlist') {
								$c->parentNode->removeChild($c);
							}
						}
					}
				}
			}
		}
		return($dom->documentElement->firstChild);
	}
}

if(isset($_POST['action'])) {
	$action = $_POST['action'];
	switch($action) {
		case 'section':
			$ncl = new ColumnList();
			$data = (object) $_POST;
			$res = $ncl->_section($data);
			echo stripslashes(urldecode($res->saveHTML()));
			break;
		case 'subsection':
			$ncl = new ColumnList();
			$data = (object) $_POST;
			$res = $ncl->_subsection($data);
			echo stripslashes(urldecode($res->saveHTML()));
			break;
	}
}

?>