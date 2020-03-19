<?php
require_once(INCLUDES.'ListLimit.php');
require_once(INCLUDES.'BreadCrumb.php');

$head = '
<div class="contentHeaderBlock" id="contentHeaderBlock">
    <div class="contentHeaderLeft">'.ucwords(str_replace('_',' ',$page)).' for Order #'.$_SESSION['order_number'].'</div>
    <div class="contentHeaderMiddle">Total Recipes This Order: '.$recipe_count.'</div>
    <div class="contentHeaderRight">';
    if($page == 'recipe_list') {
        $bc = new BreadCrumb($page);
        $ll = new ListLimit();
        $head .= $ll->_draw();
        $head .= $bc-> _paginate($pagenum,getCount($order_id,$the_id),$limit,'subheaderLink',$orderby);
    }
    
    $head .= '</div>
    <div class="contentHeaderTabs">
        <ul>';
            $head .= '<li';
           if(!$demo) {
               $head .= ' onclick="printWin(\'recipe_list\',\'\')"';
           } else {
                $head .= ' class="disabled"';       
           }
           $head .= '>Print Recipe List</li>
           <li';
            if($_SESSION['user']->order_level > 3) {
            	if($_SESSION['general_info']->order_recipes_by == 'custom') {
					if($page != 'recipe_organize') {
						$head .= ' onclick="window.location = \'recipe_organize.php\'"';
					} else {
						$head .= ' class="disabled"';
					}
				} else {
					$head .= ' class="disabled"';
				}
            } else {
                $head .= ' class="disabled"';
            }
           $head .= '>Organize Recipes</li>
           <li class="blank">&nbsp;</li>
           <li';
           if(!$demo && $page == 'recipe_edit') {
               $head .= ' onclick="_saveRecipe(this,\''.$recipe_id.'\'); return false;"';
           } else {
               $head .= ' class="disabled"';
           }
           $head .= '>Save Recipe</li>
           <li';
		   if($page == 'recipe_edit') {
			   $head .= ' onclick="sendPreview();"';
			} else {
                $head .= ' class="disabled"';
           }
           $head .= '>Preview Recipe</li>';
           $head .= '<li';
           if($_SESSION['user']->order_level > 3 && $page == 'recipe_edit') {
               if(!$demo) {
                   $head .= ' onclick="sendDelete(\''.$recipe_id.'\');"';
               } else {
                   $head .= ' class="disabled"';
               }
           } else {
                $head .= ' class="disabled"';
           }
           $head .= '>Delete Recipe</li>';
           $head .= '<li class="blank">&nbsp;</li>
           <li';
           if($action != 'recipe_add') {
               $head .= ' onclick="window.location = \'recipe_edit.php?action=recipe_add\'"';
           } else {
               $head .= ' class="disabled"';
           }
           $head .='>Add a Recipe</li>
        </ul>
    </div>
</div>
<div id="feedback">'.$feedback.'</div>';

$contentHeader = $head;
?>