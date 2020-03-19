<?php

//ini_set('display_errors',1);
//error_reporting(E_WARNING);

if ( !defined('SRC') ) require_once('../globals.php');
require_once(SERVICES.'Orders.php');
require_once(SERVICES.'Cookbook.php');
require_once(INCLUDES.'TextFunctions.php');

class MakeXML {
	
    var $allow_notes;
    
	public function makeMilesXML($order_id,$path) {
		// let's start by getting the order
		$no = new Orders();
		$xml = new TextFunctions();
		$retreival_order = '';
		
		$this_order = $no->getComposedOrder($order_id);
        // parse out the variables into something useful
        // Order Information
        
        $added_by_type = $this_order->order->added_by_type;
        
        $add_date = new DateTime($this_order->order->date_added);
        $date_added = $add_date->format('M d, Y');
        
        $modify_date = new DateTime($this_order->order->date_modified);
        $date_modified = $modify_date->format('M d, Y');
        
        $order_title = $this_order->order->title;
        $order_number = $this_order->order->order_number;
        
        $status = 1;
        if(isset($this_order->status)) {
            $status = $this_order->status;
        }
        $title = "EDIT ORDER #".$order_number;
        
        // Meta Information
        $general_info = null;
        $categories = null;
        $subcategories = null;

        foreach($this_order AS $key=>$val) {
            switch($key) {
                case 'general_info':
                    $array_a = explode('|',$val);
                    $array_b = array();
                    foreach($array_a AS $b) {
                        $array_b[substr($b,0,strpos($b,':'))] = substr($b,strpos($b,':')+1);
                    }
                    $general_info = $array_b;
                    if(isset($general_info['allow_notes'])) {
                        $this->allow_notes = $general_info['allow_notes'];
                    }
                    break;
                case 'utypeit_info':
                    $uti_arr = explode('|',$val);
                    $uti_data = array();
                    foreach($uti_arr AS $u) {
                        $data = explode(':',$u);
                        $uti_data[$data[0]] = $data[1];
                    }
                    break;
                case 'categories':
                    // creates array( order:title, order:title, order:title);
                    $categories = json_decode($val);
                    break;
                case 'subcategories':
                    $subcategories = json_decode($val);
                    break;
            }
        }
		
		// compose the final xml output
		$doc = "<?xml version='1.0'?>\n";
		$doc .= '<cookbook';
		
		$meta = $order['meta'];
		
		// all of the attributes for cookbook
		/*
		 id="0001-11" format="text" 
		 continued="yes/no" 
		 fillers="yes/no" 
		 filler_type="text" 
		 filler_set="set name"  
		 index_order="numeric" 
		 contributor_index="yes/no" 
		 icons="yes/no" 
		 nutritionals="yes/no" 
		 -- DEPRECATED -- subtoc="yes/no" 
		 uti="yes/no"
		 order_form="yes/no"
		*/
		$doc .= ' id="'.$this_order->order->order_number.'"';
		$find = array(' ','-');
		$recipe_format = strtolower(str_replace($find,'',$general_info['recipe_format']));
		if($recipe_format == 'welcomehome') {
			$recipe_format = 'welcome';
		}
		$doc .= ' format="'.$recipe_format.'"';
		$doc .= ' continued="'.$general_info['recipes_continued'].'"';
		$doc .= ' fillers="'.$general_info['use_fillers'].'"';
		if($general_info['filler_type'] == 'text_fillers') {
			$filler_type = 'text';
		} elseif($general_info['filler_type'] == 'image_fillers') {
			$filler_type = 'image';
		}
		$doc .= ' filler_type="'.$filler_type.'"';
		$doc .= ' filler_set="'.str_replace(' ','',$general_info['filler_set']).'"';
		$doc .= ' index_order="'.$general_info['order_index_by'].'"';
		$doc .= ' contributor_index="'.$general_info['contributors'].'"';
		$doc .= ' icons="'.$general_info['use_icons'].'"';
		$doc .= ' nutritionals="'.$general_info['nutritionals'].'"';
		//$doc .= ' subtoc="'.$general_info['subtoc'].'"';
		$doc .= ' uti="'.$general_info['uti'].'"';
		$doc .= ' order_form="'.trim($general_info['order_form']).'"';
		if($path == 'CBSOAP') {
			$doc .= ' proof="n"';
		} else {
			$doc .= ' proof="y"';
		}
		
		$doc .= ">\n";
		
		$doc .= "\t<files></files>\n";
		$doc .= "\t<cover></cover>\n";
		$doc .= "\t<dividers></dividers>\n";
		$doc .= "\t<fillers></fillers>\n";
		$doc .= "\t<order_form>";
			//order_form_name,order_form_address1,order_form_address2,order_form_city,order_form_state,order_form_zip,order_form_retail,order_form_shipping
			if($general_info['order_form'] == 'yes') {
				$order_form_name = '';
				if(isset($general_info['order_form_name'])) {
					$order_form_name = htmlentities(stripslashes(urldecode($general_info['order_form_name'])));
				}
				$order_form_address1 = '';
				if(isset($general_info['order_form_address1'])) {
					$order_form_address1 = htmlentities(stripslashes(urldecode($general_info['order_form_address1'])));
				}
				$order_form_address2 = '';
				if(isset($general_info['order_form_address2'])) {
					$order_form_address2 = htmlentities(stripslashes(urldecode($general_info['order_form_address2'])));
				}
				$order_form_city = '';
				if(isset($general_info['order_form_city'])) {
					$order_form_city = htmlentities(stripslashes(urldecode($general_info['order_form_city'])));
				}
				$order_form_state = '';
				if(isset($general_info['order_form_state'])) {
					$order_form_state = htmlentities(stripslashes(urldecode($general_info['order_form_state'])));
				}
				$order_form_zip = '';
				if(isset($general_info['order_form_zip'])) {
					$order_form_zip = htmlentities(stripslashes(urldecode($general_info['order_form_zip'])));
				}
				$order_form_retail = '';
				if(isset($general_info['order_form_retail'])) {
					$order_form_retail = htmlentities(stripslashes(urldecode($general_info['order_form_retail'])));
				}
				$order_form_shipping = '';
				if(isset($general_info['order_form_shipping'])) {
					$order_form_shipping = htmlentities(stripslashes(urldecode($general_info['order_form_shipping'])));
				}
				$doc .= '
				<name>'.$order_form_name.'</name>
				<address1>'.$order_form_address1.'</address1>';
				if(strlen($order_form_address2) > 0) {
					$doc .= '
				<address2>'.$order_form_address2.'</address2>';
				}
				$doc .= '
				<city>'.$order_form_city.'</city>
				<state>'.$order_form_state.'</state>
				<zip>'.$order_form_zip.'</zip>
				<retail>'.$order_form_retail.'</retail>
				<shipping>'.$order_form_shipping.'</shipping>';
			}
		$doc .= "</order_form>\n";
		$doc .= "\t<special_pages";
		$doc .= ' format="'.$recipe_format."\"></special_pages>\n";
		
		$nc = new Cookbook();
		foreach($categories->categories AS $c) {
			
			$parent = $c->parent;
			$category = $c->number;
			$namestr = htmlentities(stripslashes(urldecode($c->name)),ENT_QUOTES,'UTF-8',false);
			
			$doc .= "\t<category>\n";
			$doc .= "\t\t<title>".$namestr."</title>\n";
			
			$retrieval_order = $general_info['order_recipes_by'];
			
			if($general_info['use_subcategories'] == 'yes') {
				// First we have to check and see if there are recipes that belong in the category, but not to any particular subcategory
				$query = "SELECT content_id FROM Order_Content WHERE status='3' AND type='recipe' AND order_id='".$order_id."' ";
					$query .= " AND category='".$category."'";
					$query .= " AND subcategory='0'";
					if($retrieval_order == 'custom') {
						$query .= " ORDER BY list_order,id ASC";
					} elseif($retrieval_order == 'alpha') {
						$query .= " ORDER BY title,id ASC";
					} else {
						$query .= " ORDER BY id";
					}
				$res = $nc->getRecipes($query);
				if($res) {	
					$doc .= "\t\t<recipes>\n";
					foreach($res AS $m) {
						$query = 'SELECT title,subtitle,meta,content FROM Content WHERE id="'.$m->content_id.'"';
						$recipe = $nc->sendAndGetOne($query);
						$doc .= $this->makeRecipe($recipe);
					}
					$doc .= "\t\t</recipes>\n";
				}
				//echo 'CATEGORY: '.$c->name.'-'.count($res).'<br /><br />';
				// Then we have to check and see what recipes belong to each subcategory
				foreach($subcategories->subcategories AS $s) {
					if($s->parent == $category) {
						$subnamestr = htmlentities(stripslashes(urldecode($s->name)),ENT_QUOTES,'UTF-8',false);
						$subcategory = $s->number;

						$subquery = "SELECT content_id FROM Order_Content WHERE status='3' AND type='recipe' AND order_id='".$order_id."' AND category='".$category."' AND subcategory='".$subcategory."'";
						if($retrieval_order == 'custom') {
							$subquery .= " ORDER BY list_order,id ASC";
						} elseif($retrieval_order == 'alpha') {
							$subquery .= " ORDER BY title,id ASC";
						} else {
							$subquery .= " ORDER BY id ASC";
						}
						$subres = $nc->getRecipes($subquery);
						if($subres) {
							$doc .= "\t\t<subcategory>\n";
							$doc .= "\t\t\t<title>".$subnamestr."</title>\n";
							$doc .= "\t\t\t<recipes>\n";
							foreach($subres AS $r) {
								$query = 'SELECT title,subtitle,meta,content FROM Content WHERE id="'.$r->content_id.'"';
								$recipe = $nc->sendAndGetOne($query);
								$doc .= $this->makeRecipe($recipe);
							}
							$doc .= "\t\t\t</recipes>\n";
							$doc .= "\t\t</subcategory>\n";
						}
					}
				}
			} else {
				// Go get the recipes that belong to this category, and order them by the proper order.
				$query = "SELECT id,content_id,title,subtitle FROM Order_Content WHERE status='3' AND type='recipe' AND order_id='".$order_id."' AND category='".$category."'";
				if($retrieval_order == 'custom') {
					$query .= " ORDER BY list_order,id ASC";
				} elseif($retrieval_order == 'alpha') {
					$query .= " ORDER BY title,id ASC";
				} else {
					$query .= " ORDER BY id ASC";
				}
                $res = $nc->sendAndGetMany($query);
				$my_recipes = array();
                for($r=0;$r<count($res);$r++) {
                	$query = "SELECT meta,content FROM Content WHERE id='".$res[$r]->content_id."'";
                    $recipe = $nc->sendAndGetOne($query);
                    $res[$r]->meta = $recipe->meta;
                    $res[$r]->content = $recipe->content;
                    $my_recipes[] = $res[$r];
                }
                $doc .= "\t\t<recipes>\n";
                foreach($my_recipes AS $m) {
                    $doc .= $this->makeRecipe($m);
                }
                $doc .= "\t\t</recipes>\n";
			}
			
			$doc .= "\t</category>\n";
		}
		
		$doc .= '</cookbook>';
		
		return($doc);
	}
	
	// time to add the recipes
	// first we need to create an array of category/subcategory IF there are subcategories
	
	protected function makeRecipe($recipe) {
		$scrubber = new TextFunctions();
        $meta = json_decode($recipe->meta);
        $content= json_decode($recipe->content);
        
		$xml ="\t\t\t<recipe";
        if($meta->recipe_icon) {
    		$icon = $meta->recipe_icon;
    		if($icon != 'none') {
	    		$icn = str_replace(' ','_',$icon);
    		} else {
	    		$icn = '';
    		}
    		$xml .= ' icon="'.$icn.'"';
		}
		
		$xml .= ">\n";
		
		$title = htmlentities(stripslashes(urldecode($recipe->title)),ENT_QUOTES,'utf-8',false);
		$xml .= "\t\t\t\t<title>".$title."</title>\n";
		if($recipe->subtitle) {
			$subtitle = htmlentities(stripslashes(urldecode($recipe->subtitle)),ENT_QUOTES,'utf-8',false);
		} else {
			$subtitle = '';
		}
		
		$xml .= "\t\t\t\t<subtitle>".$subtitle."</subtitle>\n";
		
		$xml .= "\t\t\t\t<contributors>\n";
		
		$contributors = array();
		if($meta->contributors) {
			foreach($meta->contributors AS $c) {
				$first_name = '';
				if($c->contributor->first_name) {
					$first_name = urldecode($c->contributor->first_name);
					$first_name = stripslashes($first_name);
					$first_name = htmlentities($first_name,ENT_QUOTES,'utf-8',false);
					$first_name = trim($first_name);
				}
				
				$last_name = '';
				if($c->contributor->last_name) {
					$last_name = urldecode($c->contributor->last_name);
					$last_name = stripslashes($last_name);
					$last_name = htmlentities($last_name,ENT_QUOTES,'utf-8',false);
					$last_name = trim($last_name);
				}
				
				$credits1 = '';
                if($c->contributor->credits_1) {
    				$credits1 = urldecode($c->contributor->credits_1);
    				$credits1 = stripslashes($credits1);
					$credits1 = htmlentities($credits1,ENT_QUOTES,'utf-8',false);
					$credits1 = trim($credits1);
                }
                
                $credits2 = '';
                if($c->contributor->credits_2) {
    				$credits2 = urldecode($c->contributor->credits_2);
    				$credits2 = stripslashes($credits2);
					$credits2 = htmlentities($credits2,ENT_QUOTES,'utf-8',false);
					$credits2 = trim($credits2);
                }
                
				$xml .= "\t\t\t\t\t<contributor>\n";
				$xml .= "\t\t\t\t\t\t<first_name>".$first_name."</first_name>\n";
				$xml .= "\t\t\t\t\t\t<last_name>".$last_name."</last_name>\n";
				$xml .= "\t\t\t\t\t\t<credits1>".$credits1."</credits1>\n";
				$xml .= "\t\t\t\t\t\t<credits2>".$credits2."</credits2>\n";
				$xml .= "\t\t\t\t\t</contributor>\n";
			}
		}
		$xml .= "\t\t\t\t</contributors>\n";
        
        if(count($content->recipe) > 0) {
            
    		foreach($content->recipe AS $s) {
    			
    		    $section = $s->section;
    			$section_text = '';
    			$section_title = '';
    			
    			if($section->title) {
    				
    				$section_title = htmlentities(stripslashes(urldecode($section->title)),ENT_QUOTES,'utf-8',false);
    			}
                
    			if($section->type == 'ingredient') {
    			    $section_type = 'ingredients';
    				$section_text = "\t\t\t\t\t<ingredients>\n";
    				$ingredients = $section->content;
                    if(count($ingredients) > 0) {
        				for($i=0;$i<count($ingredients);$i++) {
        					$ing = $scrubber->scrubText($ingredients[$i]);
        					$section_text .= "\t\t\t\t\t\t<ingredient>".$ing."</ingredient>\n";
        				}
                    }
    				$section_text .= "\t\t\t\t\t</ingredients>\n";
    			}
    			if($section->type == 'method') {
    				$section_type = 'method';
    				$method = $scrubber->scrubText($section->content);
    				$section_text = "\t\t\t\t\t<text>".$method."</text>\n";
    			}
    			if($section->type == 'note') {
    			    if($this->allow_notes == 'yes') {
        				$section_type = 'note';
        				$note = $scrubber->scrubText($section->content);
        				if($note) {
        				    $section_text = "\t\t\t\t\t<text>".$note."</text>\n";
                        }
                    }
    			}
    			
    			if($section_text != '') {
    				$xml .= "\t\t\t\t<section";
    				$xml .= ' type="'.$section_type.'"';
    				$xml .= ">\n";
    				$xml .= "\t\t\t\t\t<title>".$section_title."</title>\n";
    				$xml .= $section_text;
    				$xml .= "\t\t\t\t</section>\n";
    			}
    		}
    	}
		
		$xml .="\t\t\t</recipe>\n";
		
		return($xml);
	}
	
}
?>