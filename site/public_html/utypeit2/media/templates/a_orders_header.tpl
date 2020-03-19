<?php
require_once(INCLUDES.'ListLimit.php');
require_once(INCLUDES.'BreadCrumb.php');

switch($page) {
    case 'order_list':
        $head = '<div class="contentHeaderBlock" id="contentHeaderBlock">
        <div class="contentHeaderLeft">ORDERS LIST</div>
        <div class="contentHeaderRight">';
        $bc = new BreadCrumb($page);
        $ll = new ListLimit();
        $head .= $ll->_draw();
        $head .= $bc-> _paginate($pagenum,getCount($action,$the_id),$limit,'subheaderLink',$orderby);
        $head .= '</div>
        </div>';
        break;
    case 'order_edit':
        $head = '
    	<div class="contentHeaderBlock" id="contentHeaderBlock">
        	<div class="contentHeaderLeft">'.$title.'</div>
		</div>';
        break;
}
$head .= '<div id="feedback"></div>';
$contentHeader = $head;
?>