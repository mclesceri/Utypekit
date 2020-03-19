<?
define('FORCE_HTTPS', true);

session_start();

if ( !defined('SRC') ) require_once('../globals.php');

/*
*
* Functions to process the admin-side forms
*
*/
require_once(SERVICES.'BaseService.php');
require_once(SERVICES.'People.php');
require_once(SERVICES.'Orders.php');
require_once(SERVICES.'Cookbook.php');
require_once(SERVICES.'Email.php');

if($_REQUEST['action']) {
	if(substr($_GET['action'],-7) == '_delete') {
		$action = $_GET['action'];
	} else {
		if($_REQUEST['action']) {
			$action = $_REQUEST['action'];
		}
	}
}

switch($action) {
	case 'customer_add':
		$id = createPerson();

		if(!is_numeric($id)) {
			echo '<span style="color: #FF0000">There has been a problem with the entry. Please click the "add" button to try again.</span>';
		} else {
			echo $id;
		}
		break;
	case 'customer_edit':
		$id = updatePerson();
		
		if(!is_numeric($id)) {
			echo '<span style="color: #FF0000">There has been a problem with the entry. Please click the "add" button to try again.</span>';
		} else {
			echo $id;
		}
		break;
	case 'customers_delete':
		$person_id = $_POST['id'];
		$newPeople = new People();
		$newPeople->removePerson($person_id);
		break;
	case 'customer_search':
		$np = new People();
		$query = 'SELECT id FROM People WHERE first_name="'.$_GET['first_name'].'" AND last_name="'.$_GET['last_name'].'"';
		$res = $np->sendAndGetMany($query);
		if($res) {
			for($i=0;$i<count($res);$i++) {
				$query = 'SELECT id FROM People WHERE email="'.urlencode($_GET['email']).'" AND id="'.$res[$i]->id.'"';
				$subres = $np->sendAndGetOne($query);
				if($subres) {
					die('{"status":"false","message":"This account holder is already in the system. You can log in using the form on the U-Type-It&trade; <em>Online</em> home page, or create a new account using a different name. If you have lost your password, use the lost password link below the login form."}');
				} else {
					$query = 'SELECT id FROM People WHERE email="'.$_GET['email'].'" AND id="'.$res[$i]->id.'"';
					$subres = $np->sendAndGetOne($query);
					if($subres) {
						die('{"status":"false","message":"This account holder is already in the system. You can log in using the form on the U-Type-It&trade; <em>Online</em> home page, or create a new account using a different name. If you have lost your password, use the lost password link below the login form."}');
					}
				}
			}
		}
		$query = 'SELECT id FROM People WHERE login="'.$_GET['login'].'" AND password="'.$_GET['password'].'"';
		$res = $np->sendAndGetMany($query);
		if($res) {
			die('{"status":"false","message":"This account holder is already in the system. You can log in using the form on the U-Type-It&trade; <em>Online</em> home page, or create a new account using a different name. If you have lost your password, use the lost password link below the login form."}');
		}
		echo '{"status":"true"}';
		break;
	case 'user_add':
		$id = createPerson();
		
		if(!is_numeric($id)) {
			echo '<span style="color: #FF0000">There has been a problem with the entry. Please click the "add" button to try again.</span>';
		} else {
			echo $id;
		}
		break;
	case 'user_edit':
		$id = updatePerson();
		
		if(!is_numeric($id)) {
			echo '<span style="color: #FF0000">There has been a problem with the entry. Please click the "add" button to try again.</span>';
		} else {
			echo $id;
		}
		break;
	case 'users_delete':
		$person_id = $_POST['id'];
		$newPeople = new People();
		$newPeople->removePerson($person_id);
		break;
	case 'user_search':
		break;
	case 'contractor_add':
		$id = createPerson();
		
		if(!is_numeric($id)) {
			echo '<span style="color: #FF0000">There has been an error creating this record. Please contact technical support for further help.</span>';
		} else {
			echo $id;
		}
		break;
	case 'contractor_edit':
		$id = updatePerson();
		
		if(!is_numeric($id)) {
			echo '<span style="color: #FF0000">There has been a problem with the entry. Please click the "add" button to try again.</span>';
		} else {
			echo $id;
		}
		break;
	case 'contractors_delete':
		$person_id = $_POST['id'];
		$newPeople = new People();
		$newPeople->removePerson($person_id);
		break;
	case 'contractor_search':
		break;
	case 'recipe_users':
		$nb = new BaseService();
		$order_id = $_GET['order_id'];
		$query = "SELECT People.id,People.first_name,People.last_name FROM People,Order_People WHERE Order_People.order_id='".$order_id."' AND People.id=Order_People.person_id ORDER BY id ASC";
		$users = $nb->sendAndGetMany($query);
		$out = "<select name=\"recipe_search_term\">";
		foreach($users AS $u) {
			$out .= "<option value=\"".$u->id."\">".$u->first_name." ".$u->last_name."</option>";
		}
		$out .= "</select>";
		echo $out;
		break;
	case 'order_users':
		$nb = new BaseService();
		$query = "SELECT People.id,People.first_name,People.last_name FROM People,Order_People WHERE Order_People.level='5' AND People.id=Order_People.person_id ORDER BY id ASC";
		$users = $nb->sendAndGetMany($query);
		$out = "<select name=\"orders_search_term\">";
		foreach($users AS $u) {
			$out .= "<option value=\"".$u->id."\">".$u->first_name." ".$u->last_name."</option>";
		}
		$out .= "</select>";
		echo $out;
		break;
	case 'recipe_search':
		$nb = new BaseService();
		foreach($_POST AS $key=>$val) {
			$$key = $val;
		}
		if(isset($month)) {
			$converted_date = $year.'-'.$month.'-'.$day;
			$recipe_search_term = $converted_date;
		}
		/*
		 * action,order_id,recipe_search_for,recipe_search_by,recipe_search_term
		 *
		 * possible search for:
		 *		ID
		 *		Title
		 *		Date Added
		 *		Added By ID
		 *		Status
		 * possible search by:
		 *		is
		 *		like
		 *		not
		 * 
		 */
		$query = "SELECT Order_Content.*,People.first_name,People.last_name FROM Order_Content,People WHERE 
					Order_Content.order_id='".$order_id."'
					 AND 
					People.id=Order_Content.added_by_id";
		
		$query .= " AND Order_Content.".$recipe_search_for;
		echo "SEARCH TERM: ".stripslashes($recipe_search_term);
		if($recipe_search_for == 'status') {
			if(!is_numeric($recipe_search_for)) {
				switch(ucwords($recipe_search_term)) {
					case 'Inactive':
						$recipe_search_term = '0';
						break;
					case 'Data Entry':
						$recipe_search_term = '1';
						break;
					case 'Editorial':
						$recipe_search_term = '2';
						break;
					case 'Approved':
						$recipe_search_term = '3';
						break;
				}
			}
		} elseif($recipe_search_for == 'title') {
			$recipe_search_term = urlencode(stripslashes($recipe_search_term));
		}
		
		if($recipe_search_by == 'is') {
			$query .= "='".$recipe_search_term."'";
		} elseif($recipe_search_by == 'like') {
			$query .= " LIKE '%".$recipe_search_term."%'";
		} elseif($recipe_search_by == 'less') {
			$query .= "<'".$recipe_search_term."'";
		} elseif($recipe_search_by == 'more') {
			$query .= ">'".$recipe_search_term."'";
		} elseif($recipe_search_by == 'not') {
			$query .= " NOT LIKE '%".$recipe_search_term."%'";
		}
		$query .= ' ORDER BY id ASC';
		
		$recipes = $nb->sendAndGetMany($query);
		if(!$recipes) {
			echo '<div style="margin-top: 5px; font-weight: bold;">There are no results to display.</div>';
		} else {
			$columns = array('ID','Title','Date Added','Added By ID','Status','');
			$recipes_list = array();
			for($o=0;$o<count($recipes);$o++) {
				$recipes_list[$o]['ID'] = $recipes[$o]->id;
				$recipes_list[$o]['Title'] = $recipes[$o]->title;
				$add_date = new DateTime($recipes[$o]->date_added);
				$recipes_list[$o]['Date Added'] = date_format($add_date,'M d, Y');
				$added_by = $recipes[$o]->added_by_id;
				$np = new People();
				$res= $np->getPerson($added_by);
				if($res) {
					$added_name = $res->first_name.' '.$res->last_name;
				} else {
					$added_name = "Person Deleted";
				}
				$recipes_list[$o]['Added By'] = $added_name;
				$recipes_list[$o]['Status'] = $recipes[$o]->status;
				$recipes_list[$o]['Edit'] = $orders[$o]->id;
			}
			
			$out .= '<table class="listTable" cellpadding="0" cellspacing="0">';
			$out .= "<tr>";
			foreach($columns AS $c) {
				$thisorder = strtolower(str_replace(' ','_',$c));
				$out .= '<td class="listHeader"><a href="#" class="subheaderLink" onclick="setContent(\'recipe_list\',{mode:\'redirect\',start:\''.$start.'\',limit:\''.$_SESSION['list_limit'].'\',orderby:\''.$thisorder.'\'})">'.$c.'</a></td>';
			}
			$out .= "</tr>\r";
			
			for($p=0;$p<count($recipes_list);$p++) {
				$out .= '<tr>';
				$id = $recipes_list[$p]['ID'];
				foreach($recipes_list[$p] as $e=>$v) {
					if($e == 'Status') {
						if($v == -1) {
							$status = 'Unselected';
							$bgcol = 'FF9E9E';
						} elseif($v == 0) {
							$status = 'Inactive';
							$bgcol = 'FF9E9E';
						} elseif($v == 1) {
							$status = 'Data Entry';
							$bgcol = 'FFD59E';
						} elseif($v == 2) {
							$status = 'Editorial';
							$bgcol = 'C6FF9E';
						} elseif($v == 3) {
							$status = 'Approved';
							$bgcol = '8CFF84';
						}
						$out .= '<td class="listItem" style="background-color: #'.$bgcol.'">'.$status.'</td>';
					}elseif($e == 'Edit') {
						$out .= '<td class="listItem"><a href="recipe_edit.php?action=recipe_edit&id='.$id.'" class="listEdit">EDIT</a></td>';
					} else {
						$out .= '<td class="listItem">'.urldecode($v).'</td>';
					}
				}
				$out .= '</tr>';
			}
		
			// Draw the close
			$out .= '</table>';
		}
		echo $out;
		break;
	case 'orders_search':
		$nb = new BaseService();
		foreach($_POST AS $key=>$val) {
			$$key = $val;
		}
		if(isset($month)) {
			$converted_date = $year.'-'.$month.'-'.$day;
			$orders_search_term = $converted_date;
		}
		/*
		 * action,order_search_for,order_search_by,order_search_term
		 *
		 * possible search for:
		 *		ID
		 *		Title
		 *		Order Number
		 *		Date Added
		 *		Added By ID
		 *		Status
		 * possible search by:
		 *		is
		 *		like
		 *		not
		 * 
		 */
		
		$query = "SELECT Orders.* FROM Orders WHERE ".$orders_search_for;
		
		if($orders_search_by == 'is') {
			$query .= "='".$orders_search_term."'";
		} elseif($orders_search_by == 'like') {
			$query .= " LIKE '%".$orders_search_term."%'";
		} elseif($orders_search_by == 'less') {
			$query .= "<'".$orders_search_term."'";
		} elseif($orders_search_by == 'more') {
			$query .= ">'".$orders_search_term."'";
		} elseif($orders_search_by == 'not') {
			$query .= " NOT LIKE '%".$orders_search_term."%'";
		}
		$query .= ' ORDER BY id ASC';
		
		$orders = $nb->sendAndGetMany($query);
		if(!$orders) {
			echo '<div style="margin-top: 5px; font-weight: bold;">There are no results to display.</div>';
		} else {
			$columns = array('ID','Title','Date Added','Order Number','Chairperson','Status','');
			$orders_list = array();
			for($o=0;$o<count($orders);$o++) {
				$query = "SELECT id,first_name,last_name FROM People WHERE id='".$orders[$o]->added_by_id."' AND type='1'";
				$res = $nb->sendAndGetOne($query);
				
				$orders_list[$o]['ID'] = $orders[$o]->id;
				$orders_list[$o]['Title'] = $orders[$o]->title;
				$add_date = new DateTime($orders[$o]->date_added);
				$orders_list[$o]['Date Added'] = date_format($add_date,'M d, Y');
				$orders_list[$o]['Order Number'] = $orders[$o]->order_number;
				$added_by = $orders[$o]->added_by_id;
				
				if($res) {
					$added_name = $res->first_name.' '.$res->last_name;
				} else {
					$added_name = "...";
				}
				$orders_list[$o]['Added By ID'] = $added_by;
				$orders_list[$o]['Added By'] = $added_name;
				$orders_list[$o]['Status'] = $orders[$o]->status;
				$orders_list[$o]['Edit'] = $orders[$o]->id;
			}
			
			$out .= '<table class="listTable">';
			$out .= "<tr>";
			foreach($columns AS $c) {
				$thisorder = strtolower(str_replace(' ','_',$c));
				$out .= '<td class="listHeader"><a href="#" class="subheaderLink" onclick="setContent(\'order_list\',{mode:\'redirect\',start:\''.$start.'\',limit:\''.$_SESSION['list_limit'].'&orderby='.$thisorder.'\'})">'.$c.'</a></td>';
			}
			$out .= "</tr>\r";
			
			for($p=0;$p<count($orders_list);$p++) {
				$out .= '<tr>';
				$id = $orders_list[$p]['ID'];
				foreach($orders_list[$p] as $e=>$v) {
					if($e == 'Status') {
						//echo $e.':'.$v.'<br />';
						if($v == -1) {
							$status = 'Unselected';
							$bgcol = 'FF9E9E';
						} elseif($v == 0) {
							$status = 'Inactive';
							$bgcol = 'FC2A2A';
						} elseif($v == 1) {
							$status = 'Data Entry';
							$bgcol = 'FFD59E';
						} elseif($v == 2) {
							$status = 'Editorial';
							$bgcol = 'F9FF63';
						} elseif($v == 3) {
							$status = 'Customer Review';
							$bgcol = '63D5FF';
						} elseif($v == 4) {
							$status = 'Approved';
							$bgcol = 'C6FF9E';
						} elseif($v == 5) {
							$status = 'Proofing';
							$bgcol = 'EA63FF';
						} elseif($v == 6) {
							$status = 'To Print';
							$bgcol = '999999';
						}
						$out .= '<td class="listItem" style="background-color: #'.$bgcol.'">'.$status.'</td>';
					}elseif($e == 'Edit') {
						if($status == 1) {
							$class = 'listEdit';
						} else {
							$class = 'listEditInactive';
						}
						// If you're not an admin...
						if($_SESSION['user']->level < 7) {
							// If you're a contractor...
							if($_SESSION['user']->level == 6) {
								// see if this order belongs to this user
								$query = 'SELECT id FROM Orders_Contractors WHERE contractor_id="'.$_SESSION['user']->id.'" AND order_id="'.$orders_list[$p]['ID'].'"';
								$res = $nb->sendAndGetOne($query);
								if($res) {
									$out .= '<td class="listEdit"><a href="order_edit.php?action=order_edit&id='.$id.'">Details</a></td>';
								} else {
									$out .= '&nbsp;';
								}
							// if you're not a contractor...
							} else if($_SESSION['user']->level == 1) {
								// see if this order belongs to this user
								if($_SESSION['user']->order_level < 3) {
									if($orders_list[$p]['Added By ID'] == $_SESSION['user']->id) {
										$out .= '<td class="listEdit"><a href="order_edit.php?action=order_edit&id='.$id.'">Details</a></td>';
									} else {
											$out .= '&nbsp;';
									}
								} else {
									$out .= '<td class="listEdit"><a href="order_edit.php?action=order_edit&id='.$id.'">Details</a></td>';
								}
							}
						} else {
							$out .= '<td class="listEdit"><a href="order_edit.php?action=order_edit&id='.$id.'">Details</a></td>';
						}
					} else {
						if($e != 'Added By ID') {
							$out .= '<td class="listItem">'.$v.'</td>';
						}
					}
				}
				$out .= '</tr>';
			}
		
			// Draw the close
			$out .= '</table>';
		}
		echo $out;
		break;
	case 'people_search':
		$nb = new BaseService();
		foreach($_POST AS $key=>$val) {
			$$key = $val;
		}
		if(isset($month)) {
			$converted_date = $year.'-'.$month.'-'.$day;
			$people_search_term = $converted_date;
		}
		if(isset($_POST['type'])) {
			if($_POST['type'] == 'uti') {
				$query = "SELECT People.*,Order_People.level AS order_level FROM People,Order_People WHERE ".$people_search_for;
			} else {
				$query = "SELECT People.* FROM People WHERE ".$people_search_for;
			}
		} else {
			$query = "SELECT People.* FROM People WHERE ".$people_search_for;
		}	 
		if($people_search_by == 'is') {
			$query .= "='".$people_search_term."'";
		} elseif($people_search_by == 'like') {
			$query .= " LIKE '%".$people_search_term."%'";
		} elseif($people_search_by == 'less') {
			$query .= "<'".$people_search_term."'";
		} elseif($people_search_by == 'more') {
			$query .= ">'".$people_search_term."'";
		} elseif($people_search_by == 'not') {
			$query .= " NOT LIKE '%".$people_search_term."%'";
		}
		if(isset($_POST['type'])) {
			if($_POST['type'] == 'uti') {
				$query .= " AND Order_People.order_id='".$_SESSION['order_id']."' AND People.id=Order_People.person_id";
			}
		}
		$query .= ' ORDER BY id ASC';
		$people = $nb->sendAndGetMany($query);
		if(!$people) {
			echo '<div style="margin-top: 5px; font-weight: bold;">There are no results to display.</div>';
		} else {
			$columns = array('ID','Last Name','Email','Level','Status','');
			$people_list = array();
			for($o=0;$o<count($people);$o++) {
				$people_list[$o]['ID'] = $people[$o]->id;
				$people_list[$o]['Last Name'] = $people[$o]->first_name.' '.$people[$o]->last_name;
				$people_list[$o]['Email'] = $people[$o]->email;
				if($people[$o]->order_level) {
					$people_list[$o]['Level'] = $people[$o]->order_level;
				} else {
					 $people_list[$o]['Level'] = $people[$o]->level;
				}
				$people_list[$o]['Status'] = $people[$o]->status;
				$people_list[$o]['Edit'] = $people[$o]->id;
			}
			$out .= '<table class="listTable" cellpadding="0" cellspacing="0">';
			$out .= "<tr>";
			foreach($columns AS $c) {
				$thisperson = strtolower(str_replace(' ','_',$c));
				$out .= '<td class="listHeader"><a href="#" class="subheaderLink" onclick="setContent(\'people_list\',{mode:\'redirect\',start:\''.$start.'\',limit:\''.$_SESSION['list_limit'].'&orderby='.$thisperson.'\'})">'.$c.'</a></td>';
			}
			$out .= "</tr>\r";
			
			for($p=0;$p<count($people_list);$p++) {
				$out .= '<tr>';
				$id = $people_list[$p]['ID'];
				foreach($people_list[$p] as $e=>$v) {
					if($e == 'Status') {
						//echo $e.':'.$v.'<br />';
						if($v == '') {
							$status = 'Unselected';
							$bgcol = 'FF9E9E';
						} elseif($v == 0) {
							$status = 'Inactive';
							$bgcol = 'FC2A2A';
						} elseif($v == 1) {
							$status = 'Active';
							$bgcol = 'FFD59E';
						}
						$out .= '<td class="listItem" style="background-color: #'.$bgcol.'">'.$status.'</td>';
					}elseif($e == 'Edit') {
						$out .= '<td class="listItem"><a href="#" onclick="setContent(\'people_edit\',{mode:\'redirect\',action: \''.$_POST['return'].'\',id:\''.$people[$p]->id.'\'})" class="listEdit">Edit</a></td>';
					} else {
						$out .= '<td class="listItem">'.$v.'</td>';
					}
				}
				$out .= '</tr>';
			}
		
			// Draw the close
			$out .= '</table>';
		}
		echo $out;
			break;
		case 'organization':
			$type = $_GET['type'];
			$nb = new BaseService();
			$orgnames = $nb->sendAndGetMany('SELECT id,name FROM Organizations WHERE type="'.$type.'"');
			$out = '';
			foreach($orgnames AS $o) {
				$out .= '<option value="'.$o->id.'">'.$o->name.'</option>';
			}
			echo $out;
			break;
}

function isUser($login,$password) {
	$np = new People();
	$query = "SELECT id FROM People WHERE login='".$login."' AND password='".$password."'";
	$res = $np->sendAndGetOne($query);
	return($res);
}

function updatePerson()
{
	$person = $_POST;
	$person['organization_id'] = $_POST['organization'];
	$np = new People();

	$res = $np->sendAndGetOne('SELECT login,password FROM People WHERE id="'.$_POST['id'].'"');
	$old_login = $res->login;
	$old_pass = $res->password;
	if($_POST['username'] != $old_login || $_POST['password'] != $old_pass) {
		$used = isUser($_POST['username'],$_POST['password']);
		if($used) {
			return('There is already a user with that username and password. Please select another login and try again.');
		} else {
			$person = $np->updatePerson($person);
			return($person);
		}
	} else {
		$person = $np->updatePerson($person);
		return($person);
	}
}

function createPerson()
{
	$person = $_POST;
	
	$organization = $person['organization'];
	$other_organization = $person['other_organization'];
	
	$organization_type = $person['organization_type'];
	$other_organization_type = $person['other_type'];
	
	// check to see if that name already exists
	if($other_organization != '') {
		$neworg = new OrganizationsService();
		$result = $neworg->getOrganizationsByName($other_organization);
		if(count($result) == 0) {
			// if it doesn't exist, add	 it --->
			//name,type,added_by_type,added_by_id,date_modified,status
			$org_item = array();
			$org_item['name'] = $other_organization;
			if($other_organization_type != '') {
				$org_item['type'] = $other_organization_type;
			} else {
				$org_item['type'] = $organization_type;
			}
			$org_item['added_by_type'] = $person['added_by_type'];
			$org_item['added_by_id'] = $person['added_by_id'];
			$org_item['date_modified'] = $person['date_modified'];
			$org_item['status'] = '1';
			$org_id = $neworg->createOrganizations($org_item);
			$person['organization_id'] = $org_id;
		} else {
			$person['organization_id'] = $person['organization'];
		}
	} else {
		$person['organization_id'] = $person['organization'];
	}
	
	$np = new People();
	$res = $np->sendAndGetOne("SELECT id FROM People WHERE first_name='".$_POST['first_name']."' AND last_name='".$_POST['last_name']."' AND email='".$_POST['email']."'");
	$p_id = $res->id;
	if(!$p_id) { // this person is not in the system yet…
		// let's see if the username and password are unique…
		$used = isUser($_POST['username'],$_POST['password']);
		if($used) {
			return('There is already a user with that username and password. Please select another login and try again.');
		} else {
			return( $newPeople->addPerson($person) );
		}
	} else {
		return("A user with that name and email already exists.");
	}	
	
}

sleep(1);
?>