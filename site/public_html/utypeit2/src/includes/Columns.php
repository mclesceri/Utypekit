<?php
/*
*
* Columns v 0.1
* by William Logan
*
* Handles all data transactions for the Column List
* 
*/
session_start();

if ( !defined('SRC') ) require_once('../globals.php');
require_once(SERVICES.'BaseService.php');
class Columns extends BaseService
{

	public function _get($name,$parent=0,$child=0) {
		
		$out = '';
		if($name == 'categories') {
			$query = 'SELECT value FROM Order_Meta WHERE order_id="'.$_SESSION['order_id'].'" AND name="categories"';
			$res = $this->sendAndGetOne($query);
			$out = $res->value;
		}
		if($name == 'subcategories') {
			$query = 'SELECT value FROM Order_Meta WHERE order_id="'.$_SESSION['order_id'].'" AND name="subcategories"';
			$subcategories = $this->sendAndGetOne($query);
			// Get Unassigned Recipes
			$query = 'SELECT id,title,list_order,date_added,date_modified,status FROM Order_Content WHERE order_id="'.$_SESSION['order_id'].'" AND category="'.$child.'" AND subcategory="0"';
			$recipes = $this->sendAndGetMany($query);
			
			if($subcategories) {
				$subcategories = $subcategories->value;
			} else {
				$subcategories = '';
			}
			
			if($recipes) {
				$recipes = json_encode($recipes);
			} else {
				$recipes = '';
			}
			
			$out = $subcategories . $recipes;
		}
		if($name == 'recipes') {
			$query = 'SELECT id,title,list_order,date_added,date_modified,status FROM Order_Content WHERE order_id="'.$_SESSION['order_id'].'" AND category="'.$parent.'" AND subcategory="'.$child.'"';
			$recipes = $this->sendAndGetMany($query);
			$recipes = json_encode($recipes);
			$out = $recipes;
		}
		return($out);
	}
	
}

/*$c = new Columns();
$res = $c->_get('subcategory',1,0); // array assumes hierarchy
echo $res;*/

if(isset($_POST['name'])) {
		//print_r($_POST['action']);
		$nc = new Columns();
		$name = $_POST['name'];
		$parent = $_POST['parent'];
		$child = $_POST['child'];
		$res = $nc->_get($name,$parent,$child);
		echo $res;
}

?>