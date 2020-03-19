<?
if ( !defined('SRC') ) require_once('../globals.php');
require_once(INCLUDES.'Elements.php');

class OrderedList
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

	public function _orderedlist($data,$name=null,$tabindex=0) {
		// You don't have to send a container element as a template...
		$list_container = null;
		
        if(isset($data->subcategories)) {
            $this->subs = 'yes';
        }
		
		// But we need to look and see if a list_container element was sent...
		foreach($this->templates AS $t) {
			if($t->name == 'list_parent') { // if we find one, convert it
				$list_container = $t->template;
				$list_container = simplexml_load_string($list_container);
				$list_container = dom_import_simplexml($list_container);
			}
			unset($t); // release it from the object so we don't waste time on it any more
		}
		
		// Start creating the dom
		$dom = new DOMDocument();
		if($list_container) { // IF there's a template for list container...
			$list_container = $dom->importNode($list_container,true); // import it into the dom
			// Now, find the list_parent element inside the imported template
			foreach($list_container->childNodes AS $lc) {
				$attr = $lc->attributes;
				if($attr) {
					foreach($attr AS $k=>$v) {
						if($k == 'id') {
							if($v->nodeValue == 'list_parent') {
								$list_parent = $lc;
							}
						}
					}
				}
			}
		} else { // ELSE create one on the fly...
			$list_container = $dom->createElement('div');
			$list_container->setAttribute('id','list_container');
			$list_parent = $dom->createElement('div');
			$list_parent->setAttribute('id','list_parent');
		}
		
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
					foreach($list_parent->childNodes AS $p) {
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
					$list_parent->appendChild($newparse);
				}
			}
		}
		
		$list_container->appendChild($list_parent);
		$dom->appendChild($list_container);
		return(array(stripslashes(urldecode($dom->saveHTML())),$tabindex));
	}

	public function _section($data) {
		if(isset($data->subcategories)) {
			$this->subs = $data->subcategories;
		}
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

		$title = null;
		
		if(isset($values->title)) {
			$title = $values->title;
		}
		$subject = str_replace($title, $title.'_'.$values->order,$subject);
        
		if($this->subs == 'yes') {
			if(strpos($subject, 'childlist')) {
				$subject = str_replace('childlist', 'childlist_'.$values->order, $subject);
			}
		}
		foreach($values AS $k=>$v) {
			$subject = str_replace('[['.$k.']]',htmlspecialchars(stripslashes(urldecode($v)),ENT_QUOTES,'utf-8',false),$subject);
		}
        if(isset($values->tabindex)) {
            $subject = str_replace('[[tabindex]]',$values->tabindex++,$subject);
        } else {
            $subject = str_replace('tabindex="[[tabindex]]"','',$subject);
        }
		if(strpos($subject, '[[controls]]')){
			$subject = str_replace('[[controls]]','<img src="'.IMAGES.'remove_button.png" onclick="removeElement(this)">&nbsp;<img src="'.IMAGES.'move_button.png" class="handle">',$subject);
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
			$nol = new OrderedList();
			$data = (object) $_POST;
			$res = $nol->_section($data);
			echo stripslashes(urldecode($res->saveHTML()));
			break;
		case 'subsection':
			$nol = new OrderedList();
			$data = (object) $_POST;
			$res = $nol->_subsection($data);
			echo stripslashes(urldecode($res->saveHTML()));
			break;
	}
}

?>