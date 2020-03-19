<?
session_start();

if (!$_SESSION['login'] == true) {
    header('Location: index.php');
}

require_once('src/globals.php');

$page = 'recipe_edit';

if(!isset($_SESSION['order_id'])) {
	$out = 'You must first select an order. Please go to the <a href="order_list.php">Order List</a> page and choose which order you want to work with.';
} else {
	
	$first = 'true';
	if (isset($_SESSION['recipe_edit_first'])) {
	    $first = 'false';
	}
	
	require_once (INCLUDES . 'CommonAbbreviations.php');
	require_once (INCLUDES . 'SpecialCharacters.php');
	require_once (INCLUDES . 'RecipeIcons.php');
	require_once (INCLUDES . 'Recipe.php');
	require_once (INCLUDES . 'Warnings.php');
	
	require_once (SERVICES . 'BaseService.php');
	
	if (isset($_REQUEST['action'])) {
	    $action = $_REQUEST['action'];
	}
	$recipe_id = null;
	if (isset($_GET['id'])) {
	    $recipe_id = $_GET['id'];
	}
	
	$order_id = $_SESSION['order_id'];
	
	$demo = false;
	if($order_id == 1) {
	    $demo = true;
	}
	
	switch($action) {
	    case 'recipe_add' :
	        $title = "ADD RECIPE TO ORDER #" . $_SESSION['order_number'];
	        $form_action = "recipe_add";
	        $response_action = "recipe_edit";
	        $status = '1';
	        $contributor_count = 0;
	        $section_count = 1;
	        $sections[0]['type'] = '';
	        $sections[0]['title'] = '';
	        break;
	    case 'recipe_edit' :
	        $title = 'EDIT RECIPE #' . $recipe_id;
	        $form_action = "recipe_edit";
	        $response_action = "recipe_edit";
	        break;
	}
	
	$script = "var first = " . $first . ";";
	$_SESSION['recipe_edit_first'] = false;
	$script .= "
	var _recipesections = '';
	var _contributors = '';
	var _ins_object = '';
	var max_contributors = 2;
	document.observe('dom:loaded', function(){
		if($('contributor_list').select('div').length > 1) {
			var max_contributors = $('contributor_list').select('div').length;
		}
		
		fancyNav();
	    _setSideBoxes();
	    _setDragDrop();
	    if($('subcategory')) {
			$('category').observe('change', function(){
	        	var select = $( 'category' );
	            var val = select.selectedIndex >=0 && select.selectedIndex ? escape(select.options[select.selectedIndex].value): undefined;
	            _setSubCatList(val);
			});
		}
		
	});";
	//if (strpos($_SERVER['HTTP_USER_AGENT'], 'Firefox')) {
	//    $script .= "
	//    Hotkeys.bind('ctrl+shift+T',_addSection);";
	//} else {
	    $script .= "
	    Hotkeys.bind('ctrl+shift+t',_addSection);";
	//}
	$script .= "
	    Hotkeys.bind('ctrl+shift+i',_addIngredient);
	    Hotkeys.bind('ctrl+shift+s',_saveRecipe);
	    Hotkeys.bind('ctrl+alt+shift+s',_saveAndAdd);";
	
	$out = '<div id="boxes">';
	$spec = new CommonAbbreviations();
	$out .= $spec->_draw();
	
	$spec = new SpecialCharacters();
	$out .= $spec->_draw();
	
	if($_SESSION['general_info']->use_icons == 'yes') {
	    $icns = new RecipeIcons();
	    $out .= $icns->_draw();
	}
	$out .= '</div>';
	
	// ! Test to make sure that this isn't just a random recipe id...
	if(isset($recipe_id)) {
	    $nb = new BaseService();
	    $query = 'SELECT order_id FROM Order_Content WHERE id="'.$recipe_id.'"';
	    $res = $nb->sendAndGetOne($query);
	    if($res->order_id != $_SESSION['order_id']) {
	    	$feedback = "Wrong Order";
	    } else {
	    	$feedback = "";
	    	$nr = new Recipe($action,$recipe_id);
	    	$out .= stripslashes($nr->_draw($action));
	    }
	} else {
	    $feedback = "";
	    $nr = new Recipe($action,$recipe_id);
	    $out .= stripslashes($nr->_draw($action));
	}
	
	$header_left = '&nbsp;';
	if($action == 'recipe_add') {
	    $header_middle = $_SESSION['order_title']." : Add New Recipe";
	} else {
	    $header_middle = $_SESSION['order_title']." : Recipe ID ".$recipe_id;
	}
	$header_right = "";
	
	require_once (TEMPLATES . 'u_recipes_header.tpl');
	require_once (TEMPLATES . 'recipes_footer.tpl');
	
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
include (TEMPLATES . 'main.tpl');
?>