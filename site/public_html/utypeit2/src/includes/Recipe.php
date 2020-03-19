<?

if(!session_id()) { session_start(); };

if ( !defined('SRC') ) require_once('../globals.php');

require_once(INCLUDES.'Elements.php');
require_once(SERVICES.'Cookbook.php');

class Recipe
{
	var $case;
	var $sender;
	var $out;
	var $recipe;
	var $parts;
	var $meta;
	
   function __construct($case=null,$recipe_id=null,$sender=null) {
		
		$this->case = $case;
		$this->sender = $sender;
		
		$this->out = new Elements();
		if($case == 'recipe_edit') {
			 $nc = new Cookbook();
			 $recipe = $nc -> getRecipe($recipe_id);
			 //print_r($recipe);
			 $this->recipe = $recipe;
			 $username = $nc->sendAndGetOne("SELECT CONCAT(first_name, ' ', last_name) AS username FROM People WHERE id='".$recipe->added_by_id."'");
			 $recipe->added_by_name = $username->username;
			 $content = $nc->getRecipeContent($recipe->content_id);
			 //print_r($content);
		} else {
			 $recipe = new stdClass();
			 $recipe->order_id = $_SESSION['order_id' ];
			 $added_by_type = '1';
			 if($_SESSION['user']->level < 6) {
				  $added_by_type = '2';
			 }
			$recipe->added_by_type = $added_by_type;
			$recipe->added_by_id = $_SESSION['user']->id;
			$recipe->added_by_name = $_SESSION['user']->first_name.' '.$_SESSION['user']->last_name;
			$recipe-> category = '';
			$recipe-> subcategory = '';
			$recipe-> type = 'recipe';
			$recipe-> title = '';
			$recipe->subtitle = '';
			$recipe->quantity = '1';
			$recipe->cost_ea = '0';
			$recipe-> price_ea = '0';
			$recipe-> list_order = '0';
			$recipe->date_modified = date('Y-m-d H:i:s');
			$recipe->status = '1';
			$this->recipe = $recipe;
		}
        
		if(isset($content->content)) {
			if($content->content == 'NULL') {
				$content->content = null;
			} else {
				$parts = json_decode($content->content);
				if(isset($parts->recipe)) {
					$parts = $parts->recipe;
					$this->parts = $parts;
				}
			}
		} else {
			$parts = file_get_contents(DATA.'xml/default_recipe.json');
			$parts = json_decode($parts);
			$parts = $parts->recipe;
			$this->parts = $parts;
		}
		$meta = null;
        
		if(isset($content->meta)) {
			$meta = json_decode($content->meta);
		} else {
			$meta = new stdClass();
			$meta->contributors = array();
			if($_SESSION['general_info']->use_icons == 'yes') {
				$meta->recipe_icon = 'none';
			}
		}
		if(isset($meta->recipe_icon)) {
			$this->recipe->icon = $meta->recipe_icon;
		}
		$this->meta = $meta;
   }
	
	public function _draw($case) {
		$form = $this->out->createElement('form', 'name=edit_recipe&id=edit_recipe&onsubmit=return+false;');
		$this->out->appendChild($form);
		if(isset($this->recipe->id)) {
			 $id = $this->out->createElement('input','type=hidden&name=id&value=' . $this->recipe->id);
			 $form->appendChild($id);
		}
		if(isset($this->recipe->content_id)) {
			$content_id = $this->out->createElement('input','type=hidden&name=content_id&value=' . $this->recipe->content_id);
			$form->appendChild($content_id);
		}
		$list_order = $this->out->createElement('input','type=hidden&name=list_order&value=' . $this->recipe->list_order);
		$form->appendChild($list_order);
		$order_id = $this->out->createElement('input','type=hidden&name=order_id&value=' . $this->recipe->order_id);
		$form->appendChild($order_id);
		$action = $this->out->createElement('input','type=hidden&name=action&id=action&value=' . $case);
		$form->appendChild($action);
		$mod_date = date('Y-m-d H:i:s');
		$date_modified = $this->out->createElement('input','type=hidden&name=date_modified&value=' . $mod_date);
		$form->appendChild($date_modified);
		if($case == 'recipe_add') {
			 // type 1 = admin
			 // type 2 = uti customer
			 $added_by_type = $this->out->createElement('input','type=hidden&name=added_by_type&value='.$this->recipe->added_by_type);
			 $form->appendChild($added_by_type);
			 $added_by_id = $this->out->createElement('input','type=hidden&name=added_by_id&value=' .	 $this->recipe->added_by_id );
			 $form->appendChild($added_by_id);
		}
		$added_by_name = $this->out->createElement('input','type=hidden&name=added_by_name&value=' .	 $this->recipe->added_by_name );
		$form->appendChild($added_by_name);
		$datablock = $this->_recipedata();
		$form->appendChild($datablock);
		$contributorblock = $this->_recipecontributors();
		$form->appendChild($contributorblock);
		$sectionsblock = $this->_recipesections();
		$form->appendChild($sectionsblock);
		return(stripslashes($this->out->saveHTML()));
	}
	
	public function _recipedata() {
		$out = $this->out;
		$table = $out->createElement('table','id=datablock');
		$toshow = array('icon','status','date_added','date_modified','category','subcategory','title','subtitle');
		$this_category = '';
		$tr = $out->createElement('tr');
		if(isset($_SESSION['general_info'])) {
			foreach($_SESSION['general_info'] AS $key=>$val) {
				$$key = $val;
			}
		}
		
		for($i=0;$i<count($toshow);$i++) {
			 if($i%2===0) {
				if($i>1) {
					$tr = $out->createElement('tr');
				}
				$table->appendChild($tr);
			}
		    
            foreach($this->recipe AS $key=>$val) {
                if($key == $toshow[$i]) {
                    $$key = $val;
                }
            }
            
            switch($toshow[$i]) {
                case 'icon':
                    if($use_icons == 'yes') {
                        if(!$icon) {
                            $icon = 'none';
                        }
                        $label = $out->createElement('td','class=formLabel&id='.$key.'_label',ucfirst(str_replace('_', ' ', 'icon')).': ');
                        $tr->appendChild($label);
                        $td = $out->createElement('td','class=formInput');
                        $element = $out->createElement('input','type=hidden&name=recipe_icon&id=recipe_icon&value='.$icon);
                        $td->appendChild($element);
                        $img = $out->createElement('img','src='.IMAGES.'recipe_icons/icon_'.strtolower(str_replace(' ','_',$icon)).'.png&width=25&id=recipe_icon_img');
                        $desc = $out->createElement('span','class=iconDesc&id=iconDesk','('.$icon.')');
                        $td->appendChild($img);
                        $td->appendChild($desc);
                        $tr->appendChild($td);
                    } else {
                        $label = $out->createElement('td','class=formLabel',' ');
                        $tr->appendChild($label);
                        $td = $out->createElement('td','class=formInput',' ');
                        $tr->appendChild($td);
                    }
                    break;
                case 'status':
                    $label = $out->createElement('td','class=formLabel&id=status_label',ucfirst(str_replace('_', ' ', 'status')).': ');
                    $tr->appendChild($label);
                    $td = $out->createElement('td','class=formInput');
                    $opts = new stdClass();
                    $opts->{'0'} = 'Inactive';
                    $opts->{'1'} = 'DataEntry';
                    $opts->{'2'} = 'Editorial';
                    $opts->{'3'} = 'Approved';
                    $select = $this->_optionlist('status',$opts,$status);
                    if($status < 3) {
	                    $note = $out->createElement('span','class=note','This recipe will not be printed in your cookbook unless the status is saved as "Approved"');
	                }
                    $td->appendChild($select);
                    if($status < 3) {
                    	$td->appendChild($note);
                    }
                    $tr->appendChild($td);
                    break;
                case 'date_added':
                    if(!isset($date_added)) {
                        $date_added = date('Y-M-d H:i:s');
                    }
                    $label = $out->createElement('td','class=formLabel&id=date_added_label',ucfirst(str_replace('_', ' ', 'date_added')).': ');
                    $tr->appendChild($label);
                    $td = $out->createElement('td','class=formInput&id=date_added',date('M d, Y', strtotime($date_added)));
                    $tr->appendChild($td);
                    break;
                case 'date_modified':
                    $label = $out->createElement('td','class=formLabel&id=date_modified_label',ucfirst(str_replace('_', ' ', 'date_modified')).': ');
                    $tr->appendChild($label);
                    $td = $out->createElement('td','class=formInput&id=date_modified',date('M d, Y', strtotime($date_modified)));
                    $tr->appendChild($td);
                    break;
                case 'category':
                    $label = $out->createElement('td','class=formLabel&id=category_label',ucfirst(str_replace('_', ' ', 'category')).': ');
                    $req = $out->createElement('span','style=color:#F00','*');
                    $label->appendChild($req);
                    $tr->appendChild($label);
                    $td = $out->createElement('td','class=formInput');
                    $categories = new stdClass();
                    if(isset($_SESSION['categories'])) {
	                    foreach($_SESSION['categories']->categories AS $c) {
	                         $categories->{$c->number} = htmlentities(stripslashes(urldecode($c->name)),ENT_QUOTES,'utf-8',false);
							 }
					}
                    $select = $this->_optionlist('category',$categories,$category);
                    $td->appendChild($select);
                    $tr->appendChild($td);
                    break;
                case 'subcategory':
                    if(isset($_SESSION['subcategories'])) {
                        $subcats = array();
                        foreach($_SESSION['subcategories']->subcategories AS $s) {
                            if($s->parent == $category) {
                                $subcats[] = $s;
                            }
                        }
                        
                        $label = $out->createElement('td','class=formLabel&id=subcategory_label',ucfirst(str_replace('_', ' ', 'subcategory')).': ');
                        $tr->appendChild($label);
                        $td = $out->createElement('td','class=formInput');
                        $subcategories = new stdClass();
                        foreach($subcats AS $s) {
                            $subcategories->{$s->number} = htmlentities(stripslashes(urldecode($s->name)),ENT_QUOTES,'utf-8',false);
                        }
                        $select = $this->_optionlist('subcategory',$subcategories,$subcategory);
                        $td->appendChild($select);
                        $tr->appendChild($td);
                    } else {
                        $td = $out->createElement('td','colspan=2',' ');
                        $tr->appendChild($td);
                    }
                    break;
                case 'title':
                        $countdown = 60;
                        if($title) {
                            $countdown = $countdown - strlen(urldecode($title));
                        }
                        
                        //$title = rawurldecode($title);
                        //$title = urldecode($title);
                        //$newtitle = html_entity_decode($title);
                        //$newtitle = str_replace('&#039;','\'', $newtitle);
                        //$newtitle = htmlentities($newtitle);
                        //$title = rawurlencode($title);                        
                        
                        $countdown = $out->createElement('span','id=countdown&class=countdown&style=padding-left:5px',$countdown);                        
                        $label = $out->createElement('td','class=formLabel&id=title_label',ucfirst(str_replace('_', ' ', 'title')).': ');
                        $req = $out->createElement('span','style=color:#F00','*');
                    	$label->appendChild($req);
                        $tr->appendChild($label);
                        $td = $out->createElement('td','class=formInput');
                        $input = $out->createElement('input','type=text&name=title&id=title&spellcheck=true&onkeydown='.urlencode("limitText(this,'td',60)").'&onkeyup='.urlencode("limitText(this,'td',60)").'&onfocus=setSecret(this)&maxlength=60&value='.$title);
                        $td->appendChild($input);
                        $td->appendChild($countdown);
                        $tr->appendChild($td);
                    break;
                case 'subtitle':
                        $countdown = 60;
						$countdown = $countdown - strlen(urldecode($subtitle));
						
						/*$subtitle = urldecode($subtitle);
						$subtitle = urldecode($subtitle);
						$newsubtitle = html_entity_decode($subtitle);
						$newsubtitle = str_replace('&#039;','\'', $newsubtitle);
						$newsubtitle = htmlentities($newsubtitle);
						$subtitle = urlencode($newsubtitle);*/

                        $countdown = $out->createElement('span','id=countdown&class=countdown&style=padding-left:5px',$countdown);
                        $label = $out->createElement('td','class=formLabel&id=subtitle_label',ucfirst(str_replace('_', ' ', 'subtitle')).': ');
                        $tr->appendChild($label);
                        $td = $out->createElement('td','class=formInput');
                        $input = $out->createElement('input','type=text&name=subtitle&id=subtitle&spellcheck=true&onkeydown='.urlencode("limitText(this,'td',60)").'&onkeyup='.urlencode("limitText(this,'td',60)").'&onfocus=setSecret(this)&maxlength=60&value='.$subtitle);
                        $td->appendChild($input);
                        $td->appendChild($countdown);
                        $tr->appendChild($td);
                        
                    break;
            }
		}
		return($table);
	}
	
	protected function _recipecontributors() {
		$contributors = array();
		if(isset($this->meta->contributors)) {
			if(count($this->meta->contributors) > 0) {
				$contributors = $this->meta->contributors;
			} else {
			    $contributor = new stdClass();
                $contributor->first_name = '';
                $contributor->last_name = '';
                $contributor->credits_1 = '';
                $contributor->credits_2 = '';
                $contributors[0] = new stdClass();
                $contributors[0]->contributor = $contributor;
			}
		} else {
			$contributor = new stdClass();
            $contributor->first_name = '';
            $contributor->last_name = '';
            $contributor->credits_1 = '';
            $contributor->credits_2 = '';
            $contributors[0] = new stdClass();
            $contributors[0]->contributor = $contributor;
		}
		$total = count($contributors);
		if($this->sender == 'admin') {
			$headers = array('First Name','Last Name','Credits 1','Credits 2','Order','Delete');
		} else {
			$headers = array('First Name','Last Name','Contributor Information','Order','Delete');
		}
		$out = $this->out;
		$table = $out->createElement('table','id=contributors');
		$tr = $out->createElement('tr');
		$th = $out->createElement('th','class=title&colspan=4','Recipe Contributors');
		$help = $out->createElement('a','href='.HELP.'contributors.html&rel=ibox&title=Recipe+Contributors&class=lightwindow+help&params=lightwindow_width%3D500%2Clightwindow_height%3D300','?');
		$th->appendChild($help);
		$tr->appendChild($th);
		$th = $out->createElement('th','class=title&colspan=2');
		$button = $out->createElement('button','type=button&onclick=_contributors._add();return+false;&style=width:150px','Add Contributor');
		$th->appendChild($button);
		$tr->appendChild($th);
		$table->appendChild($tr);
		$tr = $out->createElement('tr','id=headers');
		foreach($headers AS $k=>$v) {
			$attr = '';
			if($v == 'Delete' || $v == 'Order') {
				$attr = 'style=width:20px';
			} else {
				if($v == 'Contributor Information') {
					$attr = 'style=width:400px';
				} else {
					$attr = 'style=width:200px';
				}
			}
			$th =$out->createElement('th',$attr,$v);
			$tr->appendChild($th);
		}
		$table->appendChild($tr);
        
		if($contributors) {
			$tr = $out->createElement('tr');
			$td = $out->createElement('td','id=contributor_list&colspan='.count($headers));
			$i = 0;
			foreach($contributors AS $c) {
				if(isset($c->contributor)) {
					$div = $this->_contributor($headers,$total,($i+1),$c->contributor);
					$td->appendChild($div);
					$i++;
				}
			}
			$tr->appendChild($td);
			$table->appendChild($tr);
		}
		
		return($table);
	}

	public function _contributor($headers,$total,$iterant,$contributor=null,$sender=null) {
		$out = $this->out;
		$div = $out->createElement('div','id=contributors_'.$iterant.'&class=contributor');
		for($h=0;$h<count($headers);$h++) {
			$value = '';
			$child = $out->createElement('div','style='.urlencode('display:inline-block;float:left'));
			$key = strtolower(str_replace(' ','_', $headers[$h]));
			if($headers[$h] == 'Delete') {
				if($total > 1) {
					$attr = 'src='.IMAGES.'remove_button.png';
					$attr .= '&onclick=_contributors._remove(this)';
					$img = $out->createElement('img',$attr);
					$child->appendChild($img);
				}
			} elseif($headers[$h] == 'Order') {
				if($total > 1) {
					$attr = 'src='.IMAGES.'move_button.png&class=handle';
					$img = $out->createElement('img',$attr);
					$child->appendChild($img);
				}
			/* -------------------------------- */
			} elseif($headers[$h] == 'Contributor Information') {
				
				$countdown = 50;
				if($contributor) {
					foreach($contributor AS $k=>$v) {
					  if($k == 'credits_1') {
						  //$value = htmlspecialchars(urldecode($v),ENT_QUOTES,'utf-8',false);
						  $value = $v;
						  $countdown = $countdown - strlen($value);
					  }
					}
				}
				if($sender == 'admin') {
					$input = $out->createElement('input','type=text&name=contributor-'.$iterant.'-credits_1&id=contributor-'.$iterant.'_credits_1&size=35&value='.$value);
					$child->appendChild($input);
				} else {
					$input = $out->createElement('input','type=text&name=contributor-'.$iterant.'-credits_1&id=contributor-'.$iterant.'_credits_1&onkeydown='.urlencode("limitText(this,'div',35)").'&onfocus=setSecret(this)&onkeyup='.urlencode("limitText(this,'div',50)").'&maxlength=50&size=35&value='.$value);
					$countdown = $out->createElement('span','id=countdown&class=countdown',$countdown);
					$child->appendChild($countdown);
					$child->appendChild($input);
				}
			/* -------------------------------- */
			} else {
				$value = '';
				if($headers[$h] == 'Credits 1' || $headers[$h] == 'Credits 2') {
					$countdown = 50;
				} else {
					$countdown = 35;
				}
				$subtracted = $countdown;
				if($contributor) {
					foreach($contributor AS $k=>$v) {
					  if($k == $key) {
						  //$value = htmlspecialchars(urldecode($v),ENT_QUOTES,'utf-8',false);
						  $value = $v;
						  $subtracted = $countdown - strlen($value);
					  }
					}
				}
				if($sender == 'admin') {
					$input = $out->createElement('input','type=text&name=contributor-'.$iterant.'-'.$key.'&id=contributor-'.$iterant.'_'.$key.'&size=17&value='.$value);
					$countdown = $out->createElement('span','id=countdown&class=countdown','&nbsp;&nbsp;');
					$child->appendChild($countdown);
					$child->appendChild($input);
				} else {
					$input = $out->createElement('input','type=text&name=contributor-'.$iterant.'-'.$key.'&id=contributor-'.$iterant.'_'.$key.'&onkeydown='.urlencode("limitText(this,'div',".$countdown.")").'&onfocus=setSecret(this)&onkeyup='.urlencode("limitText(this,'div',".$countdown.")").'&maxlength='.$countdown.'&size=17&value='.$value);
					$countdown = $out->createElement('span','id=countdown&class=countdown',$subtracted);
					$child->appendChild($countdown);
					$child->appendChild($input);
				}
			}
			$div->appendChild($child);
		}
		return($div);
	}
	
	protected function _recipesections() {
		
		$out = $this->out;
		
		$table = $out->createElement('table','id=sections');
		$tr = $out->createElement('tr');
		$th = $out->createElement('th','class=title','Recipe Parts');
		$help = $out->createElement('a','href='.HELP.'recipe_parts.html&rel=ibox&title=Recipe+Parts&class=lightwindow+help&params=lightwindow_width%3D500%2Clightwindow_height%3D300','?');
		$th->appendChild($help);
		$tr->appendChild($th);
		$th = $out->createElement('th','class=title&style=width:160px');
		$button = $out->createElement('button','type=button&id=add_section&onclick=_recipesections._add(this)&style=width:150px','Add Recipe Part');
		$th->appendChild($button);
		$tr->appendChild($th);
		$table->appendChild($tr);
		$tr = $out->createElement('tr');
		$td = $out->createElement('td','id=sections_list&colspan=2');
		$note = null;
		$allow_notes = $_SESSION['general_info']->allow_notes;
		if($allow_notes == 'yes') {
			$note = ' ';
			for($p=0;$p<count($this->parts);$p++) {
				 if($this->parts[$p]->section->type == 'note') {
					 $note = $this->parts[$p]->section->content;
				}
			}
		}
		
		$total = count($this->parts);
		// There must be at least two parts for each recipe.
		if($note) {
			$possible = 3;
		} else {
			$possible = 2;
		}
		if($total < $possible) { // If there's less than the total possible...
			$parts = file_get_contents(DATA.'xml/default_recipe.json');
			$parts = json_decode($parts);
			$parts = $parts->recipe;
			// Find out what parts are present...
			$method = false;
			$ingredients = false;
			// If no one is present, pass over the check and just add the missing parts...
			if($total > 0) {
				// If (at least) one is present, find out which one...
				foreach($this->parts AS $p) {
					if($p->section->type == 'ingredient') {
						$ingredients = true;
					}
					if($p->section->type == 'method') {
						$method = true;
					}
				}
			}
			// Create a generic part for the part(s) missing...
			if(!$ingredients) {
				$this->parts[] = $parts[0];
			}
			if(!$method) {
				$this->parts[] = $parts[1];
			}
		}
		$i = 0;
		foreach($this->parts AS $p) {
			$section = $this->_recipesection($i+1,$total,$p);
			if($section) {
				$td->appendChild($section);
			}
			$i++;
		}
		
		$tr->appendChild($td);
		$table->appendChild($tr);
		if($note) {
			$tr = $out->createElement('tr');
			$td = $out->createElement('td','colspan=2');
            $i++;
			$td->appendChild($this->_note($i,$note));
		}
		$tr->appendChild($td);
		$table->appendChild($tr);
		
		return($table);
	}

	public function _recipesection($count,$total=null,$object=null,$sender=null) {
		
		$type = '';
		$ingselected = '';
		$metselected = '';
		if($object) {
			$object = $object->section;
			$type = $object->type;
			if($type == 'ingredient') {
				$ingselected = '&checked="checked"';
			} else if($type == 'method') {
				$metselected = '&checked="checked"';
			} else if($type == 'note') {
				return;
			}
		}
		
		$out = $this->out;

		if(!$total) {
			$total = 3;
		}
		
		$section = $out->createElement('div','id=section_'.$count.'&class=recipeSection');
		$header = $out->createElement('div','class=recipeSectionHeader');
		$label = $out->createElement('label','style=font-weight:bold;width:100px','Recipe Part Type: ');
		$header->appendChild($label);
		$label = $out->createElement('label','','Ingredients List');
		$ing = $out->createElement('input','type=radio&id=ingredients&name=section-type_'.$count.'&value=ingredient'.$ingselected.'&onclick=_recipesections._settype(this)');
		$label->appendChild($ing);
		$header->appendChild($label);
		$label = $out->createElement('label','','Recipe Method');
		$met = $out->createElement('input','type=radio&id=method&name=section-type_'.$count.'&value=method'.$metselected.'&onclick=_recipesections._settype(this)'); 
		$label->appendChild($met);
		$header->appendChild($label);
		$hidden = $out->createElement('input','type=hidden&id=sectiontype&value='.$type);
		$header->appendChild($hidden);
		$controls = $out->createElement('div','id=controls_'.$count.'&class=recipeSectionControls');
		if($total > 0) {
				$desc = $out->createElement('span','','Remove Part ');
				$controls->appendChild($desc);
				$img = $out->createElement('img','src='.IMAGES.'move_button.png&class=handle');
				$controls->appendChild($img);
				$img = $out->createElement('img','src='.IMAGES.'remove_button.png&id=remove_section&onclick=_recipesections._remove(this)&style=margin-right:50px&alt=Remove+Recipe+Section');
				$controls->appendChild($img);
		}
		$header->appendChild($controls);
		$section->appendChild($header);
		$content = $out->createElement('div','id=section_content&class=recipeSectionContent');
		$ingredients = null;
		$title = '';
		if($type == 'ingredient') {
			$ingredients = $object->content;
			if(isset($object->title)) {
				$title = htmlspecialchars($object->title,ENT_QUOTES,'utf-8',true);
			}
		}
        
		$content->appendChild($this->_ingredients($count,$type,$ingredients,$title,$sender));
		$method = null;
		if($type == 'method') {
			$method = $object->content;
			//$title = $object->title;
		}
		$content->appendChild($this->_method($count,$type,$method,$title));
		$section->appendChild($content);
		return($section);
	}
	
	protected function _ingredients($count,$type,$array=null,$title=null,$sender=null) {
		$out = $this->out;
		
		$show = '';
		if($type != 'ingredient') {
			$show = '&style=display:none';
		}
		if($sender == 'admin') {
			$total = 1;
		} elseif($sender == 'uti') {
			$total =  6;
		} else {
			if(isset($this->sender)) {
				if($this->sender == 'admin') {
					$total = 1;
				} else {
					$total =  6;
				}
			} else {
				$total = 6;
			}
		}
		if($array[0]) {
			$total = count($array);
		}
		
		$display = null;
		$checked = '&checked=checked';
		if(!$title) {
			$checked = null;
			$display = '&style=display:none';
		}
		
		$ingredientsbox = $out->createElement('div','id=ingredients-box_'.$count.$show.'&class=ingredientsBox');
		$titlebox = $out->createElement('div','class=recipeSectionTitleBox');
		$label = $out->createElement('label','','Add a Title to the Ingredients?');
		$checkbox = $out->createElement('input','type=checkbox&onclick=_recipesections._addtitle(this)'.$checked.'&id=ingredients-title-check');
		$label->appendChild($checkbox);
		$titlebox->appendChild($label);
		$title = $out->createElement('input','type=text&name=ingredient-title_'.$count.'&id=ingredient-title'.$display."&spellcheck=true&value=".$title."&onfocus=setSecret(this)");
		$titlebox->appendChild($title);
		$ingredientsbox->appendChild($titlebox);
		$ingredientslist = $out->createElement('div','id=ingredients-list_'.$count.'&class=ingredientsList');
		for($i=0;$i<$total;$i++) {
			$ingredientslist->appendChild($this->_ingredient($total,$count,($i+1),$array[$i]));
		}
		$ingredientsbox->appendChild($ingredientslist);
		return($ingredientsbox);
	}
	
	protected function _method($count,$type,$method=null,$title=null) {
		
		$out = $this->out;
		
		$show = '';
		if($type != 'method') {
			$show = '&style=display:none';
		}
		
		/*$display = null;
		$checked = '&checked=checked';
		if(!$title) {
			$checked = null;
			$display = '&style=display:none';
		}*/
		
		$methodbox = $out->createElement('div','id=method-box_'.$count.$show.'&class=methodBox');
		$titlebox = $out->createElement('div','class=recipeSectionTitleBox');
		$label = $out->createElement('label','','Tip: You can use a ^ (shift + 6) instead of a degree symbol. Note: Only one paragraph is allowed in the recipe method box. If you add returns between lines in this field, your method will still appear as one paragraph.');
		/*$checkbox = $out->createElement('input','type=checkbox&onclick=_recipesections._addtitle(this)'.$checked.'&id=method-title-check');
		$label->appendChild($checkbox);*/
		$titlebox->appendChild($label);
		/*$title = $out->createElement('input','type=text&name=method-title_'.$count.'&id=method-title'.$display."&value=".urlencode($title));
		$titlebox->appendChild($title);*/
		$methodbox->appendChild($titlebox);
        $met = htmlspecialchars(stripslashes(urldecode($method)),ENT_QUOTES,'utf-8',false);
		$textarea = $out->createElement('textarea','id=method_'.$count.'&name=method_'.$count.'&onfocus=setSecret(this)',$met);
		$methodbox->appendChild($textarea);
		return($methodbox);
	}
	
	protected function _note($count,$note=null) {
		
		$out = $this->out;

		$string = null;
		$display = null;
		$checked = '&checked=checked';
		if($note) {
			$string = htmlspecialchars(stripslashes(urldecode($note)),ENT_QUOTES,'utf-8',false);
			$string = trim($string);
			if(!$string) {
				$checked = null;
				$display = '&style=display:none';
			}
		} else {
			$checked = null;
			$display = '&style=display:none';
		}
		$note = $out->createElement('div','id=section_'.$count.'&class=recipeNote');
		$header = $out->createElement('div','class=recipeNoteHeader');
		$label = $out->createElement('label','style=font-weight:bold;width:100px','Recipe Note ');
		$header->appendChild($label);
		$note->appendChild($header);
		$content = $out->createElement('div','id=section_content&class=recipeNoteContent');
		$titlebox = $out->createElement('div','class=recipeNoteTitleBox');
		$label = $out->createElement('label','id=note-check','Add a Note?');
		$checkbox = $out->createElement('input','type=checkbox&name=section-type_'.$count.'&id=note_select&value=note'.$checked.'&onclick=_recipesections._addnote(this)');
		$label->appendChild($checkbox);
		$desc = $out->createElement('span','class=label','Recipe Notes are limited to 350 characters maximum.');
		$titlebox->appendChild($label);
        $titlebox->appendChild($desc);
		$content->appendChild($titlebox);
		$textarea = $out->createElement('textarea','id=note&name=note_'.$count.'&maxlength=350&onfocus=setSecret(this)'.$display,$string);
		$content->appendChild($textarea);
		$note->appendChild($content);
		return($note);
	}
	 
	public function _ingredient($total,$count,$iterant,$item=null) {
		$out = $this->out;	  
		
		$countdown = 100;
		if($item) {
            $countdown = $countdown - strlen($item);
        }
		$ingredientbox = $out->createElement('div','id=ingredient_'.$iterant.'&class=ingredientItem');
		$countdown = $out->createElement('span','id=countdown&class=countdown',$countdown);
		$ingredientbox->appendChild($countdown);
		$ingredient = $out->createElement('input','type=text&name=ingredient-'.$count.'_'.$iterant.'&id=ingredient&onkeydown='.urlencode("limitText(this,'div',100)").'&onkeyup='.urlencode("limitText(this,'div',100)").'&value='.$item.'&spellcheck=true&maxlength=100&onfocus=setSecret(this)');
		$ingredientbox->appendChild($ingredient);
		$controls = $out->createElement('div','id=controls_'.$iterant.'&class=ingredientItemControls');
		if($total > 1) {
			$img = $out->createElement('img','src='.IMAGES.'remove_button.png');
			$button = $out->createElement('button','type=button&class=ing&id=remove_ingredient&onclick=_recipesections._remove(this);return+false;&style=margin-right:50px');
			$button->appendChild($img);
			$controls->appendChild($button);
			if($iterant < $total) {
				$img = $out->createElement('img','src='.IMAGES.'blank.png&id=blank&style=margin-right:50px;cursor:default');
				$controls->appendChild($img);
			} else {
				$img = $out->createElement('img','src='.IMAGES.'add_button.png');
				$button = $out->createElement('button','type=button&class=ing&id=add_ingredient&onclick=_recipesections._add(this);return+false;&style=margin-right:50px');
				$button->appendChild($img);	
				$controls->appendChild($button);
			}
			$img = $out->createElement('img','src='.IMAGES.'move_button.png&class=handle');
			$controls->appendChild($img);
		} else {
			$img = $out->createElement('img','src='.IMAGES.'add_button.png');
			$button = $out->createElement('button','type=button&class=ing&id=add_ingredient&onclick=_recipesections._add(this);return+false;&style=margin-right:50px');
			$button->appendChild($img);
			$controls->appendChild($button);
		}
		$ingredientbox->appendChild($controls);
		return($ingredientbox);
		
	}
	
	protected function _optionlist($name,$list,$value) {
		$out = $this->out;
		$select = $out->createElement('select','id='.$name.'&name='.$name);
		$option = $out->createElement('option','value=',' -- ');
		$select->appendChild($option);
		foreach($list AS $key=>$val) {
			$selected = '';
				if($value == $key) {
					$selected = '&selected=selected';
				}
				$option = $out->createElement('option','value='.$key.$selected,$val);
				$select->appendChild($option);
		}
		return($select);
	}
}

if(isset($_POST['action'])) {
	$action = $_POST['action'];
	$section = null;
	$nr = new Recipe();
	switch($action) {
		case 'contributor':
			$headers = explode(',',$_POST['headers']);
			$total = ($_POST['total']+1);
			$iterant = $total;
			$sender = '';
			if(isset($_POST['sender'])) {
				$sender = $_POST['sender'];
			}
			$section = $nr->_contributor($headers,$total,$iterant,'',$sender);
			$nr->out->appendChild($section);
			break;
		case 'section':
			$total = ($_POST['total']+1);
			$count = $total;
			$iterant = '';
			$sender = '';
			if(isset($_POST['sender'])) {
				$sender = $_POST['sender'];
			}
			$ingredient = $nr->_recipesection($count,$total,$iterant,$sender);
			$nr->out->appendChild($ingredient);
			break;
		case 'ingredient':
			$total = ($_POST['total']+1);
			$parent = ($_POST['parent']);
			$iterant = $total;
			$ingredient = $nr->_ingredient($total,$parent,$iterant);
			$nr->out->appendChild($ingredient);
			break;
	}
	echo stripslashes($nr->out->saveHTML());
}

?>