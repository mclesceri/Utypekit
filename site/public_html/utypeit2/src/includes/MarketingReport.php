<?php

/*
*
* Constructs a report of customers who might be eligible to meet the current print deadline
* Criteria are Max Recipes, Last Entry Date and Order Status is Data Entry
*
*/

if ( !defined('SRC') ) require_once('../globals.php');

require_once(SERVICES.'BaseService.php');

class MarketingReport extends BaseService
{

	function _print() {
		$output = array();
		$output[0] = array('id','order_number','title','date_added','last_modified','organization_name','organization_type','chairperson_first_name','chairperson_last_name','email','address1','address2','city','state','zip','phone','order_status');
		echo '
		<div style="text-align: left; font-size: .75em;">';
		echo "'id','order_number','title','date_added','last_modified','organization_name','organization_type','chairperson_first_name','chairperson_last_name','email','address1','address2','city','state','zip','phone','order_status'<br />";
		$query = "SELECT COUNT(id) AS order_count FROM Orders ORDER BY date_added ASC";
		$all_orders = $this->sendAndGetOne($query);
		$total = $all_orders->order_count;
		//echo "id,title,date_added,date_modified,max_recipes,last_modified,chairperson,email,phone";
		//echo "<br />";
		
		$block = 100;
		$pages = ceil($total / $block);
		for($i=0;$i<$pages;$i++) {
			$start = $i*$block;
			$query = "SELECT id FROM Orders ORDER BY id ASC LIMIT ".$start.','.$block;
			$these_orders = $this->sendAndGetMany($query);
			foreach($these_orders AS $o) {
				$output[] = $this->buildOne($o->id);
			}
		}
		echo '</div>';
		$file = 'marketing_report_'.date('m-d-Y_H-i-s').'.csv';
		$fp = fopen(DATA.'reports/'.$file, 'w');
		
		foreach ($output as $fields) {
			fputcsv($fp, $fields);
		}
		
		fclose($fp);
		return($file);
	}
	
	function buildOne($id) {
		$o_query = "SELECT * FROM Orders WHERE id='".$id."'";
		$this_order = $this->sendAndGetOne($o_query);
		
		$order = array();
		$order[] = $id;
		echo $id.',';
		
		$order[] = $this_order->order_number;
		echo $this_order->order_number.',';
		
		$order_title = stripslashes(urldecode($this_order->title));
		$order[] = $order_title;
		echo $order_title.',';
		
		$timestamp = strtotime($this_order->date_added);
		$add_date = date("m/d/Y", $timestamp);
		$order[] = $add_date;
		echo $add_date.',';
		
		$query = "SELECT last_modified FROM Order_Data WHERE order_id='".$id."'";
		$timestamp = strtotime($max_res->last_modified);
		$mod_date =  date("m/d/Y", $timestamp);
		$order[] = $mod_date;
			echo $mod_date.',';
			
		$query = "SELECT O.name,O.type FROM Order_Organizations OO,Organizations O WHERE OO.order_id='".$id."' AND O.id=OO.organization_id";
		$res = $this->sendAndGetOne($query);
		$order[] = $res->name;
			echo $res->name;
		$order[] = $res->type;
			echo $res->type;
		
		$query = "SELECT P.first_name,P.last_name,P.email,P.address1,P.address2,P.city,P.state,P.zip,P.phone FROM People P WHERE id='".$this_order->added_by_id."'";
		//$query = "SELECT CONCAT(People.first_name, ' ',People.last_name) AS person,People.email,People.phone FROM People,Order_People WHERE Order_People.order_id='".$this_order->id."' AND Order_People.level='5' AND Order_People.person_id=People.id";
		$res = $this->sendAndGetOne($query);
		$chair_first_name = stripslashes(urldecode($res->first_name));
		$chair_last_name = stripslashes(urldecode($res->last_name));
		$order[] = $chair_first_name;
		$order[] = $chair_last_name;
		echo $chair_first_name.',';
		echo $chair_last_name.',';
		
		$email = stripslashes(urldecode($res->email));
		$order[] = $email;
		echo $email.',';
		
		if(isset($res->address1)) {
			$order[] = $res->address1;
			echo urldecode($res->address1).',';
		} else {
			$order[] = '0,';
			echo ' ,';
		}
		
		if(isset($res->address2)) {
			$order[] = $res->address2;
			echo urldecode($res->address2).',';
		} else {
			$order[] = '0,';
			echo ' ,';
		}
		
		if(isset($res->city)) {
			$order[] = $res->city;
			echo urldecode($res->city).',';
		} else {
			$order[] = '0,';
			echo ' ,';
		}
		
		if(isset($res->state)) {
			$order[] = $res->state;
			echo $res->state.',';
		} else {
			$order[] = '0,';
			echo ' ,';
		}
		
		if(isset($res->zip)) {
			$order[] = $res->zip;
			echo $res->zip.',';
		} else {
			$order[] = '0,';
			echo ' ,';
		}
		
		if(isset($res->phone)) {
			$phone = $res->phone;
			if($phone != '') {
				$search = array('(',')','-',' ');
				$phone = str_replace($search, '', $phone);
				$area = substr($phone, 0,3);
				$prefix = substr($phone,3,3);
				$number = substr($phone, -4);
				$newphone = $area.'-'.$prefix.'-'.$number;
				$order[] = $newphone;
				echo $newphone.',';
			} else {
				$order[] = '0';
				echo '0,';
			}
		} else {
			$phone = '0,';
			$order[] = $phone;
			echo $phone;
		}
		
		switch($this_order->status) {
			case '1':
				$order[] = 'Data Entry';
				echo 'Data Entry';
				break;
			case '2':
				$order[] = 'Editorial';
				echo 'Editorial';
				break;
			case '3':
				$order[] = 'Customer Review';
				echo 'Customer Review';
				break;
			case '4':
				$order[] = 'Approved';
				echo 'Approved';
				break;
			case '5':
				$order[] = 'Proofing';
				echo 'Proofing';
				break;
			case '6':
				$order[] = 'To Print';
				echo 'To Print';
				break;
			default:
				$order[] = 'Inactive';
				echo 'Inactive';
				break;
		}
		echo '<br />';
		return($order);
	}

}

function loadCSV() {
	$dr = new MarketingReport();
	$res = $dr->_print();
	if($res) {
		$out = '<a href="'.UTI_URL.'src/data/reports/'.$res.'">Download Marketing Report CSV</a>';
	}
	return($out);
}

if(isset($_POST['action'])) {
	if($_POST['action'] = 'deadline_report') {
		echo loadCSV();
	}
}