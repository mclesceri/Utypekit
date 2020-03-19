<?php
require_once(INCLUDES.'ListLimit.php');
require_once(INCLUDES.'BreadCrumb.php');
$order_number = null;
if(isset($_SESSION['order_number'])) {
	$order_number = $_SESSION['order_number'];
}

switch($page) {
    case 'order_list':
        $head = '<div class="contentHeaderBlock" id="contentHeaderBlock">
        <div class="contentHeaderLeft">ORDERS LIST ';
        $head .= '
        </div>
        <div class="contentHeaderRight">
            <ul>
                <li class="disabled" data-ot="You must select an order first." data-ot-delay="1">Save Order</li>
                <li class="blank">&nbsp;</li>
                <li';
				if(!$demo) {
					if($page == 'order_list') {
						$head .= ' onclick="setContent(\'order_edit\',{mode:\'redirect\',action:\'order_add\'})"';
					} else {
						if($page == 'order_edit' && $action == 'order_add') {
							$head .= ' class="disabled" data-ot="Create a new order." data-ot-delay="1"';
						} else {
							$head .= ' onclick="setContent(\'order_edit\',{mode:\'redirect\',action:\'order_add\'})"';
						}
					}
				} else {
					$head .= ' class="disabled" data-ot="Create a new order." data-ot-delay="1"';
				}
				$head.= '>Add New Order</li>
            </ul>
        </div>
        </div>';
        break;
    case 'order_edit':
        $head = '<div class="contentHeaderBlock" id="contentHeaderBlock">
        <div class="contentHeaderLeft">'.stripslashes(urldecode($title));
        if($action != 'order_add') {
            $head .= ' Use the blue tabs below to review / change this order\'s settings.';
        } else {
            $head .= ' Use the blue tabs below to enter the new order\'s settings.';
        }
        $head .= '</div>
        <div class="contentHeaderRight">
            <ul>
	                <li';
					if(!$demo) {
						if($action == 'order_add' ) {
							$head .= ' onclick="sendOrder(\''.$action.'\'); return false;"';
						} else {
							if($_SESSION['user']->order_level >= 4) {
								$head .= ' onclick="sendOrder(\''.$action.'\'); return false;"';
							} else {
								$head .= ' class="disabled"';
							}	
						}
	                } else {
	                        $head .= ' class="disabled"';
                    }
	                 $head .= '>Save Order</li>
	                <li class="blank">&nbsp;</li>
	                <li';
                    if(!$demo) {
                        if($action != 'order_add') {
                            $head .= ' onclick="setContent(\'order_edit\',{mode:\'redirect\',action:\'order_add\'})"';
                        } else {
                            $head .= ' class="disabled"';
                        }
                    } else {
                        $head .= ' class="disabled"';
                    }
                    $head .= ' data-ot="Create a new order." data-ot-delay="1">Add New Order</li>
            </ul>
        </div>
    </div>';
        break;
}

$contentHeader = $head;
?>