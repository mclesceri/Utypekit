<?php

if(!session_id()) { session_start(); };

if ( !defined('SRC') ) require_once('../globals.php');

require_once(SERVICES."BaseService.php");

class Orders extends BaseService
{
	public function addNewOrder($item) {
		$mod_date = date('Y-m-d H:i:s');
		/*
	    
	    STEP 1: Deal with the organization associated with this order...
	    Organization is not required
	    
	    */
	    $organization = new stdClass();
		if(isset($item['organization_name'])) {
			$organization_name = $item['organization_name'];
			if($item['organization_type'] == "other") {
				$organization_type = $item['other_type'];
			} else {
				$organization_type = $item['organization_type'];
			}
			if($organization_type) {
				$query = 'SELECT id FROM Organizations WHERE type="'.$organization_type.'" AND name="'.$organization_name.'"';
				$res = $this->sendAndGetOne($query);
				$organization->new = false;
				if(isset($res->id)) { // This organization already exists...
					$organization->id = $res->id;
				} else {
					$organization->new = true;
					//name,type,added_by_type,added_by_id,date_modified,status
					$organization->name = $organization_name;
					$organization->type = $organization_type;
					$organization->added_by_type = $item['added_by_type'];
					$organization->added_by_id = 0;
					$organization->date_modified = date('Y-m-d H:i:s');
					$organization->status = 1;
					$query = $this->_insert('Organizations',$organization);
					$organization->id = $this->insertAndGetOne($query);
				}
			}
		}
		/*
	    
	    STEP 2: Deal with the person associated with this order...
	    
	    */
	    $person = new stdClass();
	    if(isset($item['added_by_id'])) {
	    	$person->id = $item['added_by_id'];
	    } else {
	    	$person = $this->makePerson($item,$organization);
	    	$query = $this->_insert('People',$person);
			$person->id = $this->insertAndGetOne($query);
			
			$update = new stdClass();
				$update->added_by_id = $person->id;
				$update->id = $person->id;
			$query = $this->_update('People',$update);
			$res = $this->sendAndGetOne($query);
	    }
	    //print_r($person);
	    /*
	    
	    STEP 3: Make the new order according to its type...
	    
	    */
		$order = new stdClass();
		if($item['account_type'] == 'demo') {
			$order->id = 1;
		} else {
			$last_order = $this->sendAndGetOne("SELECT order_number FROM Orders ORDER BY id DESC LIMIT 1");
			$last_order = explode('_',$last_order->order_number);
			$last_number = $last_order[0];
			$last_year = $last_order[1];
			
			$this_year = date('y');
			$this_number = (intval($last_number) + 1);
			if($this_year > $last_year) {
				$this_number = '0001';
			}
			$this_number = strval($this_number);
			if(strlen($this_number) < 4) {
				while(strlen($this_number) < 4) {
					$this_number = "0".$this_number;
				}
			}
			
			$order->title = urlencode($item['title']);
			$order->date_modified = $mod_date;
			$order->order_number = $this_number."_".$this_year;
			$order->added_by_type = $item['added_by_type'];
			$order->added_by_id = $person->id;
			$order->status = 1;
			
			$query = $this->_insert('Orders',$order);	
			$order->id = $this->insertAndGetOne($query);
		}
		//print_r($order);
		/*
			
		STEP 4: Link up all the data for this order...
		
		*/
		if($item['account_type'] != 'demo') {
			// If the organization is new, change the added_by_id to this user...
			if($organization) {
				if($organization->new) {
					$organizations = new stdClass();
						$organizations->added_by_id = $person->id;
						$organizations->id = $organization->id;
					$query = $this->_update('Organizations',$organizations);
					$this->sendAndGetOne($query);
				}
	            // Add the record to the Order_Organizations table
	            $order_organizations = new stdClass();
	            	$order_organizations->order_id = $order->id;
	            	$order_organizations->organization_id = $organization->id;
	            	$order_organizations->added_by_id = $person->id;
	            	$order_organizations->added_by_type = $item['added_by_type'];
	            	$order_organizations->date_modified = $mod_date;
	            	$order_organizations->status = 1;
	            $query = $this->_insert('Order_Organizations',$order_organizations);
	            $order_organizations->id = $this->insertAndGetOne($query);
			}
			// Add the people associated with this order
			// IF this is not a demo order...
			$contributors = array();
			
			foreach($item AS $key=>$val) {
				$val = trim($val);
				$exploded = explode('_',$key);
				if($exploded[0] == 'contributor') {
					$contributor = array();
					$con_key = $exploded[1];
					if($exploded[2]) {
						$con_key .= '_'.$exploded[2];
					}
					
					if($val != '' && $val != 0) {
						$contributor->{$con_key} = $val;
					}
				}
			}
			if(count($contributor) > 0) {
				$contributor = new stdClass($contributor);
				$contributors[] = $contributor;
			}
			if(isset($item['contributors'])) {
				$more = json_decode(urldecode($item['contributors']));
				foreach($more AS $m) {
					$adj = new stdClass();
					foreach($m AS $key=>$val) {
						$key = str_replace('contributor_','',$key);
						$adj->{$key} = $val;
					}
					$contributors[] = $adj;
				}
			}
				       	
	       	// If there are other people, find out what to do with them...
	       	if(count($contributors) > 0) {
				$other_people = array();
				for($c=0;$c<count($contributors);$c++) {
					$other_people[$c] = $this->makePerson($contributors[$c],$organization);
					$other_people[$c]->order_level = $contributors[$c]->order_level;
				}
				
				for($o=0;$o<count($other_people);$o++) {
					$op = $other_people[$o];
					// test to see if this person exists...
					$query  = "SELECT id FROM People WHERE first_name='".$op->first_name."' AND	last_name='".$op->last_name."' AND email='".$op->email."'";
					$c_id = $this->sendAndGetOne($query);
					if(!isset($c_id->id)) { // if they don't exist add them...
						// now check and see if they're in there 
						$query = $this->_insert('People',$op);
						$op->id = $this->insertAndGetOne($query);
					} else {
						$op->id = $c_id->id;
					}
				}
			}
			
			if($item['added_by_type'] != '2') {
				// Now add the people to this order in the Order_People table...
				$order_people = array();
				// Add the first order_person to the $order_people array...
				$order_people[0]['date_modified'] = $mod_date;
				$order_people[0]['order_id'] = $order->id;
				$order_people[0]['person_id'] = $person->id;
				$order_people[0]['level'] = '5';
				$order_people[0]['added_by_type'] = '1';
				$order_people[0]['added_by_id'] = $person->id;
				$order_people[0]['status'] = '1';
				// Now add all the other people to the $order_people array...
				if(isset($other_people)) {
					for($c=1;$c<=count($other_people);$c++) {
						$order_people[$c]['date_modified'] = $mod_date;
						$order_people[$c]['order_id'] = $order->id;
						$order_people[$c]['person_id'] = $other_people[$c]->id;
						$order_people[$c]['level'] = $other_people[$c]->order_level;
						$order_people[$c]['added_by_type'] = '1';
						$order_people[$c]['added_by_id'] = $person->id;
						$order_people[$c]['status'] = '1';
					}
				}
				
				foreach($order_people as $p) {
		    	    $query = $this->_insert('Order_People',$p);
					$this->insertAndGetOne($query);
				}
			}
			// if this order was submitted from the admin, see if there's a contractor assigned...
			if($item['added_by_type'] == '2') {
	    		if(isset($item['contractor'])) {
	    			$contractor = new stdClass();
		    			$contractor->order_number = $order->order_number;
		    			$contractor->date_modified = $mod_date;
		    			$contractor->added_by_id = $item['added_by_id'];
		    			$contractor->modified_by_id = $item['added_by_id'];
		    			$contractor->order_id = $order->id;
		    			$contractor->contractor_id = $item['contractor'];
		    			$contractor->status = 1;
	    			$query = $this->_insert('Orders_Contractors',$contractor);
	    			$this->insertAndGetOne($query);
	    		}
	    	}
			// Create the order meta data and insert it into the Order_Meta table...
			$meta = $this->makeOrderMeta($item);
			foreach($meta AS $k=>$v) {
				$om_id = $this->insertAndGetOne("INSERT INTO Order_Meta (date_modified,order_id,name,value) VALUES ('".date('Y-m-d H:i:s')."','".$order->id."','".$k."','".$v."')");
			}
			// Create the order data and insert it into the Order_Data table...
			$this->insertAndGetOne("INSERT INTO Order_Data (order_id) VALUES ('".$order->id."')");
		} else {
			// Now add the people to this order in the Order_People table...
			$order_person = new stdClass;
			// Add the first order_person to the $order_people array...
			$order_person->date_modified = date('Y-m-d H:i:s');
			$order_person->order_id = $order->id;
			$order_person->person_id = $person->id;
			$order_person->level = '5';
			$order_person->added_by_type = '1';
			$order_person->added_by_id = $person->id;
			$order_person->status = '1';
			
			$query = $this->_insert('Order_People',$order_person);
			$this->insertAndGetOne($query);
		}
		
		/*
		
		STEP 5: Return the success message...
		
		*/
		return( "{\"status\": \"true\", \"id\":\"".$order->id."\", \"user\":\"".$person->id."\", \"action\":\"order_list\", \"mode\":\"redirect\", \"message\": \"New Order Added\"}" );
	}
	
	public function updateOrder($item) {
		$order = array();
		$order_id = $item['id'];
        $query = 'UPDATE Orders SET date_modified="'.$item['date_modified'].'",title="'.$item['title'].'",status="'.$item['status'].'" WHERE id="'.$order_id.'"';
		$order = $this->sendAndGetOne($query);
		
		// DEAL WITH THE PEOPLE ASSOCIATED WITH THIS ORDER
		if(isset($sender)) {
			if($sender == 1) { // only if this is sent from UTypeIt
	    		// STEP 1: coelate the people and levels together
	    		$order_people = array();
	    		$i=0;
	    		foreach($item AS $key=>$val) {
	    			if(substr($key,0,7) == 'person_') {
	    				$personarr = explode('_',$key);
	    				$order_people[$i]['place'] = $personarr[1];
	    				$order_people[$i]['id'] = $val;
	    				//unset($item[$key]);
	    				$i++;
	    			}
	    		}
	    	}
    		
    		foreach($item AS $key=>$val) {
    			if(substr($key,0,12) == 'personlevel_') {
    				$levelarr = explode('_',$key);
    				$place = $levelarr[1];
    				$level = $val;
    				for($i=0;$i<count($order_people);$i++) {
    					if($place == $order_people[$i]['place']) {
    						$order_people[$i]['level'] = $level;
    					}
    				}
    				// unset($item[$key]);
    			}
    		}
    		
    		// STEP 2: get the people who used to be associated with this order
    		$old_people = $this->sendAndGetMany("SELECT id,person_id,level FROM Order_People WHERE order_id='".$order_id."'");
    		
    		// STEP 3: compare the lists
    		//If the person is changed, update the record.
    		foreach($order_people AS $o) {
    			$person['date_modified'] = $item['date_modified'];		
    			$person['order_id'] = $order_id;
    			$person['person_id'] = $o['id'];
    			$person['level'] = $o['level'];
    			$person['status'] = $o['status'];
    			$query = 'UPDATE Order_People SET ';
    			foreach($person as $key=>$val) {
    				$query .= $key.'="'.$val.'",';
    			}
    			$query = substr($query,0,-1);
    			$query .= ' WHERE id="'.$o['id'].'"';
    			//echo $query.'<br />';
    			foreach($old_people AS $p) {
    				if($o['id'] == $p->person_id) {
    					$this->sendAndGetOne($query);
    				}
    			}
    		}
    		
    		// If someone's new, add the person.
    		foreach($order_people AS $o) {
    			$person['date_modified'] = $item['date_modified'];		
    			$person['order_id'] = $order_id;
    			$person['person_id'] = $o['id'];
    			$person['level'] = $o['level'];
    			$person['added_by_type'] = 2;
    			$person['added_by_id'] = $_SESSION['user']->id;
    			$person['status'] = $o['status'];
    			$match = false;
    			foreach($old_people AS $p) {
    				if($p->person_id == $o['id']) {
    					$match = true;
    				}
    			}
    			if($match == false) {
    				$query = 'INSERT INTO Order_People (date_modified,order_id,person_id,level,added_by_type,added_by_id) VALUES (';
    				foreach($person as $key=>$val) {
    					$query .= '"'.$val.'",';
    				}
    				$query = substr($query,0,-1);
    				$query .= ")";
    				//echo $query.'<br />';
    				$res = $this->insertAndGetOne($query);
    			}
    		}
    		
    		// If someone's missing, get rid of the record.
    		foreach($old_people AS $p) {
    			$match = false;
    			foreach($order_people AS $o) {
    				if($o['id'] == $p->person_id) {
    					$match = true;
    				}
    			}
    			if($match == false) {
    				$this->sendAndGetOne("DELETE FROM Order_People WHERE id='".$p->id."'");
    			}
    		}
    	} else { // only if sent from UTI Admin...
    		if(isset($item['contractor'])) {
    		    $contractor['order_number'] = $_SESSION['order_number'];
    		    $contractor['date_modified'] = $item['date_modified'];
    		    $contractor['modified_by_id'] = $item['modified_by_id'];
    		    $contractor['order_id'] = $order_id;
    			$contractor['contractor_id'] = $item['contractor'];
    			$contractor['status'] = 1;
                $query = 'SELECT id FROM Orders_Contractors WHERE order_id="'.$order_id.'"';
                $conres = $this->sendAndGetOne($query);
                if($conres->id) {
                    $query = 'UPDATE Orders_Contractors SET ';
                    foreach($contractor AS $key=>$val) {
                        $query .= $key.'="'.$val.'",';
                    }
                    $query = substr($query,0,-1);
                    $query .= ' WHERE id="'.$conres->id.'"';
                    //echo $query.'<br />';
                    $this->sendAndGetOne($query);
                }  else {
                    $contractor['added_by_id'] = $_SESSION['user']->id;
                    $contractor['added_by_type'] = '2';
                    
                    $query = 'INSERT INTO Orders_Contractors (order_number, date_modified, modified_by_id, order_id, contractor_id, status, added_by_id, added_by_type) VALUES (';
                    foreach($contractor AS $key=>$val) {
                        $query .= '"'.$val.'",';
                    }
                    $query = substr($query,0,-1);
                    $query .= ')';
                    //echo $query.'<br />';
                    $this->insertAndGetOne($query);
                }
    		}
    	}
		// Organization is not required
		if($item['organization_name']) {
			// Find out what the "old" organization information is
			$query = 'SELECT Organizations.* FROM Organizations, Order_Organizations WHERE Order_Organizations.order_id="'.$order_id.'" AND Organizations.id=Order_Organizations.organization_id';
            $org = $this->sendAndGetOne($query);
            $is_org = false;
			if($org) {
				// Find out if the organization information has changed...
				if($item['organization_type'] != $org->type) {
					$is_org = true;
				}
				if($item['organization_name'] != $org->name) {
					$is_org = true;
				}
			} else {
				$is_org = true;
			}
			if($is_org == true) { // We have a new org, so…
				if($item['organization_type'] == "other") {
					$organization_type = $item['other_type'];
				} else {
					if(!$item['organization_type']) {
						$organization_type = 'Nonprofit';	
					} else {
						$organization_type = $item['organization_type'];
					}
				}
				// See if that 'new' org already exists in the list of organizations…
				$res = $this->sendAndGetOne('SELECT id FROM Organizations WHERE type="'.$item['organization_type'].'" AND name="'.$item['organization_name'].'"');
				// If there is already an organization of that type, by that name, then just use that id for the organization id...
				
				/*echo 'RES: ';
				print_r($res);*/
				
				
				if($res) {
					$organization_id = $res->id;
				} else { // Otherwise, we should add the new org to the database…
					$new_org['name'] = $item['organization_name'];
					$new_org['type'] = $organization_type;
					$new_org['added_by_type'] ='1';
					if(isset($_SESSION['user'])) {
						$user_id = $_SESSION['user']->id;
					} else {
						$user_id = $item['modified_by_id'];
					}
					$new_org['added_by_id'] = $user_id;
					$new_org['date_modified'] = date('Y-m-d H:i:s');
					$new_org['status'] = '1';
					$query = "INSERT INTO Organizations (name,type,added_by_type,added_by_id,date_modified,status) VALUES (";
					foreach($new_org AS $key=>$val) {
						$query .= '"'.$val.'",';
					}
					$query = substr($query,0,-1);
					$query .= ')';
					
					
					/*echo $query.'<br />';*/
					
					
					$organization_id = $this->insertAndGetOne($query);
					
					//echo 'ORGANIZATION_ID: '.$organization_id;
				}
				// Now see if there's already a record for organization to go along with this order…
				$res = $this->sendAndGetOne('SELECT id FROM Order_Organizations WHERE order_id="'.$order_id.'"');
				
				
				
				/*echo 'RES: ';
				print_r($res);*/
				
				
				
				// If there is, then just update that record with the new organization's id
				if($res) {
					$this->sendAndGetOne('UPDATE Order_Organizations SET organization_id="'.$organization_id.'" WHERE id="'.$res->id.'"');
				} else { // Otherwise, add the record to the Order_Organizations table
					$order_org['organization_id'] = $organization_id;
					$order_org['order_id'] = $order_id;
					$order_org['added_by_type'] ='1';
					if(isset($_SESSION['user'])) {
						$user_id = $_SESSION['user']->id;
					} else {
						$user_id = $item['modified_by_id'];
					}
					$order_org['added_by_id'] = $user_id;
					$order_org['date_modified'] = date('Y-m-d H:i:s');
					$order_org['status'] = '1';
					$query = "INSERT INTO Order_Organizations (organization_id,order_id,added_by_type,added_by_id,date_modified,status) VALUES (";
					foreach($order_org AS $key=>$val) {
						$query .= '"'.$val.'",';
					}
					$query = substr($query,0,-1);
					$query .= ')';
					
					
					
					//echo $query.'<br />';
					
					
					
					$order_org_id = $this->insertAndGetOne($query);
				}
			}
		}
				
		//print_r($item);
		$meta = $this->makeOrderMeta($item);
		
		/*if($item['use_subcategories'] != 'yes') {
			$res = $this->sendAndGetOne("SELECT id FROM Order_Meta WHERE order_id='".$order_id."' AND name='subcategories'");
			if($res->id) {
				$this->sendAndDelete("DELETE FROM Order_Meta WHERE id='".$res->id."'");
			}
		}*/
		//print_r($meta);
		foreach($meta AS $key=>$val) {
			if($val) {
				$newmeta['date_modified'] = date('Y-m-d H:i:s');
				$newmeta['order_id'] = $order_id;
				$newmeta['name'] = $key;
				$newmeta['value'] = $val;
				
				/// Find out if there's already a record of this type in the database for this order
				$query = "SELECT id FROM Order_Meta WHERE order_id='".$order_id."' AND name='".$key."'";
				$meta_id = $this->sendAndGetOne($query);
				if(isset($meta_id->id)) {
					$meta_id = $meta_id->id;
					$query = "UPDATE Order_Meta SET date_modified='".date('Y-m-d H:i:s')."',value='".addslashes($val)."' WHERE id='".$meta_id."'";
					$this->sendAndGetOne($query);
				} else {
					$query = "INSERT INTO Order_Meta (date_modified,order_id,name,value) VALUES (";
					foreach($newmeta AS $k=>$v) {
						if($v) {
							$query .= "'".$v."',";
						} else {
							$query .= "'0',";
						}
					}
					$query = substr($query,0,-1);
					$query .= ")";
					//echo $query.'<br />';
					$meta_id = $this->insertAndGetOne($query);
				}
				
				if($key == 'categories' || $key == 'subcategories') {
					$_SESSION[$key] = json_decode($val);
				} else {
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
		if($order_id) {
            return("{\"status\": \"true\", \"id\":\"".$order_id."\",\"action\":\"order_edit\", \"mode\":\"redirect\", \"message\": \"Order Updated\"}");
        } else {
            return("{\"status\":\"false\", \"message\": \"Error Saving Content\"}");
        }
	}
	
	public function getComposedOrder($order_id) {
		
        $order = new stdClass();
        
        // Get the order...
		$base_order = $this->sendAndGetOne("SELECT * FROM Orders WHERE id='".$order_id."'");
        $order->order = $base_order;
        
        $res = $this->sendAndGetOne("SELECT COUNT(id) FROM Order_Content WHERE order_id='".$order_id."' AND type='recipe'");
        $order->order->total_recipes = $res->{'COUNT(id)'};
        
        // Get the chairperson and cochairperson...
        $query = "SELECT People.*,Order_People.level AS order_level,Order_People.status as user_status FROM People,Order_People WHERE Order_People.order_id='".$order_id."' AND Order_People.level > 3 AND People.id=Order_People.person_id";
        $people = $this->sendAndGetMany($query);
        if($people) {
            foreach($people AS $p) {
                if($p->order_level == '5') {
                    $order->chairperson = $p;
                }
                if($p->order_level == '4') {
                    $order->cochairperson = $p;
                }
            }
        }
        
        // Get the contractor (if not UTI)...
        $query = "SELECT People.* FROM People,Orders_Contractors WHERE Orders_Contractors.order_id='".$order_id."' AND People.id=Orders_Contractors.contractor_id";
        $contractor = $this->sendAndGetOne($query);
        if($contractor) {
            $order->contractor = $contractor;
        }
        
        // Get the order meta
		$query = "SELECT * FROM Order_Meta WHERE order_id='".$order_id."'";
		$meta = $this->sendAndGetMany($query);
		if($meta) {
    		foreach($meta AS $m) {
    		    if($m->name == 'general_info') {
    		        $order->general_info = $m->value;
    		    } elseif($m->name == 'utypeit_info') {
                    $order->utypeit_info = $m->value;
                } elseif($m->name == 'categories') {
                    $order->categories = $m->value;
                } elseif($m->name == 'subcategories') {
                    $order->subcategories = $m->value;
                }
    		}
        }
		
		return($order);
	}
	
	public function getOrderList($start='',$limit='',$order_by='id') {
		$query = "SELECT * FROM Orders";
		if($order_by != 'chairperson') {
			$query .= " ORDER BY Orders.".$order_by;
		}
		if($limit) {
			$query .= ' LIMIT '.$start.','.$limit;
		}
		$orders = $this->sendAndGetMany($query);
		
		foreach($orders AS $o) {
			$query = "SELECT People.first_name,People.last_name FROM People,Order_People WHERE Order_People.order_id=".$o->id." AND Order_People.level=5 AND People.id=Order_People.person_id";
			
			$res = $this->sendAndGetOne($query);
			if($res) {
				$o->chairperson = $res->last_name.', '.$res->first_name;
			} else {
				$o->chairperson = "...";
				$added_by = $o->added_by_id;
				$query = "SELECT CONCAT(first_name, ' ', last_name) AS added_by FROM People WHERE People.id='".$added_by."'";
				$res = $this->sendAndGetOne($query);
				if($res) {
					$o->added_by = $res->added_by;
				}
			}
		}
		if($order_by == 'chairperson') {
			function byChair($a,$b) {
				if($a->chairperson[0] == $b->chairperson[0]) {
					return 0;
				} elseif($a->chairperson[0] < $b->chairperson[0]) {
					return -1;
				} elseif($a->chairperson[0] > $b->chairperson[0]) {
					return 1;
				}
			}
			usort($orders, 'byChair');
		}
		return($orders);
	}
	
	public function getCustomerOrderList($customer,$start='',$limit='',$order_by='id') {
		$query = "SELECT DISTINCT(order_id),level AS order_level, status AS user_status FROM Order_People WHERE person_id='".$customer."'";
		$orders = $this->sendAndGetMany($query);
		// Get the data for each order
		foreach($orders AS $o) {
			$order_id = $o->order_id;
			$query = "SELECT * FROM Orders WHERE id='".$order_id."'";
			$data = $this->sendAndGetOne($query);
			foreach($data AS $key=>$val) {
				$o->{$key} = $val;
			}
			$query = "SELECT CONCAT(People.first_name,' ',People.last_name) AS chairperson FROM People,Order_People WHERE Order_People.order_id='".$order_id."' AND Order_People.level='5' AND People.id=Order_People.person_id";
			$chairperson = $this->sendAndGetOne($query);
			$o->chairperson = $chairperson->chairperson;
		}
		return($orders);
	}
	
	public function getContractorOrderList($contractor,$start='',$limit='',$order_by='order_id') {
		$contractor_query = "SELECT order_id FROM Orders_Contractors WHERE contractor_id='".$contractor."'";
		$contractor_query .= ' ORDER BY '.$order_by.' DESC';
		if($limit) {
			$query .= ' LIMIT '.$start.','.$limit;
		}
		$res = $this->sendAndGetMany($contractor_query);
		$orders = array();
		if($res) {
			for($i=0;$i<count($res);$i++) {
				$o_query = 'SELECT * FROM Orders WHERE Orders.id="'.$res[$i]->order_id.'"';
				if($order_by != 'chairperson') {
					$o_query .= " ORDER BY Orders.".$order_by;
				}
				if($limit) {
					$o_query .= ' LIMIT '.$start.','.$limit;
				}
				$order = $this->sendAndGetOne($o_query);
				if($order) {
					$query = "SELECT People.first_name,People.last_name FROM People,Order_People WHERE Order_People.order_id=".$res[$i]->order_id." AND Order_People.level=5 AND People.id=Order_People.person_id";
					$sres = $this->sendAndGetOne($query);
					if($sres) {
						$order->chairperson = $sres->last_name.', '.$sres->first_name;
					} else {
						$order->chairperson = "...";
					}
						$orders[] = $order;
				}
			}
			if($order_by == 'chairperson') {
				function byChair($a,$b) {
					if($a->chairperson[0] == $b->chairperson[0]) {
						return 0;
					} elseif($a->chairperson[0] < $b->chairperson[0]) {
						return -1;
					} elseif($a->chairperson[0] > $b->chairperson[0]) {
						return 1;
					}
				}
				usort($orders, 'byChair');
			}
		} else {
			$orders = null;
		}/**/
		return($orders);
	}
	
	public function getOrderCount($type,$id) {
		switch($type) {
			case 'customer':
				$query = 'SELECT COUNT(Orders.id) AS COUNT FROM Orders,Order_People WHERE Order_People.person_id="'.$id.'" AND Order_People.order_id=Orders.id';
				break;
			case 'contractor':
				$query = 'SELECT COUNT(id) AS COUNT FROM Orders_Contractors WHERE contractor_id="'.$id.'"';
				break;
			default:
				$query = 'SELECT COUNT(*) AS COUNT FROM Orders';
		}
		$res = $this->sendAndGetOne($query);
		return( $res );
	}
	
	public function getOrderPeople($order_id) {
		$query = 'SELECT People.id,People.first_name,People.last_name,People.Email,People.phone,People.address1,People.address2,People.city,People.state,People.zip,Order_People.level FROM People,Order_People WHERE People.id=Order_People.person_id AND Order_People.order_id="'.$order_id.'" AND People.status="1"';
		$newop = $this->sendAndGetMany($query);
		
		return($newop);
	}
	
	public function getOrderPerson($id) {
		$query = 'SELECT first_name,last_name FROM People WHERE id="'.$id.'"';
		$person = $this->sendAndGetOne($query);
		$query = 'SELECT level FROM Order_People WHERE person_id="'.$id.'"';
		$nop = $this->sendAndGetOne($query);
		$result['id'] = $id;
		$result['name'] = $person->first_name.' '.$person->last_name;
		$result['level'] = $nop->level;
		return($result);
	}
	
	public function deleteOrder($id) {
		// delete the meta data
		$query = "DELETE FROM Order_Meta WHERE order_id='".$id."'";
		$this->sendAndDelete($query);
		
		// delete the people associations
		$query = "DELETE FROM Order_People WHERE order_id='".$id."'";
		$this->sendAndDelete($query);
		
		// delete the contractor associations
		$query = "DELETE FROM Orders_Contractors WHERE order_id='".$id."'";
		$this->sendAndDelete($query);
		
		// delete the organization associations
		$query = "DELETE FROM Order_Organizations WHERE order_id='".$id."'";
		$this->sendAndDelete($query);
		
		// delete the counted data
		$query = "DELETE FROM Order_Data WHERE order_id='".$id."'";
		$this->sendAndDelete($query);
		
		// delete the files
		$query = "DELETE FROM Order_Files WHERE order_id='".$id."'";
		$this->sendAndDelete($query);
		
		// delete the content
		$query = "SELECT content_id FROM Order_Content WHERE order_id='".$id."'";
		$content = $this->sendAndGetMany($query);
		if($content) {
			$gone = array();
			foreach($content AS $c) {
				$gone[] = $c->content_id;
			}
			$gone = implode(',',$gone);
			$query = "DELETE FROM Content WHERE id IN (".$gone.")";
			$this->sendAndDelete($query);
		}
		
		$query = "DELETE FROM Order_Content WHERE order_id='".$id."'";
		$this->sendAndDelete($query);
		
		// delete the order
		$query = "DELETE FROM Orders WHERE id='".$id."'";
		$this->sendAndDelete($query);
		
		 return("{\"status\": \"true\",\"id\":\"".$id."\", \"action\":\"order_list\", \"mode\":\"redirect\"}");
	}
	
	function makePerson($data,$organization) {
		
		$all = $this->_describe('People');
		
    	array_shift($all);
    	
    	$mod_date = date('Y-m-d H:i:s');
    	$person = new stdClass();
    	foreach($all AS $a) {
	    	if($a->type != 'timestamp') {
		    	if($a->type == 'datetime') {
			    	$person->{$a->name} = $mod_date;
		    	} else {
		    		if($a->name == 'organization_id') {
			    		$person->organization_id = $organization->id;
		    		} else if($a->name == 'level') {
				    	$person->level = 1;
			    	} else if($a->name == 'type') {
				    	$person->type = 1;
			    	} else if($a->name == 'meta') {
			    		$newsletter = 'newsletter:no';
						if(is_array($data)) {
							if(isset($data['newsletter'])) {
								$newsletter = 'newsletter:yes';
							}
						}
				    	$person->meta = $newsletter;
			    	} else if($a->name == 'status') {
				    	$person->status = 1;
			    	} else {
				    	foreach($data AS $key=>$val) {
					    	if($key == $a->name) {
						    	$person->{$a->name} = urlencode(stripslashes(urldecode($val)));
					    	}
				    	}
			    	}
		    	}
	    	}
    	}
    	return($person);
	}
	
	protected function makeOrderMeta($item) {
		// Order General Info
		$general_string = 'book_title1:'.urlencode($item['book_title1']).'|';
		$general_string .= 'book_title2:'.urlencode($item['book_title2']).'|';
		$general_string .= 'book_title3:'.urlencode($item['book_title3']).'|';
		$general_string .= 'book_count:'.$item['book_count'].'|';
		$general_string .= 'book_style:'.$item['book_style'].'|';
		$nutritionals = 'no';
		if(isset($item['nutritionals'])) {
			$nutritionals = $item['nutritionals'];
		}
		$general_string .= 'nutritionals:'.$nutritionals.'|';
		$contributors = '';
		if(isset($item['contributors'])) {
			$contributors = $item['contributors'];
		}
		$general_string .= 'contributors:'.$contributors.'|';
		//$general_string .= 'tableofcontents:'.$item['tableofcontents'].'|';
		$general_string .= 'order_index_by:'.$item['order_index_by'].'|';
		//$general_string .= 'index_page:'.$item['indexpage'].'|';
		$order_form = 'no';
		if(isset($item['order_form'])) {
			$order_form = $item['order_form'];
		}
		$general_string .= 'order_form:'.$order_form.'|';
		$order_form_name = null;
		if(isset($item['order_form_name'])) {
			$order_form_name = urlencode($item['order_form_name']);
		}
		$general_string .= 'order_form_name:'.$item['order_form_name'].'|';
		$order_form_address1 = null;
		if(isset($item['order_form_address1'])) {
			$order_form_address1 = urlencode($item['order_form_address1']);
		}
		$general_string .= 'order_form_address1:'.$order_form_address1.'|';
		$order_form_address2 = null;
		if(isset($item['order_form_address2'])) {
			$order_form_address2 = urlencode($item['order_form_address2']);
		}
		$general_string .= 'order_form_address2:'.$order_form_address2.'|';
		$order_form_city = null;
		if(isset($item['order_form_city'])) {
			$order_form_city = urlencode($item['order_form_city']);
		}
		$general_string .= 'order_form_city:'.$order_form_city.'|';
		$order_form_state = null;
		if(isset($item['order_form_state'])) {
			$order_form_state = $item['order_form_state'];
		}
		$general_string .= 'order_form_state:'.$item['order_form_state'].'|';
		$order_form_zip = null;
		if(isset($item['order_form_zip'])) {
			$order_form_zip = $item['order_form_zip'];
		}
		$general_string .= 'order_form_zip:'.$item['order_form_zip'].'|';
		$order_form_retail = null;
		if(isset($item['order_form_retail'])) {
			$order_form_retail = $item['order_form_retail'];
		}
		$general_string .= 'order_form_retail:'.$item['order_form_retail'].'|';
		$order_form_shipping = null;
		if(isset($item['order_form_shipping'])) {
			$order_form_shipping = $item['order_form_shipping'];
		}
		$general_string .= 'order_form_shipping:'.$item['order_form_shipping'].'|';
		$use_subcategories = 'no';
		if(isset($item['use_subcategories'])) {
			$use_subcategories = $item['use_subcategories'];
		}
		$general_string .= 'use_subcategories:'.$use_subcategories.'|';
		$recipes_continued = 'no';
		if(isset($item['recipes_continued'])) {
			$recipes_continued = $item['recipes_continued'];
		}
		$general_string .= 'recipes_continued:'.$recipes_continued.'|';
		$allow_notes = 'no';
		if(isset($item['allow_notes'])) {
			$allow_notes = $item['allow_notes'];
		}
		$general_string .= 'allow_notes:'.$allow_notes.'|';
		$use_icons = 'no';
		if(isset($item['use_icons'])) {
			$use_icons = $item['use_icons'];
		}
		$general_string .= 'use_icons:'.$use_icons.'|';
        $use_fillers = 'no';
        if(isset($item['use_fillers'])) {
            $use_fillers = $item['use_fillers'];
        }
		$general_string .= 'use_fillers:'.$use_fillers.'|';
		$filler_type = null;
        if(isset($item['filler_type'])) {
            $filler_type = $item['filler_type'];
        }
		$general_string .= 'filler_type:'.$filler_type.'|';
		$filler_set = null;
        if(isset($item['filler_set'])) {
            $filler_set = $item['filler_set'];
        }
		$general_string .= 'filler_set:'.$filler_set.'|';
		$recipe_format = 'Traditional';
        if(isset($item['recipe_format'])) {
            $recipe_format = $item['recipe_format'];
        }
		$general_string .= 'recipe_format:'.$recipe_format.'|';
		$design_type = 'CPI_Custom';
        if(isset($item['design_type'])) {
            $design_type = $item['design_type'];
        }
		$general_string .= 'design_type:'.$design_type.'|';
		$order_recipes_by = 'alpha';
        if(isset($item['order_recipes_by'])) {
            $order_recipes_by = $item['order_recipes_by'];
        }
		$general_string .= 'order_recipes_by:'.$order_recipes_by;
		$use_subcategories = 'no';
        if(isset($item['use_subcategories'])) {
            $use_subcategories = $item['use_subcategories'];
        }
		/*
		DEPRECATED
		if($use_subcategories == 'yes') {
			$general_string .= '|subtoc:'.$item['subtoc'];
		} else {
			$general_string .= '|subtoc:no';
		}*/
		if($item['added_by_type'] == '2') {
			$general_string .= '|uti:no';
		} else {
			$general_string .= '|uti:yes';
		}
		$meta['general_info'] = $general_string;
		
        // UTypeIt Preferences
        
        
		// Categories
		/*
		 * 
		 * Categories meta is a json string:
		 * {"categories":[{"number": "1","order": "1","parent":"0","name": "Appetizers%2C+Beverages"}, ... ]}
		 * 
		 * This function receives the category information from the input in a name value pair:
		 * name="category-title_1-0-1" value="Hors d'Oeuvres"
		 * where 1-0-1 equals number, parent, order and value equals name
		 *  
		 */
		$category_string = '{"categories":[';
		foreach($item AS $key=>$val) {
			if(substr($key,0,8) == 'category') {
				$category_string .= '{';
				$order_array = explode('_',$key);
				$data = $order_array[1];
				$data = explode('-',$data);
				for($d=0;$d<3;$d++) {
					if($d == 0) {
						$category_string .= '"number":"'.$data[$d].'",';
					} elseif($d == 1) {
						$category_string .= '"parent":"0",';
					} elseif($d == 2) {
						$category_string .= '"order":"'.$data[$d].'",';
					}
				}
				$category_string .= '"name": "'.urlencode($val).'"},';
			}
		}
		$category_string = substr($category_string,0,-1);
		$category_string .= ']}';
		$meta['categories'] = $category_string;
		
		// Subcategories
		/*
		 * 
		 * Categories meta is a json string:
		 * {"subcategories":[{"number": "1","order": "1","parent":"1","name": ""}, ... ]}
		 * 
		 * This function receives the category information from the input in a name value pair:
		 * name="subcategory-title_1-1-1" value="Hors d'Oeuvres"
		 * where 1-1-1 equals number, parent, order and value equals name
		 *  
		 */
		 $subcategory_string = '';
		if($use_subcategories == 'yes') {
			$subcategories = new stdClass();
			$subcategories->subcategories = array();
			//$subcategory_string = '{"subcategories":[';
			foreach($item AS $key=>$val) {
			    $test = explode('_',$key);
				if($test[0] == 'subcategory-title') {
					$subcategory = new stdClass();
					//$subcategory_string .= '{';
					$subdata = explode('-',$test[1]);
					/*$subcategory_string .= '"number":"'.$subdata[0].'",';
					$subcategory_string .= '"parent":"'.$subdata[1].'",';
					$subcategory_string .= '"order":"'.$subdata[2].'",';
					$subcategory_string .= '"name": "'.urlencode($val).'"},';*/
					$subcategory->number = $subdata[0];
					$subcategory->parent = $subdata[1];
					$subcategory->order = $subdata[2];
					$subcategory->name = urlencode($val);
					$subcategories->subcategories[] = $subcategory;
				}
			}
			//$subcategory_string = substr($subcategory_string,0,-1);
			$subcategory_string = json_encode($subcategories);
			//$subcategory_string .= ']}';
		}
		$meta['subcategories'] = $subcategory_string;
		return($meta);
	}
}

if(isset($_POST['action'])) {
    $action= $_POST['action'];
    $nc = new Orders();
    switch($action) {
        case 'order_add':
            $res = $nc->addNewOrder($_POST);
            echo $res;
            break;
        case 'order_edit':
            $res = $nc->updateOrder($_POST);
            //$res = 'hello';
            echo $res;
            break;
        case 'order_delete':
			//echo $_POST['id'];
			$res = $nc->deleteOrder($_POST['id']);
			echo $res;
            break;
    }
}