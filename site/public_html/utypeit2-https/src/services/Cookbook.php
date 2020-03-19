<?php
/*
 * 
 *	Cookbook
 *	by William Logan, 2012
 * 
 *	Handles all data functions for saving and retrieving recipes.
 * 
 */
 
//ini_set('display_errors',1);
//error_reporting(-1);

if ( !defined('SRC') ) require_once('../globals.php');

require_once(SERVICES.'BaseService.php');
require_once(INCLUDES.'Elements.php');

class Cookbook extends BaseService
{
	
	public function addNewRecipe($item,$response=null) {
		/*
		 * 
		 *	make Content data
		 *	id, title, meta, content, date_added, date_modified, status
		 * 
		 */
		 $mod_date = date('Y-m-d H:i:s');
		 // make Content.meta data
		 // meta : contributors, icon, share
		$meta = $this->makeMeta($item);
		$content = $this->makeRecipe($item);
				$title = $this->scrub($item['title']);
				$subtitle = $this->scrub($item['subtitle']);
		$query = "INSERT INTO Content (title, subtitle, meta, content, date_modified, status) 
														VALUES 
																	('".$title."','".$subtitle."','".$meta."','".$content."','".$mod_date."','1')";
		$res = $this->insertAndGetOne($query);
		 if($res) {
			$content_id = $res;
			/*
			 * 
			 *	make Order_Content data
			 * 
			 *	id, order_id, content_id, added_by_type, added_by_id, 
			 *	category, subcategory, type, title, subtitle, quantity, cost_ea, 
			 *	price_ea, list_order, date_added, date_modified,status
			 * 
			 */
			 $subcategory = '0';
			 if(isset($item['subcategory']) && is_numeric($item['subcategory'])) {
				 $subcategory = $item['subcategory'];
			 }
			$query = "INSERT INTO Order_Content (order_id,content_id,added_by_type,added_by_id,category,subcategory,type,title,subtitle,quantity,share,list_order,date_modified,status) VALUES ('".$item['order_id']."','".$content_id."','".$item['added_by_type']."','".$item['added_by_id']."','".$item['category']."','".$subcategory."','recipe','".$title."','".$subtitle."','1','0','0','".$mod_date."','".$item['status']."')";
			$cres = $this->insertAndGetOne($query);
			if($cres) {
				$add_to_query = 'UPDATE Order_Data SET total_content = total_content+1 WHERE order_id="'.$item['order_id'].'"';
				$this->sendAndGetOne($add_to_query);
				return("{\"status\": \"true\", \"id\":\"".$cres."\",\"result\":\"".$response."\",\"message\":\"Item Saved\"}");
			} else {
				return("{\"status\":\"false\", \"message\": \"Error Saving Content\"".$cres."}");
			}
		 } else {
			 die('Error Adding Recipe:'.$res);
		 }
		 
	}
	
	public function updateRecipe($item,$response=null) {
		 /*
		 * 
		 *	make Content data
		 * 
		 *	id, title, meta, content, date_added, date_modified, status
		 *	meta : contributors, icon, share
		 * 
		 */
		$mod_date = date('Y-m-d H:i:s');
		$meta = $this->makeMeta($item);
		$content = $this->makeRecipe($item);
			$title = $this->scrub($item['title']);
			$subtitle = $this->scrub($item['subtitle']);
		$query = "UPDATE Content SET title='".$title."', subtitle='".$subtitle."', meta='".$meta."', content='".$content."', date_modified='".$mod_date."', status='".$item['status']."' WHERE id='".$item['content_id']."'";
		$res = $this->sendAndGetOne($query);
		if(!$res) {
			/*
			 * 
			 *	make Order_Content data
			 * 
			 *	id, order_id, content_id, added_by_type, added_by_id, 
			 *	category, subcategory, type, title, subtitle, quantity, cost_ea, 
			 *	price_ea, list_order, date_added, date_modified,status
			 * 
			 */
			$subcategory = '0';
			if(isset($item['subcategory']) && is_numeric($item['subcategory'])) {
				$subcategory = $item['subcategory'];
			}
			$query = "UPDATE Order_Content SET category='".$item['category']."',subcategory='".$subcategory."',title='".$title."',subtitle='".$subtitle."',list_order='".$item['list_order']."',date_modified='".$mod_date."', status='".$item['status']."' WHERE id='".$item['id']."'";
			//echo $query;
			$res = $this->sendAndGetOne($query);
			return( "{\"status\": \"true\", \"id\":\"".$item['id']."\",\"result\":\"".$response."\",\"message\":\"Item Saved\"}");
		} else {
			die('Error Saving Recipe:'.$res);
		}
	}
		
	public function getRecipeList($order_id,$person_id='',$start='',$limit='',$orderby='') {
		if($person_id) {
			$query = "SELECT * FROM Order_Content WHERE type='recipe' AND order_id='".$order_id."' AND added_by_id='".$person_id."'";
		} else {
			$query = "SELECT * FROM Order_Content WHERE type='recipe' AND order_id='".$order_id."'";
		}
		if($orderby) {
			$query .= " ORDER BY ".$orderby;
		} else {
			$query .= " ORDER BY id ASC";
		}
		if($limit) {
			$query .= " LIMIT ".$start.",".$limit;
		}
				
		$result = $this->sendAndGetMany($query);
		return($result);
	}
	
	public function getRecipesQualified($order_id,$category=null,$subcategory=null) {
		$query = "SELECT * FROM Order_Content WHERE order_id='".$order_id."'";
		if($category) {
			$query .= " AND category='".$category."'";
			if($subcategory) {
				$query .= " AND subcategory='".$subcategory."'";
			} else {
				$query .= " AND subcategory='0'";
			}
		} else {
			$query .= " AND category='0' AND subcategory='0'";
		}
		$query .= " ORDER BY list_order,id ASC";
		$res = $this->sendAndGetMany($query);
		return($res);
	}
	
	public function getRecipeCount($order_id) {
		$res = $this->sendAndGetOne('SELECT COUNT(*) AS COUNT FROM Order_Content WHERE order_id="'.$order_id.'"');
		return( $res->COUNT );
	}
	
	public function getRecipe($recipe_id) {
		$query = "SELECT * FROM Order_Content WHERE id='".$recipe_id."'";
		$result = $this->sendAndGetOne($query);
		return($result);
	}
	
	public function getRecipeContent($content_id) {
		$query = "SELECT * FROM Content WHERE id='".$content_id."'";
		$result = $this->sendAndGetOne($query);
		return($result);
	}
	
	public function getRecipes($query){
		$result = $this->sendAndGetMany($query);
		return($result);
	}
	
	public function getCategories($order_id) {
		$query = "SELECT value FROM Order_Meta WHERE order_id='".$order_id."' and name='categories'";
		$res = $this->sendAndGetOne($query);
		return($res);
	}
	
	public function getSubcategories($order_id) {
		$query = "SELECT value FROM Order_Meta WHERE order_id='".$order_id."' and name='subcategories'";
		$res = $this->sendAndGetOne($query);
		return($res);
	}
	
	public function deleteRecipe($id) {
		// get the actual content id
		$query = 'SELECT Content.id,Order_Content.order_id FROM Content,Order_Content WHERE Order_Content.content_id=Content.id AND Order_Content.id="'.$id.'"';
		$res = $this->sendAndGetOne($query);
		$content_id = $res->id;
		$order_id = $res->order_id;
		$query = 'DELETE FROM Content WHERE id="'.$content_id.'"';
		$this->sendAndDelete($query);
		$query = 'DELETE FROM Order_Content WHERE id="'.$id.'"';
		$this->sendAndDelete($query);
		$subtract_from_query = 'UPDATE Order_Data SET total_content = total_content-1 WHERE order_id="'.$order_id.'"';
		$this->sendAndGetOne($subtract_from_query);
		return(true);
	}
	
	protected function makeRecipe($item) {
        // extract the sections and convert to JSON
        $json = '';
        $sections = null;
        
        foreach($item AS $key=>$val) {
            $split = explode('-',$key);
            
            if($split[0] == 'section') {
                $subsplit = explode('_',$split[1]);
                $place = $subsplit[1];
                $type = $val;
                
                $section_content = $this->makeSection($item,$type,$place);
                
                if($section_content != '') {
                    $section_open = '{"section": {';
                    $section_open .= '"type":"'.$type.'",';
                    $section_close = '}},';
                    $section = $section_open.$section_content.$section_close;
                    $sections .= $section;
                }
                
                unset($item[$key]);
            }
        }
        if($sections) {
            $json = '{"recipe":[';
            $sections = substr($sections, 0, -1);
            $json .= $sections;
            $json .= ']}';
        }
        return($json);
    
    }
    
    protected function makeSection($data,$type,$place) {
        $section = null;
        if($type == 'ingredient') {
            $title = 0;
            $ingredients = null;
            foreach($data AS $k=>$v) {
                $test = explode('-',$k);
                $subtest = '';
                if(isset($test[1])) {
	                $subtest = explode('_',$test[1]);
					if($subtest[0] == 'title' && $subtest[1] == $place) {
						$title = urlencode($v);
					}
	            }
                if($test[0] == $type && $subtest[0] == $place) {
                    if($v) {
                        $ingredients .= '"'.urlencode($v).'",';
                    }
                }
            }
            if($ingredients) {
                $section .= '"title":"'.$title.'",';
                $ingredients = substr($ingredients, 0, -1);
                $section .= '"content":['.$ingredients.']';
            }
        } else {
        	$content = null;
            foreach($data AS $k=>$v) {
                $test = explode('-',$k);
                $subtest = explode('_',$test[0]);
                if($subtest[0] == $type && $subtest[1] == $place) {
                	if(trim($v)) {
                		if($type == 'note') {
                			$v = substr(urlencode($v),0,350);
                		} else {
                			$v = urlencode($v);
                		}
	                    $content = ' "content":"'.$v.'"';
	                }
                }
            }
            if($content) {
            	$section = $content;
            }
        }
        return($section);
    }
		
	protected function textSection($type,$title,$value='') {
		if($value) {
			$content = urlencode(stripslashes(html_entity_decode($value)));
		} else {
			$content = '';
		}
		$json = '"title":"'.$title.'", "content": "'.$content.'"}},';
		return($json);
	}
	
	protected function makeMeta($item) {
		 $meta = '';
		 $icon_meta = null;
		 if(isset($_POST['recipe_icon'])){
			 $icon_meta = '"recipe_icon":"'.$_POST['recipe_icon'].'"';
		 }
		 $contributors = array();
		 foreach($_POST AS $key=>$val) {
			 if(substr($key, 0,11) == 'contributor') {
				 if($val) {
				 	$val = urlencode(html_entity_decode($val));
					$val_split = explode('-', $key);
					$contributors[$val_split[1]][$val_split[2]] = $val;
				 }
			 }
		 }
		 $contributor_meta = null;
		 if(count($contributors) > 0) {
			 $contributor_meta .= '"contributors":[';
			 foreach($contributors AS $c) {
				 $contributor_meta .= '{"contributor": { ';
				 if(isset($c['first_name'])) {
					 $contributor_meta .= '"first_name" : "'.trim($c['first_name']).'",';
				 }
				 if(isset($c['last_name'])) {
					 $contributor_meta .= '"last_name": "'.trim($c['last_name']).'",';
				 }
				 if(isset($c['credits_1'])) {
					 $contributor_meta .= '"credits_1": "'.trim($c['credits_1']).'",';
				 }
				 if(isset($c['credits_2'])) {
					 $contributor_meta .= '"credits_2": "'.trim($c['credits_2']).'",';
				 }
				 $contributor_meta = substr($contributor_meta, 0, -1);
				 $contributor_meta .= '}},';
			 }
			 $contributor_meta = substr($contributor_meta,0,-1);
			 $contributor_meta .= "]";
		 }
		 
		 if(strlen($icon_meta) > 0 || strlen($contributor_meta) > 0) {
			 $meta = "{";
			 if(isset($icon_meta)) {
				 $meta .= $icon_meta;
			 }
			 if(isset($icon_meta) && isset($contributor_meta)) {
				 $meta .= ",";
			 }
			 if($contributor_meta) {
				 $meta .= $contributor_meta;
			 }
			 $meta .= "}";
		 }
		 return($meta);
	}

	protected function scrub($text) {
		$text = str_replace("\n", ' ', $text);
		$text = str_replace("\r", ' ', $text);
		str_replace(array("&lrm;", "&rlm;", "&LRM;", "&RLM;"), " ", $text);
		$text = trim($text);
		$text = urlencode($text);
		return($text);
	}
	
}

if(isset($_POST['action'])) {
	$action= $_POST['action'];
	$nc = new Cookbook();
	switch($action) {
		case 'recipe_add':
			$res = $nc->addNewRecipe($_POST,$_GET['result']);
			echo $res;
			break;
		case 'recipe_edit':
			$res = $nc->updateRecipe($_POST,$_GET['result']);
			echo $res;
			break;
		case 'recipe_delete':
			$res = $nc->deleteRecipe($_POST['id']);
			echo $res;
			break;
		case 'preview_win':
			$res = $nc->updateRecipe($_POST,$_GET['result']);
			echo $res;
			break;
		case 'recipe_delete':
			$res = $nc->deleteRecipe($_POST);
			echo $res;
			break;
	}
}

?>