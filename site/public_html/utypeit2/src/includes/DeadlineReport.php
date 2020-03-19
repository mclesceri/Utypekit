<?php

/*
*
* Constructs a report of customers who might be eligible to meet the current print deadline
* Criteria are Max Recipes, Last Entry Date and Order Status is Data Entry
*
*/

if ( !defined('SRC') ) require_once('../globals.php');

require_once(SERVICES.'BaseService.php');

class DeadlineReport extends BaseService
{

	function _print() {
		$output = array();
		$output[0] = array('id','order_number','title','date_added','date_modified','total_recipes','last_modified','chairperson_first_name','chairperson_last_name','email','phone');
		echo '
		<div style="text-align: left; font-size: .75em;">';
		echo "'id','title','date_added','date_modified','max_recipes','last_modified','chairperson','email','phone'<br />";
		$query = "SELECT COUNT(id) AS order_count FROM Orders WHERE status='1' AND added_by_type='1' ORDER BY date_added ASC";
		$all_orders = $this->sendAndGetOne($query);
		$total = $all_orders->order_count;
		//echo "id,title,date_added,date_modified,max_recipes,last_modified,chairperson,email,phone";
		//echo "<br />";
		
		$block = 100;
		$pages = ceil($total / $block);
		for($i=0;$i<$pages;$i++) {
			$start = $i*$block;
			$query = "SELECT id FROM Orders WHERE status=1 AND added_by_type='1' ORDER BY id ASC LIMIT ".$start.','.$block;
			$these_orders = $this->sendAndGetMany($query);
			foreach($these_orders AS $o) {
				$output[] = $this->buildOne($o->id);
			}
		}
		echo '</div>';
		$file = 'deadline_report_'.date('m-d-Y_H-i-s').'.csv';
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
		
		if($this_order->date_modified != 0) {
			$timestamp = strtotime($this_order->date_modified);
			$mod_date =  date("m/d/Y", $timestamp);
			$order[] = $mod_date;
			echo $mod_date.',';
		} else {
			$order[] = '0';
			echo '0,';
		}
		
		$query = "SELECT total_content,last_modified FROM Order_Data WHERE order_id='".$id."'";
		$max_res = $this->sendAndGetOne($query);
		$order[] = $max_res->total_content;
			echo $max_res->total_content.',';
		$timestamp = strtotime($max_res->last_modified);
		$mod_date =  date("m/d/Y", $timestamp);
		$order[] = $mod_date;
			echo $mod_date.',';
		
		$query = "SELECT People.first_name,People.last_name,People.email,People.phone FROM People WHERE id='".$this_order->added_by_id."'";
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
				echo $newphone;
			} else {
				$order[] = '0';
				echo '0';
			}
		} else {
			$phone = '0';
			$order[] = $phone;
			echo $phone;
		}
		echo '<br />';
		return($order);
	}

}

function loadCSV() {
	$dr = new DeadlineReport();
	$res = $dr->_print();
	if($res) {
		$out = '<a href="'.UTI_URL.'src/data/reports/'.$res.'">Download Deadline Report CSV</a>';
	}
	return($out);
}

if(isset($_POST['action'])) {
	if($_POST['action'] = 'deadline_report') {
		echo loadCSV();
	}
}