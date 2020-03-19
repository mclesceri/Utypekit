<?php

session_start();

$action = $_REQUEST['action'];

$out = '<h3>Results</h3>';

require_once('../services/BaseService.php');
require_once('../services/Orders.php');

function getCount($service) {
	switch($service) {
		case 'orders_search':
			$tablename = 'Orders';
			break;
		case 'people_search':
			$tablename = 'People';
	}
	$base = new BaseService();
	$res = $base->sendAndGetOne("SELECT COUNT(*) AS COUNT FROM ".$tablename, MDB2_FETCHMODE_ORDERED);
	return( $res->count );
}

function runQuery($post_object) {
	
	$base = new BaseService();

	switch($post_object['m-submit']) {
		
		case 'orders_search':
			
			if($post_object['search_by'] == 'chairperson') {
				$query = 'SELECT id FROM People WHERE concat(first_name," ",last_name)';
				if($post_object['search_condition'] == 'is') {
					$query .= '="'.$post_object['search_term'].'"';
				} elseif($post_object['search_condition'] == 'like') {
					$query .= ' LIKE "%'.$post_object['search_term'].'%"';
				} elseif($post_object['search_condition'] == 'greater') {
					$query .= '>"'.$post_object['search_term'].'"';
				} elseif($post_object['search_condition'] == 'lesser') {
					$query .= '<"'.$post_object['search_term'].'"';
				}
			
				$res = array();
				$people = $base->sendAndGetMany($query);
				if($people) {
					for($p=0;$p<count($people);$p++) {
						$person = $people[$p]->id;
						$ask = 'SELECT order_id FROM Order_People WHERE level="5" AND person_id="'.$person.'"';
						$found = $base->sendAndGetOne($ask);
						if($found) {
							$res[] = $found;
						}
					}
				}
			
				if(count($res) > 0) {
					$no = new Orders();
					$orders = array();
					foreach($res AS $o) {
						$order_id = $o->order_id;
						$order = $base->sendAndGetOne('SELECT id,title,date_added,order_number,status FROM Orders WHERE id="'.$order_id.'"');
						$chairperson_id = $base->sendAndGetOne('SELECT person_id FROM Order_People WHERE order_id="'.$order_id.'" AND level="5"');
						$chairperson_id = $chairperson_id->person_id;
						$chairperson = $no->getOrderPerson($chairperson_id);
						if($chairperson) {
							$chairname = $chairperson['name'];
							$order->chairperson = $chairname;
						}
						$orders[] = $order;
					}
				}
			
				if($orders) {
					return($orders);
				} else {
					return('0');
				}
			
			} elseif($post_object['search_by'] == 'cochairperson') {
				$query = 'SELECT id FROM People WHERE concat(first_name," ",last_name)';
				if($post_object['search_condition'] == 'is') {
					$query .= '="'.$post_object['search_term'].'"';
				} elseif($post_object['search_condition'] == 'like') {
					$query .= ' LIKE "%'.$post_object['search_term'].'%"';
				} elseif($post_object['search_condition'] == 'greater') {
					$query .= '>"'.$post_object['search_term'].'"';
				} elseif($post_object['search_condition'] == 'lesser') {
					$query .= '<"'.$post_object['search_term'].'"';
				}
			
				$res = array();
				$people = $base->sendAndGetMany($query);
				if($people) {
					for($p=0;$p<count($people);$p++) {
						$person = $people[$p]->id;
						$ask = 'SELECT order_id FROM Order_People WHERE level="5" AND person_id="'.$person.'"';
						$found = $base->sendAndGetOne($ask);
						if($found) {
							$res[] = $found;
						}
					}
				}
			
				$no = new Orders();
				$orders = array();
				foreach($res AS $o) {
					$order_id = $o->order_id;
					$order = $base->sendAndGetOne('SELECT id,title,date_added,order_number,status FROM Orders WHERE id="'.$order_id.'"');
					$chairperson_id = $base->sendAndGetOne('SELECT person_id FROM Order_People WHERE order_id="'.$order_id.'" AND level="4"');
					$chairperson_id = $chairperson_id->person_id;
					$chairperson = $no->getOrderPerson($chairperson_id);
					if($chairperson) {
						$chairname = $chairperson['name'];
						$order->chairperson = $chairname;
					}
					$orders[] = $order;
				}
				if(count($orders) > 0) {
					return($orders);
				} else {
					return('0');
				}
			} elseif($post_object['search_by'] == 'date_added') {
				// dates must come in as mm/dd/yyyy
				$tmp = explode('/',$post_object['search_term']);
				$search_date = $tmp[2].'-'.$tmp[0].'-'.$tmp[1].' 00:00:01';
				
				$query = 'SELECT id,title,date_added,order_number,status FROM Orders';
				$query .= ' WHERE date_added';
				if($post_object['search_condition'] == 'is') {
					$query .= '="'.$search_date.'"';
				} elseif($post_object['search_condition'] == 'like') {
					$query .= ' LIKE "%'.$search_date.'%"';
				} elseif($post_object['search_condition'] == 'greater') {
					$query .= '>"'.$search_date.'"';
				} elseif($post_object['search_condition'] == 'lesser') {
					$query .= '<"'.$search_date.'"';
				}
				$query .= ' ORDER BY '.$post_object['search_by'];
				
				$orders = $base->sendAndGetMany($query);
				$no = new Orders();
				foreach($orders AS $o) {
					$order_id = $o->id;
					$chairperson_id = $base->sendAndGetOne('SELECT person_id FROM Order_People WHERE order_id="'.$order_id.'" AND level="5"');
					$chairperson_id = $chairperson_id->person_id;
					$chairperson = $no->getOrderPerson($chairperson_id);
					if($chairperson) {
						$chairname = $chairperson['name'];
						$o->chairperson = $chairname;
					}
				}
				if(count($orders) > 0) {
					return($orders);
				} else {
					return('0');
				}
			} elseif($post_object['search_by'] == 'status') {
				if(is_int($post_object['search_by'])) {
					$search_status = $post_object['search_by'];
				} else {
					$tmp = strtolower($post_object['search_by']);
					switch($tmp) {
						case 'inactive':
							$search_status = 0;
							break;
						case 'data entry':
							$search_status = 1;
							break;
						case 'editorial':
							$search_status = 2;
							break;
						case 'customer review':
							$search_status = 3;
							break;
						case 'approved':
							$search_status = 4;
							break;
						case 'proofing':
							$search_status = 5;
							break;
						case 'to print':
							$search_status = 6;
							break;
					}
				}
				$query = 'SELECT id,title,date_added,order_number,status FROM Orders';
				$query .= ' WHERE status';
				if($post_object['search_condition'] == 'is') {
					$query .= '="'.$search_status.'"';
				} elseif($post_object['search_condition'] == 'like') {
					$query .= ' LIKE "%'.$search_status.'%"';
				} elseif($post_object['search_condition'] == 'greater') {
					$query .= '>"'.$search_status.'"';
				} elseif($post_object['search_condition'] == 'lesser') {
					$query .= '<"'.$search_status.'"';
				}
				$query .= ' ORDER BY '.$post_object['search_by'];
				
				$orders = $base->sendAndGetMany($query);
				$no = new Orders();
				foreach($orders AS $o) {
					$order_id = $o->id;
					$chairperson_id = $base->sendAndGetOne('SELECT person_id FROM Order_People WHERE order_id="'.$order_id.'" AND level="5"');
					$chairperson_id = $chairperson_id->person_id;
					$chairperson = $no->getOrderPerson($chairperson_id);
					if($chairperson) {
						$chairname = $chairperson['name'];
						$o->chairperson = $chairname;
					}
				}
				if(count($orders) > 0) {
					return($orders);
				} else {
					return('0');
				}
			} else {
				$query = 'SELECT id,title,date_added,order_number,status FROM Orders';
				$query .= ' WHERE '.$post_object['search_by'];
				if($post_object['search_condition'] == 'is') {
					$query .= '="'.$post_object['search_term'].'"';
				} elseif($post_object['search_condition'] == 'like') {
					$query .= ' LIKE "%'.$post_object['search_term'].'%"';
				} elseif($post_object['search_condition'] == 'greater') {
					$query .= '>"'.$post_object['search_term'].'"';
				} elseif($post_object['search_condition'] == 'lesser') {
					$query .= '<"'.$post_object['search_term'].'"';
				}
				$query .= ' ORDER BY '.$post_object['search_by'];
				
				$orders = $base->sendAndGetMany($query);
				$no = new Orders();
				foreach($orders AS $o) {
					$order_id = $o->id;
					$chairperson_id = $base->sendAndGetOne('SELECT person_id FROM Order_People WHERE order_id="'.$order_id.'" AND level="5"');
					$chairperson_id = $chairperson_id->person_id;
					$chairperson = $no->getOrderPerson($chairperson_id);
					if($chairperson) {
						$chairname = $chairperson['name'];
						$o->chairperson = $chairname;
					}
				}
				if(count($orders) > 0) {
					return($orders);
				} else {
					return('0');
				}
			}
			break;
		case 'people_search':
			if($post_object['search_by'] == 'date_added') {
				// dates must come in as mm/dd/yyyy
				$tmp = explode('/',$post_object['search_term']);
				$search_date = $tmp[2].'-'.$tmp[0].'-'.$tmp[1].' 00:00:01';
				
				$query = 'SELECT id,first_name,last_name,email,state,level,date_added,status FROM People';
				if($post_object['search_type'] == 'customers') {
					$query .= ' WHERE type="1"';
				} else {
					$query .= ' WHERE type="2"';
				}
				$query .= ' AND date_added';
				if($post_object['search_condition'] == 'is') {
					$query .= '="'.$search_date.'"';
				} elseif($post_object['search_condition'] == 'like') {
					$query .= ' LIKE "%'.$search_date.'%"';
				} elseif($post_object['search_condition'] == 'greater') {
					$query .= '>"'.$search_date.'"';
				} elseif($post_object['search_condition'] == 'lesser') {
					$query .= '<"'.$search_date.'"';
				}
				$query .= ' ORDER BY '.$post_object['search_by'];
				
				$people = $base->sendAndGetMany($query);
				
				if($post_object['search_type'] == 'customers') {
					foreach($people as $p) {
						$sq = 'SELECT level FROM Order_People WHERE person_id="'.$p->id.'"';
						$res = $base->sendAndGetOne($sq);
						$p->level = $res->level;
					}
				}
				
				if(count($people) > 0) {
					return($people);
				} else {
					return('0');
				}
			} elseif($post_object['search_by'] == 'status') {
				if(is_int($post_object['search_by'])) {
					$search_status = $post_object['search_by'];
				} else {
					$tmp = strtolower($post_object['search_by']);
					switch($tmp) {
						case 'inactive':
							$search_status = 0;
							break;
						case 'active':
							$search_status = 1;
							break;
					}
				}
				$query = 'SELECT id,first_name,last_name,email,state,level,date_added,status FROM People';
				if($post_object['search_type'] == 'customers') {
					$query .= ' WHERE type="1"';
				} else {
					$query .= ' WHERE type="2"';
				}
				$query .= ' AND status';
				
				if($post_object['search_condition'] == 'is') {
					$query .= '="'.$search_status.'"';
				}
				$query .= ' ORDER BY status';
				
				$people = $base->sendAndGetMany($query);
				
				if($post_object['search_type'] == 'customers') {
					foreach($people as $p) {
						$sq = 'SELECT level FROM Order_People WHERE person_id="'.$p->id.'"';
						$res = $base->sendAndGetOne($sq);
						$p->level = $res->level;
					}
				}
				
				if(count($people) > 0) {
					return($people);
				} else {
					return('0');
				}
			} else {
				$query = 'SELECT id,first_name,last_name,email,state,level,date_added,status FROM People';
				if($post_object['search_for'] == 'first_name' || $post_object['search_for'] == 'last_name' || $post_object['search_for'] == 'city' || $post_object['search_for'] == 'login') {
					$search_term = urlencode($post_object['search_term']);
				} else {
					$search_term = $post_object['search_term'];
				}
				
				if($post_object['search_type'] == 'customers') {
					$query .= ' WHERE type="1"';
				} else {
					$query .= ' WHERE type="2"';
				}
				$query .= ' AND '.$post_object['search_for'];
				if($post_object['search_by'] == 'is') {
					$query .= '="'.$search_term.'"';
				} elseif($post_object['search_by'] == 'like') {
					$query .= ' LIKE "%'.$search_term.'%"';
				} elseif($post_object['search_by'] == 'greater') {
					$query .= '>"'.$search_term.'"';
				} elseif($post_object['search_by'] == 'lesser') {
					$query .= '<"'.$search_term.'"';
				}
				$query .= ' ORDER BY '.$post_object['search_for'];
				$people = $base->sendAndGetMany($query);
				
				if($post_object['search_type'] == 'customers') {
					foreach($people as $p) {
						$sq = 'SELECT level FROM Order_People WHERE person_id="'.$p->id.'"';
						$res = $base->sendAndGetOne($sq);
						$p->level = $res->level;
					}
				}
				
				if(count($people) > 0) {
					return($people);
				} else {
					return('0');
				}
			break;
		}	
	}
}

if($_GET['start']) {
	$start = $_GET['start'];
}
if($_GET['limit']) {
	$limit = $_GET['limit'];
}
if($_GET['orderby']) {
	$orderby = $_GET['orderby'];
}

$_SESSION['search'] = array('post'=>$_POST,'get'=>$GET);

$list = array();
switch($_POST['m-submit']) {
	case 'orders_search':
		$list_name = 'orders_list';
		$orders = runQuery($_POST);
		if($orders != '0') {
			$data = array();
			$columns = array('ID','Title','Date Added','Order Number','Chairperson','Status','');
			for($o=0;$o<count($orders);$o++) {
				$data[$o]['ID'] = $orders[$o]->id;
				$data[$o]['Title'] = $orders[$o]->title;
				$add_date = new DateTime($orders[$o]->date_added);
				$data[$o]['Date Added'] = date_format($add_date,'M d, Y');
				$data[$o]['Order Number'] = $orders[$o]->order_number;
				$data[$o]['Chairperson'] = $orders[$o]->chairperson;
				$data[$o]['Status'] = $orders[$o]->status;
				$data[$o]['Edit'] = $orders[$o]->id;
			}
			echo drawTable('Edit Order','order_edit','order_edit',$columns,$start,$limit,$data);
		} else {
			echo 'There are no records that meet the given search criteria. Please check your search and try again.<br />';
		}
		break;
	case 'people_search':
		$list_name = 'people_search';
		$people = runQuery($_POST);
		if($people != '0') {
			$data = array();
			$columns = array('ID','Last Name','Email','Level','Status','');
			for($p=0;$p<count($people);$p++){
				$data[$p]['ID'] = $people[$p]->id;
				$data[$p]['Name']= $people[$p]->first_name.' '.$people[$p]->last_name;
				$data[$p]['Email'] = $people[$p]->email;
				$tl = $people[$p]->level;
				$level = '';
				switch($tl) {
					case '0':
						$level = 'Inactive Member';
						break;
					case '1':
						$level = 'Demo Account';
						break;
					case '2':
						$level = 'Contributor';
						break;
					case '3':
						$level = 'Committee Member';
						break;
					case '4':
						$level = 'Cochairperson';
						break;
					case '5':
						$level = 'Chairperson';
						break;
					case '6':
						$level = 'Contractor';
						break;
					case '7':
						$level = 'Customer Service';
						break;
					case '8':
						$level = 'Administrator';
						break;
					case '9':
						$level = 'Super User';
						break;
					
				}
				$data[$p]['Level'] = $level;
				$data[$p]['Status'] = $people[$p]->status;
				$data[$p]['Edit'] = $people[$p]->id;
			}
			if($_POST['search_type'] == 'customers') {
				$title = "Edit Customer";
				$list = 'people_edit';
				$edit_action = 'customers_edit';
			} elseif($_POST['search_type'] == 'contractors') {
				$title = "Edit Contractor";
				$list = 'people_edit';
				$edit_action = 'contractors_edit';
			} elseif($_POST['search_type'] == 'users') {
				$title = "Edit User";
				$list = 'people_edit';
				$edit_action = 'users_edit';
			}
			echo drawTable($title,$list,$edit_action,$columns,$start,$limit,$data);
		} else {
			echo 'There are no records that meet the given search criteria. Please check your search and try again.<br />';
		}
		break;
}

function drawTable($title,$list,$action,$columns,$start,$limit,$data) {
	$out .= "<table class='listTable' cellpadding='0' cellspacing='0'>";
	foreach($columns AS $c) {
		$orderby = strtolower(str_replace(' ','_',$c));
		$out .= '<td class="listHeader">'.$c.'</td>';
	}
	$out .= "</tr>\r";
	
	for($p=0;$p<count($data);$p++) {
		$out .= '<tr>';
		foreach($data[$p] as $e=>$v) {
			if($e == 'Status') {
				$status = $v;
			}
			if($e != 'Edit') {
				$out .= '<td class="listItem">'.stripslashes(urldecode($v)).'</td>';
			} else {
				if($status == 1) {
					$class = 'listEdit';
				} else {
					$class = 'listEditInactive';
				}
				$out .= '<td class="listEdit"><a href="#" onclick="setContent(\''.$list.'\',{mode:\'redirect\',action:\''.$action.'\',id:\''.$v.'\'})">Edit</a></td>';
			}
		}
		$out .= '</tr>';
	}
	
	// Draw the close
	$out .= '</table>';
	
	return( $out );
}
?>