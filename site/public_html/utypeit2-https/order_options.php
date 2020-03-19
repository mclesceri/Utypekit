<?php

session_start();

if(!$_SESSION['login'] == true) {
    header('Location: index.php');
}

if(isset($_SESSION['order_id'])) {
	unset($_SESSION['order_id']);
}
if(isset($_SESSION['order_title'])) {
	unset($_SESSION['order_title']);
}
if(isset($_SESSION['order_number'])) {
	unset($_SESSION['order_number']);
}
if(isset($_SESSION['general_info'])) {
	unset($_SESSION['general_info']);
}
if(isset($_SESSION['utypeit_info'])) {
	unset($_SESSION['utypeit_info']);
}
if(isset($_SESSION['general_info'])) {
	unset($_SESSION['general_info']);
}
if(isset($_SESSION['categories'])) {
	unset($_SESSION['categories']);
}
if(isset($_SESSION['subcategories'])) {
	unset($_SESSION['subcategories']);
}
if(isset($_SESSION['warning'])) {
	unset($_SESSION['warning']);
}
if(isset($_SESSION['list_limit'])) {
	unset($_SESSION['list_limit']);
}

require_once('src/globals.php');
require_once(SERVICES.'Orders.php');
$no = new Orders();
require_once(INCLUDES."Warnings.php");

$title = 'Order Options';
$page = 'order_options';

if(isset($_REQUEST['id'])) {
    $order_id = $_REQUEST['id'];
    $_SESSION['order_id'] = $order_id;
}

if(!$order_id) {
	$out = "You must select an order to begin.";
} else {
	$demo = false;
	if($order_id == 1) {
		$demo = true;
	}
	
    $this_order = $no->getComposedOrder($order_id);
    //print_r($this_order);
    $order_title = stripslashes(urldecode($this_order->order->title));
    $_SESSION['order_title'] = $order_title;
    $order_number = $this_order->order->order_number;
    $_SESSION['order_number'] = $order_number;
    
    // Get the current user's level for the currently selected order...
    $query = "SELECT level FROM Order_People WHERE order_id='".$order_id."' AND person_id='".$_SESSION['user']->id."'";
    $res = $no->sendAndGetOne($query);
    $_SESSION['user']->order_level = $res->level;
    
    // Meta Information
    $general_info = null;
    $categories = null;
    $subcategories = null;
    
    foreach($this_order AS $key=>$val) {
        if($key == 'categories' || $key == 'subcategories') {
            $_SESSION[$key] = json_decode($val);
        } else {
        	if($key == 'general_info') {
				$meta = new stdClass();
				$first = explode('|',$val);
				foreach($first AS $f) {
            	    $second = explode(':',$f);
					$meta->{$second[0]} = $second[1];
				}
				$_SESSION[$key] = $meta;
			}
			if($key == 'utypeit_info') {
				$meta = new stdClass();
				$first = explode('|',$val);
				foreach($first AS $f) {
            	    $second = explode(':',$f);
					$meta->{$second[0]} = $second[1];
				}
				$_SESSION[$key] = $meta;
			}
			if($key == 'billing_info') {
				$meta = new stdClass();
				$first = explode('|',$val);
				foreach($first AS $f) {
            	    $second = explode(':',$f);
					$meta->{$second[0]} = $second[1];
				}
				$_SESSION[$key] = $meta;
			}
        }
    }
	
$script = "
    document.observe('dom:loaded',function(){
        var sideLinks = $('left_nav').select('li');
        var bigLinks = $('big_icons').select('div').each(function(ea){
            ea.observe('mouseover',function(){
                sideLinks[bigLinks.indexOf(ea)].setStyle({
                    color: '#ffffff',
                    textShadow: '#333333 -1px 1px 2px'
                });
            });
            ea.observe('mouseout',function(){
                sideLinks[bigLinks.indexOf(ea)].removeAttribute('style');
             })
        });
    });
";

$out = '
    <div id="big_icons">
        <div class="pageNote" id="list" onclick="goTo(\'order_list.php\')" data-ot="Click here to go back to the main list of orders." data-ot-delay="1">Select Another Order</div>';
        if($_SESSION['user']->order_level > 3) {
        	$out .= '
        <div class="bigIcon" onclick="goTo(\'order_edit.php?id='.$_SESSION['order_id'].'&action=order_edit\')" data-ot="Click here to edit this order\'s settings. Please note that some settings are required before publishing your cookbook. If you haven\'t reviewed your order settings, we recommend that you do so as early as possible." data-ot-delay="1"><img src="'.IMAGES.'bi_order_on.png"></div>';
        } else {
	        $out .= '
	    <div class="bigIcon" data-ot="Chairperson, or Cocharperson Only" data-ot-delay="1"><img src="'.IMAGES.'bi_order_off.png"></div>';
        }
        $out .= '
        <div class="bigIcon" onclick="goTo(\'recipe_list.php\')" data-ot="Click here to go to the list of recipes entered for this cookbook." data-ot-delay="1"><img src="'.IMAGES.'bi_recipes_on.png"></div>';
        if($_SESSION['user']->order_level > 3) {
        	$out .= '
        <div class="bigIcon" onclick="goTo(\'people_list.php\')" data-ot="Click here to edit the user settings for this order, including the settings for  messages to your users." data-ot-delay="1"><img src="'.IMAGES.'bi_member_on.png"></div>';
        } else {
	        $out .= '
	    <div class="bigIcon" data-ot="Chairperson, or Cocharperson Only" data-ot-delay="1"><img src="'.IMAGES.'bi_member_off.png"></div>';
        }
        $out .= '
        <div class="bigIcon" onclick="goTo(\'message_center.php\')" data-ot="Click here to send messages to other contributors to this cookbook." data-ot-delay="1"><img src="'.IMAGES.'bi_message_on.png"></div>';
        if($demo) {
	    $out .= '<div class="bigIcon" onclick="goTo(\'send_proof.php\')"><img src="'.IMAGES.'bi_proof_on.png"></div>';
        } else if($_SESSION['user']->order_level > 3) { 
        $out .= '<div class="bigIcon" onclick="goTo(\'send_proof.php\')" data-ot="Click here to send for an automated PDF proof of your cookbook." data-ot-delay="1"><img src="'.IMAGES.'bi_proof_on.png"></div>';
        } else {
        $out .= '<div class="bigIcon" data-ot="Chairperson, or Cocharperson Only" data-ot-delay="1"><img src="'.IMAGES.'bi_proof_off.png"></div>';    
        }
        if($_SESSION['user']->order_level > 4) {
        $out .= '<div class="bigIcon" onclick="goTo(\'order_form.php\')" data-ot="Click here to submit your cookbook for printing." data-ot-delay="1"><img src="'.IMAGES.'bi_submit_on.png"></div>';
        } else {
        $out .= '<div class="bigIcon" data-ot="Chairperson Only" data-ot-delay="1"><img src="'.IMAGES.'bi_submit_off.png"></div>';         
        }
        $out .= '<p style="text-align: center">You are now viewing the options for order number '.$_SESSION['order_number'].'</p>
    </div>';
}


$header_left = '';
$header_right = '';
$header_middle = $_SESSION['order_title'];

$head = '';
require_once(TEMPLATES.'u_orders_header.tpl');
require_once(TEMPLATES.'orders_footer.tpl');

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

$content = $out;

include(TEMPLATES.'main.tpl');

?>