<?

session_start();

if($_SESSION['login'] != true) {
	header("Location: index.php");
}

$page = 'recipe_organize';

require_once('src/globals.php');

if(!isset($_SESSION['order_id'])) {
	$out = 'You must first select an order. Please go to the <a href="order_list.php">Order List</a> page and choose which order you want to work with.';
} else {
	include_once(SERVICES.'BaseService.php');
	require_once(SERVICES.'Cookbook.php');
	
	require_once(INCLUDES."Warnings.php");
	
	if(isset($_POST['action'])) {
	    $nb = new BaseService();
		if($_POST['action'] == 'save') {
	        foreach($_POST AS $key=>$val) {
	            $id = '';
	            $place = '';
	            $order = '';
	            
	            $split = explode('_',$key);
	            if($split[0] == 'id') {
	                $id = $val;
	                $place = $split[1];
	                foreach($_POST AS $k=>$v) {
	                    $subsplit = explode('_',$k);
	                    if($subsplit[0] == 'order' && $subsplit[1] == $place) {
	                        $order = $v;
	                    }
	                }
	                $query = "UPDATE Order_Content SET list_order='".$order."' WHERE id='".$id."'";
	                $nb->sendAndGetOne($query);
	            }
	            
	        }
	        
		}
	}
	
	$order_id = $_SESSION['order_id'];
	
	$demo = false;
	if($order_id == 1) {
	    $demo = true;
	}
	
	$panel = 0;
	$title = 'ORGANIZE RECIPES FOR ORDER #'.$_SESSION['order_number'];
	
	$order_id = $_SESSION['order_id'];
	
	$nc = new Cookbook();
	
	$categories = $_SESSION['categories'];
	
	
	
	if(isset($_POST['category'])) {
		$category = $_POST['category'];
	} else {
		$category = 1;
	}
	
	if($_SESSION['general_info']->use_subcategories == 'yes') {
		$use_subcategories = true;
		$subcategories = $_SESSION['subcategories']->subcategories;
		$subcategory = null;
		if(isset($_POST['subcategory'])) {
			$subcategory = $_POST['subcategory'];
		}
	}
	
	$nc = new Cookbook();
	$recipes =  $nc->getRecipesQualified($order_id,$category,$subcategory);
	
	$total = count($recipes);
	
	$columns = array('ID','Title','Order','Status');
	$recipes_list = array();
	for($o=0;$o<count($recipes);$o++) {
		$recipes_list[$o]['ID'] = $recipes[$o]->id;
		$recipes_list[$o]['Title'] = $recipes[$o]->title;
		$recipes_list[$o]['Order'] = $recipes[$o]->list_order;
		$recipes_list[$o]['Status'] = $recipes[$o]->status;
	}
	// Draw the header
	$script = "
	    document.observe('dom:loaded', function() {
	            
	        fancyNav();
	        
	        window.setDrag();
	       
	    $( 'category' ).observe('change', function(event){
	        var select = $( 'category' );
	        var cat = select.options[select.selectedIndex].value;
	        var subcat = '';
	        drawList(cat,subcat);
	    });";
	    
	    if($use_subcategories) {
	        $script .= "
	        $('subcategory' ).observe('change', function(event){
	            var category = $( 'category' )[$('category').selectedIndex].value;
	            var subcategory = $('subcategory')[$('subcategory').selectedIndex].value;
	            drawList(category,subcategory);
	        });";
	    }
	$script .= "})";
	
	$out = '<form action="'.$_SERVER['PHP_SELF'].'" method="POST" id="recipe_organize"  name="recipe_organize">
		<input type="hidden" name="action" value="save">';
		$out .= '
		<div class="listTitleBar">';
				$out .= '
				<div class="listTitleBarItem" id="list_item">';
				$out .= '<label>Category <select name="category" id="category">';
				foreach($_SESSION['categories']->categories AS $c) {
				    $number = $c->number;
				    $name = $c->name;
					if($number == $category) {
						$selected = 'selected="selected"';
					} else {
						$selected = '';
					}
					$out .= '<option value="'.$number.'"'.$selected.'>'.stripslashes(urldecode($name)).'</option>';
				}
				$out .= '</select>
				</label>
				</div>';
		
			if($use_subcategories) {
				$out .= '
				<div class="listTitleBarItem" id="list_item">';
				$out .= '&nbsp;<label>Subcategory <select name="subcategory" id="subcategory">
					<option value="0"> -- </option>';
				foreach($subcategories AS $o) {
					if($o->parent == $category) {
					    $val = $o->number;
	                    $name = $o->name;
					    if($val == $subcategory) {
					        $selected = 'selected="selected"';
	                    } else {
	                        $selected = '';
	                    }
	                    $out .= '<option value="'.$val.'"'.$selected.'>'.stripslashes(urldecode($name)).'</option>';
	                }
				}
				$out .= '</select>
				</label>
				</div>';
			}
		$out .= '
		</div>';
		
		$out .= "
		<div class=\"listHeader\">";
	    $out .= "<ul>";
		foreach($columns AS $c) {
			$orderby = strtolower(str_replace(' ','_',$c));
			$out .= '<li id="'.$c.'_header">'.$c.'</li>';
		}
		$out .= "<li id='Staus_header'>&nbsp;</li>";
	    $out .= "</ul>";
	    $out .= "</div>";
	    
	    $out .= '<div class="listContainer" id="listContainer">';
		$total  = count($recipes_list);
		for($p=0;$p<$total;$p++) {
			$new_order = $p+1;
			$out .= '<div id="row_'.($p+1).'" class="itemRow">';
			foreach($recipes_list[$p] as $e=>$v) {
				$width = 100;
				if($e == 'Status') {
					if($v == -1) {
						$v = 'Unselected';
						$bgcol = 'FF9E9E';
					} elseif($v == 0) {
						$v = 'Inactive';
						$bgcol = 'FF9E9E';
					} elseif($v == 1) {
						$v = 'Data Entry';
						$bgcol = 'FFD59E';
					} elseif($v == 2) {
						$v = 'Editorial';
						$bgcol = 'C6FF9E';
					} elseif($v == 3) {
						$v = 'Approved';
						$bgcol = '8CFF84';
					}
					$out .= '<div class="'.$e.'" style="background-color: #'.$bgcol.'">'.$v.'</div>';
				} elseif($e == 'Order') {
					$order = $p+1;
					$v = '<input type="hidden" name="id_'.$order.'" id="id" value="'.$recipes_list[$p]['ID'].'"><input type="text" name="order_'.$order.'" id="order" value="'.$order.'" class="null" size="2"/>';
					$out .= '<div class="'.$e.'">'.$v.'</div>';
				} else {
					if($e == 'Title') {
						$width = 500;
					}
					$v = urldecode($v);
					$v = str_replace('&#039;','\'',$v);
					$v = htmlspecialchars($v);
					$out .= '<div class="'.$e.'" style="width: '.$width.'px">'.$v.'</div>';
				}
			}
	        $out .= "<img src=\"".IMAGES."move_button.png\" id=\"handle\">";
			$out .= '</div>';
		}
		$out .= '</div>
		</form>';
	
	$header_left = '';
	$name = $_SESSION['user']->first_name.' '.$_SESSION['user']->last_name;
	if(substr($name, -1) == 's') {
	    $name .= "'";
	} else {
	    $name .= "'s";
	}
	$header_middle = $name." ".$_SESSION['order_title']." Recipes";
	$header_right = "";
	
	require_once (TEMPLATES . 'u_recipes_header.tpl');
	require_once (TEMPLATES . 'organize_footer.tpl');
	
	/*
	 * 
	 *  Set up the warnings to be displayed for:
	 *  recipe count, individual recipe count, entry deadline
	 * 
	 */
	$warning = '';
	$warn = '';
	
	if(isset($_SESSION['utypeit_info'])) {
		$x = new Warnings($_SESSION['utypeit_info']);
		$warn = $x->_warnings($_SESSION['order_id'],$_SESSION['user']->id);
		$warning = $warn->display;
	}
}
$content = $out;
include(TEMPLATES.'main.tpl');

?>