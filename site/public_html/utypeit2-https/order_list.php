<?php

session_start();

if(!$_SESSION['login'] == true) {
	header('Location: index.php');
}

require_once('src/globals.php');

$title = 'Order List';
$page = 'order_list';
$start = 0;
$limit = 25;

$header_left = '';
$header_right = '';
$header_middle = '';

require_once(SERVICES.'Orders.php');
$no = new Orders();

function getCount($type,$id='') {
	$newOrder = new Orders();
	$res = $newOrder->getOrderCount($type,$id);
	return( $res->COUNT );
}

$action = '';
if(isset($_GET['action'])) {
	$action = $_GET['action'];
}

if(isset($_GET['orderby'])) {
	$orderby = $_GET['orderby'];
} else {
	$orderby = 'id';
}

$list_name = 'orders_list';

//id,title,DATE_FORMAT(date_added,"%M %d, %Y"),order_number,status
$columns = array('ID','Title','Date Added','Order Number','Chairperson','Status','');

$the_id = '';
$the_id = $_SESSION['user']->id;
$orders = $no->getCustomerOrderList($the_id);

// find out the total number of orders to be listed...
$order_count = count($orders);

// find out if the user is signed up for a demo account only...
$demo = false;
if($order_count == 1) {
    if($orders[0]->id == 1) {
        $demo = true;
    }
} else {
    // find out if one of the orders is the demo account...
    for($o=0;$o<$order_count;$o++) {
        if($orders[$o]->id == 1) {
            unset($orders[$o]);
        }
    }
}

$old_list = array();
$current_list = array();
$out = '';
// Separate the orders by their status. Old orders go into the old_list
// and current orders go into the current_list...
if($orders) {
    foreach($orders AS $o) {
        $add_date = new DateTime($o->date_added);
        $add_date = date_format($add_date,'M d, Y');
        $o->date_added = $add_date;
        $o->edit = $o->id;
        
        if($o->status > 1) {
            if($o->status == 3) {
                $current_list[] = $o;
            } else {
                 $old_list[] = $o;
            }
        } else {
            $current_list[] = $o;
        }
    }
}

function _list($array) {
    $out = '';
    foreach($array AS $a) {
        $status = $a->status;
        if($status == -1) {
            $status_name = 'Unselected';
            $bgcol = 'FF9E9E';
        } elseif($status == 0) {
            $status_name = 'Inactive';
            $bgcol = 'FC2A2A';
        } elseif($status == 1) {
            $status_name = 'Data Entry';
            $bgcol = 'FFD59E';
        } elseif($status == 2) {
            $status_name = 'Editorial';
            $bgcol = 'F9FF63';
        } elseif($status == 3) {
            $status_name = 'Customer Review';
            $bgcol = '63D5FF';
        } elseif($status == 4) {
            $status_name = 'Approved';
            $bgcol = 'C6FF9E';
        } elseif($status == 5) {
            $status_name = 'Proofing';
            $bgcol = 'EA63FF';
        } elseif($status == 6) {
            $status_name = 'To Print';
            $bgcol = '999999';
        }
        $out .= '<tr>';
        //print_r($array[$p]);
        $out .= '<td class="listItem">'.$a->id.'</td>';
        $out .= '<td class="listItem">'.stripslashes(urldecode($a->title)).'</td>';
        $out .= '<td class="listItem">'.$a->date_added.'</td>';
        $out .= '<td class="listItem">'.$a->order_number.'</td>';
        $out .= '<td class="listItem">'.$a->chairperson.'</td>';
        $out .= '<td class="listItem" style="background-color: #'.$bgcol.'">'.$status_name.'</td>';
		// Find out if this order is available for editing...
        if($a->status <= 1) {
			// Find out if this order is editable by this user...
			if($a->order_level > 1) {
				// Find out if this user has not been turned off by the chair...
				if($a->user_status > 0) {
                	$out .= '
			<td class="listItem"><a href="#" onclick="setContent(\'order_options\',{mode:\'redirect\',id:\''.$a->id.'\'})" class="listEdit">EDIT</a></td>';
				} else {
					$out .= '
			<td class="listItem">&nbsp;</td>';
				}
			} else {
				$out .= '
			<td class="listItem">&nbsp;</td>';
			}
        } else if($a->status == 3) {
	        if($a->order_level > 3) {
	        	if($a->user_status > 0) {
		        $out .= '
			<td class="listItem"><a href="#" onclick="setContent(\'order_options\',{mode:\'redirect\',id:\''.$a->id.'\'})" class="listEdit">EDIT</a></td>';
				} else {
					$out .= '
			<td class="listItem">&nbsp;</td>';
				}
	        } else {
		        $out .= '
			<td class="listItem">&nbsp;</td>';
	        }
        } else {
	        $out .= '
			<td class="listItem">&nbsp;</td>';
        }
        
        $out .= '
		</tr>';
    }
    return($out);
}

if($demo) {
    // give the upgrade offer...
    $out .= "<h3>Hello ".stripslashes(urldecode($_SESSION['user']->first_name))." ".stripslashes(urldecode($_SESSION['user']->last_name))."</h3>
    <h4>To begin, choose one of the Current Orders from the list below and click Edit.</h4>
    <p><strong>NOTE:</strong> You are currently using a demo account to review the functions of UTypeIt Online&trade;. If you would like to upgrade your account and start your own cookbook, just <a href=\"setup_wizard.php?action=upgrade\">CLICK HERE</a>.</p>";
} else {
    $out .= '<h3>Hello '.stripslashes(urldecode($_SESSION['user']->first_name)).' '.stripslashes(urldecode($_SESSION['user']->last_name)).'</h3>
    <h4>To begin, choose one of the Current Orders from the list below and click Edit.</h4>
    <p>To begin, click "Edit" to the right of the item in the "Current Orders" list you want to change, or add to. </p>';
}

if($old_list) {
    $out .= "<table class='listTable'>\n";
    $out .= "
        <tr>
            <th colspan=\"7\">Previous Orders</th>
        </tr>
        <tr>";
    foreach($columns AS $c) {
    	$order = strtolower(str_replace(' ','_',$c));
    	$out .= '<td class="listHeader"><a href="#" class="subheaderLink" onclick="setContent(\'order_list\',{mode:\'redirect\',start:\''.$start.'\',limit:\''.$_SESSION['list_limit'].'\',orderby:\''.$order.'\'})">'.$c.'</a></td>';
    }
    $out .= "</tr>\n";
    $out .= _list($old_list);
    $out .= "</table>\n";
}

if($current_list) {
    $out .= "<table class='listTable'>\n";
    $out .= "
        <tr>
            <th colspan=\"7\">Current Orders</th>
        </tr>
        <tr>";
    foreach($columns AS $c) {
    	$list_limit = '';
    	if(isset($_SESSION['list_limit'])) {
	    	$list_limit = $_SESSION['list_limit'];
    	}
        $order = strtolower(str_replace(' ','_',$c));
        $out .= '<td class="listHeader"><a href="#" class="subheaderLink" onclick="setContent(\'order_list\',{mode:\'redirect\',start:\''.$start.'\',limit:\''.$list_limit.'\',orderby:\''.$order.'\'})">'.$c.'</a></td>';
    }
    $out .= "</tr>\n";
    $out .= _list($current_list);
    $out .= "</table>\n";
}

$script = '';

if(!$demo) {
	$header_middle = '<h3 class="t_ds99-1" style="background: #FFF4D3; display:none; border: 1px #000000 solid; padding: 4px 8px 4px 8px; border-radius: 8px">New! UTypeIt 2.1 - Click  <a href="http://www.youtube.com/embed/iwTt0lmdNMw" class="lightwindow" params="lightwindow_width=420,lightwindow_height=315,lightwindow_loading_animation=false" title="U-Type-It&trade;">HERE</a> for a short walk through of the new features!</h3>';
}

require_once(TEMPLATES.'u_orders_header.tpl');
require_once(TEMPLATES.'orders_footer.tpl');

$content = $out;

include(TEMPLATES.'main.tpl');

?>