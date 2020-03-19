<?php
define('FORCE_HTTPS', true);

session_start();

if ( !defined('SRC') ) require_once('../globals.php');
require_once(SERVICES.'Orders.php');

class Wizard extends Orders
{
		
	var $data;
	var $user_id;
	var $order_id;
	var $order_number;

	function signUp($data) {
		$res = $this->addNewOrder($data);
		$json = json_decode($res);
		if($json->status == 'true') {
			$query = 'SELECT People.*,Order_People.level AS order_level,People.status AS user_status FROM People,Order_People WHERE Order_People.order_id="'.$json->id.'" AND People.id=Order_People.person_id AND People.id="'.$json->user.'"';
			$chairperson = $this->sendAndGetOne($query);
			if($chairperson) {
				$_SESSION['login'] = true;
				$_SESSION['user'] = $chairperson;
			}
		}
		return($res);
	}
    
}

if(isset($_POST['action'])) {
	if($_POST['action'] == 'signup') {
		$wiz = new Wizard();
		$res = $wiz->signUp($_POST);
        echo $res;
	}
}

?>