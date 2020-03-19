<?php

switch($page) {
    case 'people_list':
        $head = '<div class="contentHeaderBlock" id="contentHeaderBlock">
        <div class="contentHeaderLeft">PEOPLE LIST ';
        $bc = new BreadCrumb($page);
        $ll = new ListLimit();
        $head .= $ll->_draw();
        $head .= $bc-> _paginate($pagenum,$total_people,$limit,'subheaderLink',$orderby,$action);
        $head .= '</div>
        </div>';
        break;
    case 'people_edit':
        $head_title = "EDIT PERSON ID #".$person_id;
         if(substr($action, -3) == 'add') {
             $head_title = "ADD PERSON";
         }
        $head = '<div class="contentHeaderBlock" id="contentHeaderBlock">
        <div class="contentHeaderLeft">'.$head_title.'</div>';
        if($person->level > 1) {
	        if($person->level == 6) {
				$head .= '<div class="contentHeaderRight"><button onclick="setContent(\'order_list\',{mode: \'redirect\',action: \'contractor\', id: \''.$person_id.'\'}); return false;">List Contractor\'s Orders</button></div>';
	        } else {
				$head .= '<div class="contentHeaderRight">&nbsp;</div>';
	        }
        } else {
	      $head .= '<div class="contentHeaderRight"><button onclick="setContent(\'order_list\',{mode: \'redirect\',action: \'customer\', id: \''.$person_id.'\'}); return false;">List Customer\'s Orders</button></div>';
        }
		$head .= '</div>';
        break;
}

$contentHeader = $head;
?>