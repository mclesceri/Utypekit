<?php
require_once(INCLUDES.'ListLimit.php');
require_once(INCLUDES.'BreadCrumb.php');

switch($page) {
    case 'recipe_list':
        $head = '<div class="contentHeaderBlock" id="contentHeaderBlock">
        <div class="contentHeaderLeft">ORDER # '.$_SESSION['order_number'].': Recipes List </div>
        <div class="contentHeaderRight">';
        $bc = new BreadCrumb($page);
        $ll = new ListLimit();
        $head .= $ll->_draw();
        $head .= $bc-> _paginate($pagenum,getCount($order_id),$limit,'subheaderLink',$orderby);
        $head .= '
        	</div>
        </div>';
        $head .= '<div id="feedback"></div>';
        break;
    case 'recipe_edit':
        $head = '
        <div class="contentHeaderBlock" id="contentHeaderBlock">
        	<div class="contentHeaderLeft">ORDER # '.$_SESSION['order_number'].':';
        	if($action == 'recipe_edit'){
        		$head .= ' Edit Recipe ID #'.$recipe_id;
        	} else {
        		$head .= ' Add Recipe';
        	}
        	$head .= '</div>
		</div>';
		$head .= "
		<table id=\"head_buttons\">\n
			<tr>\n";
			if ($_SESSION['user'] -> level > 7) {
				if($action != 'recipe_add') {
				$head .= "
				<td><button name=\"delete_top\" id=\"delete_top\" onclick=\"sendDelete('".$recipe_id."');\">Delete</button></td>";
				}
			}
			$head .= "
					<td><button name=\"preview_top\" id=\"preview_top\" onclick=\"sendPreview()\">Preview</button></td>
					<td><button name=\"save_andadd_top\" id=\"save_andadd_top\" onclick=\"_saveAndAdd(this,'".$recipe_id."'); return false;\">Save and Add Another</button></td>
					<td><button name=\"save_top\" id=\"save_top\" onclick=\"_saveRecipe(this,'".$recipe_id."'); return false;\">Save</button></td>
				";
			$head .= "
			</tr>\n
		</table>\n";
		$head .= '<div id="feedback"></div>';
        break;
    case 'recipe_organize':
        $head = '
        <div class="contentHeaderBlock" id="contentHeaderBlock">
        	<div class="contentHeaderLeft">ORDER # '.$_SESSION['order_number'].': Organize Recipes</div>
		</div>';
		$head .= "
		<table id=\"head_buttons\" width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">\n
			<tr>\n";
		$head .= "
				<td><button name=\"refresh_top\" id=\"refresh_top\" onclick=\"window.location = 'recipe_organize.php?id=".$order_id."\" >Refresh</button></td>\n
				<td><button name=\"save_top\" id=\"save_top\" onclick=\"saveOrganize();\" >Save</button></td>
			</tr>\n
		</table>\n";
		$head .= '<div id="feedback"></div>';
        break;
}

$contentHeader = $head;
?>