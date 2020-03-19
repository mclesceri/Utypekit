<?php
define('FORCE_HTTPS', true);

session_start();

if(!$_SESSION['login'] == true) {
	header('Location: index.php');
}

//ini_set('display_errors',1);
//error_reporting(-1);

$page = 'people_edit';

require_once('src/globals.php');

require_once(INCLUDES.'Elements.php');
require_once(INCLUDES."Warnings.php");
require_once(INCLUDES."States.php");
require_once(SERVICES.'People.php');

$nf = new Elements();


$out = '';
$person_id = '';
$first_name = '';
$last_name = '';
$order_level = '';
$email = '';
$login = '';
$phone = '';
$cell_phone = '';
$password = '';
$address1 = '';
$address2 = '';
$city = '';
$state = '';
$zip = '';
$title = '';
$user_status = '';
$organization_id = '';


if(!isset($_SESSION['order_id'])) {
	$out = 'You must first select an order. Please go to the <a href="order_list.php">Order List</a> page and choose which order you want to work with.';
} else {

	$demo = false;
	if($_SESSION['order_id'] == '1') {
	    $demo = true;
	}
	
	
	
	$levels = array('2'=>'Contributor','4'=>'Cochairperson','5'=>'Chairperson');
	$statuses = array('0'=>'Inactive','1'=>'Active');
	$mod_date = date('Y-m-d H:i:s');
	
	$action = 'user_edit';
	if(isset($_REQUEST['action'])) {
		$action = $_REQUEST['action'];
	}
	$person_id;
	if(isset($_REQUEST['id'])) {
		$person_id = $_REQUEST['id'];
	}
	$order_id;
	if(isset($_SESSION['order_id'])) {
		$order_id = $_SESSION['order_id'];
	}
	
	$form_action = '';
	$level_list = '';
	$status_list = '';
	$level = '';
	$feedback = 'Fields Marked with <span style="color: #FF0000">*</span> below are required.';
	$disabled = false;
	$existing = false;
	
	class EditPerson extends People
	{
		function getOrganization($order_id) {
			 $query = "SELECT Organizations.id,Organizations.type,Organizations.name FROM Organizations,Order_Organizations WHERE Order_Organizations.order_id='".$order_id."' AND Organizations.id=Order_Organizations.organization_id";
			 $org = $this->sendAndGetOne($query);
			 return($org);
		}
		
		function updateUser($item)
		{
			$person = $this->makeUser($item);
			$query = "UPDATE People SET ";
			foreach($person AS $key=>$val) {
				if($key != 'order_level') {
					if($key != 'status') {
						if($val) {
							$query .= $key."='".$val."',";
						} else {
							$query .= $key."='0',";
						}
					}
				}
			}
			$query = substr($query, 0, -1);
			$query .= " WHERE id='".$item['id']."'";
			$this->sendAndGetOne($query);
			$query = "UPDATE Order_People SET level='".$item['order_level']."',status='".$item['status']."' WHERE order_id='".$_SESSION['order_id']."' AND person_id='".$item['id']."'";
			$this->sendAndGetOne($query);
			$person = $this->getOrderPerson($item['id'],$_SESSION['order_id']);
			return($person);
		}
		
		function addOrderUser($item,$order_id) {
			$query = "INSERT INTO Order_People (date_modified,order_id,person_id,level,added_by_type,added_by_id,status) VALUES
				(
				'".date('Y-m-d H:i:s')."',
				'".$order_id."',
				'".$item['id']."',
				'".$item['order_level']."',
				'1',
				'".$_SESSION['user']->id."',
				'1'
				)";
			$res = $this->insertAndGetOne($query);
			return('{"status":"true","id":"'.$item['id'].'","message":"The user has been added to the order."}');
		}
		
		function testUser($item) {
			$out = '{"status":"true"}';
			$query = "SELECT id FROM People WHERE first_name='".urlencode($item['first_name'])."' AND last_name='".urlencode($item['last_name'])."' AND email='".urlencode($item['email'])."' OR email='".$item['email']."'";
			$res = $this->sendAndGetOne($query);
			if(isset($res->id)) {
				$out = '{"status":"false","id":"'.$res->id.'"}';
			}
			return($out);
		}
		
		function testOrderUser($id,$order_id) {
			$out = '';
			$res = $this->sendAndGetOne("SELECT id FROM Order_People WHERE person_id='".$id."' AND order_id='".$order_id."'");
			if(isset($res->id)) {
				$out = '{"status":"false","id":"'.$id.'"}';
			} else {
				$out = '{"status":"true","id":"'.$id.'"}';
			}
			return($out);
		}
		
		function testLogin($new,$old=null) {
			$checkit = 'false';
			$out = '{"status":"true"}';
			
			if($old) {
				if(urlencode($new->login) == $old->login && urlencode($new->password) == $old->password) {
					$checkit = 'false';
				} else {
					$checkit = 'true';
				}
				if($new->login == $old->login && $new->password == $old->password) {
					$checkit = 'false';
				} else {
					$checkit = 'true';
				}
			}
			if($checkit == 'true') {
				$res = $this->getPersonByUsernamePassword(urlencode($new->login),urlencode($new->password));
				if(!$res) {
					$res = $this->getPersonByUsernamePassword($new->login,$new->password);
				}
			}

			if($res) {
				if($res->id != $old->id) {
				$out = '{"status":"false","message":"There is already a user with that username and password. Please select another login and try again."}';
				}
			}
			return($out);
		}
		
		function addUser($item) {
			$person = $this->makeUser($item);
			
			$query = "INSERT INTO People (";
			foreach($person AS $key=>$val) {
				if($key != 'order_level') {
					if($key != 'status') {
						$query .= $key.',';
					} else {
						$query .= 'status,';
					}
				}
			}
			$query = substr($query,0,-1);
			$query .= ") VALUES (";
			foreach($person AS $key=>$val) {
				if($key != 'order_level') {
					if($key != 'status') {
						if($val) {
							$query .= "'".$val."',";
						} else {
							$query .= "'0',";
						}
					} else {
						$query .= "'1',";
					}
				}
			}
			$query = substr($query,0,-1);
			$query .= ")";
			$new_id = $this->insertAndGetOne($query);
			$query = "INSERT INTO Order_People (
				date_modified,
				order_id,
				person_id,
				level,
				added_by_type,
				added_by_id,
				status
				) VALUES (
				'".date('Y-m-d H:i:s')."',
				'".$_SESSION['order_id']."',
				'".$new_id."',
				'".$item['order_level']."',
				'1',
				'".$item['added_by_id']."',
				'".$item['status']."'
			)";
			$oip = $this->insertAndGetOne($query);
			$out = '{"status":"true","id":"'.$new_id.'","message":"The user has been added."}';
			return($out);
		}
		
		protected function makeUser($data) {
			$person = new stdClass();
			$allPossible = array(
				'organization_id',
				'first_name',
				'last_name',
				'email',
				'phone',
				'cell_phone',
				'address1',
				'address2',
				'city',
				'state',
				'zip',
				'login',
				'password',
				'order_level'
			);
			foreach($allPossible AS $a) {
				if($data[$a]) {
					$person->{$a} = urlencode($data[$a]);
				} else {
					$person->{$a} = '0';
				}
			}
			$person->level = '1';
			if($added_by_type == '2') {
				if(isset($data['level'])) {
					$person->level = $data['level'];
				}
			}
			$person->type = '1';
			$newsletter = 'newsletter:no';
			if($data['newsletter']) {
				$newsletter = 'newsletter:yes';
			}
			$person->meta = $newsletter;
			$person->added_by_type = $data['added_by_type'];
			if(isset($data['added_by_id'])) {
				$person->added_by_id = $data['added_by_id'];
			}
			$person->date_modified = date('Y-m-d H:i:s');
			$person->status = '1';
			
			return($person);
		}/**/
	}
	
	$nep = new EditPerson();
	switch( $action ) {
		case 'save_new':
			$form_action = $action;
			// Find out if the user being added already exists in the database...
			$res = $nep->testUser($_POST);
			$res = json_decode($res);
			if($res->status == 'true') { // if not...
				// Make sure the login and password are unique...
				$new = new stdClass();
					$new->login = $_POST['login'];
					$new->password = $_POST['password'];
				$subres = $nep->testLogin($new);
				$subres = json_decode($subres);
				// If they are, then add the user...
				if($subres->status == 'true') {
					$user = $nep->addUser($_POST);
					$user = json_decode($user);
					if($user->status = true) {
						/*$form_action = 'update';
						$feedback = $user->message;
						$person = $nep->getOrderPerson($user->id,$order_id);
						// parse out the variables for this person
						foreach($person AS $key=>$val) {
							$$key = htmlspecialchars(stripslashes(urldecode($val)),ENT_QUOTES,'utf-8',false);
						}*/
						header('location:'.$_SERVER['PHP_SELF'].'?action=user_edit&id='.$user->id);
					}
				} else { // otherwise, warn the user...
					$feedback = $subres->message;
				}
			} else {
				$subres = $nep->testOrderUser($res->id,$order_id);
				$subres = json_decode($subres);
				if($subres->status == 'true') {
					$person_id = $subres->id;
					$feedback = 'There is already a user matching your entries in the database. To assign this user to the currently selected order, choose a Level, then click "Save" again. To enter a different user, click "Cancel" and enter new information.';
					$form_action = 'save_existing';
					$existing = true;
					$person = $nep->getPerson($person_id);
					// parse out the variables for this person
					foreach($person AS $key=>$val) {
						if($val != '0') {
							$$key = htmlspecialchars(stripslashes(urldecode($val)),ENT_QUOTES,'utf-8',false);
						}
					}
				} else {
					$person_id = $subres->id;
					$form_action = 'update';
					$feedback = 'That user is already assigned to this order.';
					$person = $nep->getOrderPerson($person_id,$order_id);
					// parse out the variables for this person
					foreach($person AS $key=>$val) {
						if($val != '0') {
							$$key = htmlspecialchars(stripslashes(urldecode($val)),ENT_QUOTES,'utf-8',false);
						}
					}
				}
			}
			break;
		case 'save_existing':
			$res = $nep->addOrderUser($_POST,$order_id);
			$res = json_decode($res);
			if($res->status == true) {
				$feedback = $res->message;
				$person = $nep->getOrderPerson($res->id,$order_id);
				// parse out the variables for this person
				foreach($person AS $key=>$val) {
					if($val) {
						if($val != '0') {
							$$key = htmlspecialchars(stripslashes(urldecode($val)),ENT_QUOTES,'utf-8',false);
						}
					}
				}
				$form_action = 'update';
			}
			break;
		case 'update':
			$form_action = $action;
			$query = "SELECT id,login,password FROM People WHERE id='".$_POST['id']."'";
			$np = new People();
			$old = $np->sendAndGetOne($query);
			$new = new stdClass();
				$new->id = $_POST['id'];
				$new->login = $_POST['login'];
				$new->password = $_POST['password'];
			$res = $nep->testLogin($new,$old);		
			$res = json_decode($res);
			if($res->status == 'true') {
				$person = $nep->updateUser($_POST);
				//print_r($person);
				// parse out the variables for this person
				foreach($person AS $key=>$val) {
					if($val) {
						if($val != '0') {
							$$key = htmlspecialchars(stripslashes(urldecode($val)),ENT_QUOTES,'utf-8',false);
						}
					}
				}
			} else {
				$person = $nep->getOrderPerson($_POST['id'],$order_id);
				//print_r($person);
				// parse out the variables for this person
				foreach($person AS $key=>$val) {
					if($val != '0') {
						$$key = htmlspecialchars(stripslashes(urldecode($val)),ENT_QUOTES,'utf-8',false);
					}
				}
				$feedback = $res->message;
			}
			$form_action = 'update';
			break;
		case 'user_add':
	        $organization = $nep->getOrganization($order_id);
	        $form_action = 'save_new';
			break;
		case 'user_edit':
			// get form values
			$person = $nep->getOrderPerson($person_id,$order_id);
			// parse out the variables for this person
			foreach($person AS $key=>$val) {
				if($val) {
					if($val != '0') {
						$$key = htmlspecialchars(stripslashes(urldecode($val)),ENT_QUOTES,'utf-8',false);
					}
				}
			}
			$form_action = 'update';
			break;
	}
	
	$script = "
	        function sendMe(){
	            var req  = 'first_name,last_name,level,email,login,password';
	            var pass = formVerify(req);
	            if(pass.status == false) {
	                $('people_edit').submit();
	            } else {
	                $('feedback').update(pass.message);
	            }
	        }
	        
	        document.observe('dom:loaded',function() {
	            fancyNav();
	        });
	        ";
	$out .= "
	<form id='people_edit' name='people_edit' action='".$_SERVER['PHP_SELF']."' method='POST' onsubmit=\"sendMe(); return false;\">
	<input type='hidden' name='action' value='".$form_action."'>";
	if($person_id) {
		$out .= "
	<input type='hidden' name='id' value='".$person_id."'>";
	}
	if($action == 'user_add') {
	$out .= "
	<input type='hidden' name='added_by_type' value='1'>
	<input type='hidden' name='added_by_id' value='".$_SESSION['user']->id."'>";
	}
	$out .= "
	<input type='hidden' name='date_modified' value='".$mod_date."'>
	<table width='100%' border='0' cellspacing='0' cellpadding='4'>";
	if($action == 'user_edit') {
		$out .= "
		<tr>
			<td class='formLabel'>User ID:</td>
			<td class='formInput'>".$id."</td>
			<td class='formLabel'>User Since:</td>";
			//$d = explode('-', substr($date_added,0,10));
			$display_date_added = date('d M Y', strtotime($date_added));//date("M d, Y",mktime(0, 0, 0,$d[1], $d[2], $d[0]));
			$display_date_modified = date('d M Y', strtotime($date_modified));
			$out .= "
			<td class='formInput'>".$display_date_added."</td>
		</tr>
		<tr>
			<td class='formLabel'>Last Modified:</td>";
			$out .= "<td class='formInput'>".$display_date_modified."</td>
		</tr>";
	}
	$out .= '
	    <tr>
			<td class="formLabel"><span style="color: #FF0000">*</span>First Name/Last Name:</td>
			<td class="formInput"><input name="first_name" type="text" id="first_name" tabindex="3" size="13" value="'.$first_name.'" />
			<input name="last_name" type="text" id="last_name" tabindex="4" size="13" value="'.$last_name.'" /></td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
		</tr>';
	    if($_SESSION['user']->order_level > 2) {
	    	$out .= '
		<tr>
		   <td class="formLabel"><span style="color: #FF0000">*</span>Level:</td>
		   <td class="formInput">';
		       $out .= '<select name="order_level" id="order_level" tabindex="5">
		           <option value="">Choose one...</option>';
	               foreach($levels AS $key=>$val) {
						$selected = '';
						if($order_level == $key) {
							$selected = ' selected="selected"';
						}
						if($_SESSION['user']->order_level >= $key) {
							$out .= '<option value="'.$key.'"'.$selected.'>'.$val.'</option>';
						}
					}
	            $out .= '
	            </select>
	        </td>
	    </tr>
	    <tr>
	        <td class="formLabel">Status:</td>
	        <td class="formInput">
	            <select name="status" id="status" tabindex="6">
	                <option value=""> Choose one...</option>';
	                foreach ($statuses as $key => $val) {
	                    $selected = '';
	                    if($action != 'user_add') {
	                        if($user_status == $key) {
	                            $selected = ' selected="selected"';
	                        }
	                    } else {
	                        if($key == '1') {
	                            $selected = ' selected="selected"';
	                        }
	                    }
	                    
	                    $out .= '
	                <option value="'.$key.'"'.$selected.'>'.$val.'</option>';
	                }
	                $out .= '
	            </select>
	        </td>
	    </tr>';
	    } else {
		    $out .= '
		    <input type="hidden" name="status" value="1" />
		    <input type="hidden" name="order_level" value="'.$_SESSION['user']->order_level.'" />';
	    }
	    $out .= "
		<tr>
			<td class='formLabel'><span style='color: #FF0000'>*</span>Email:</td>
			<td class='formInput'><input type='text' name='email' id='email' tabindex='6' size='30' value='".$email."' /></td>";
			if($_SESSION['user']->order_level >= $order_level) {
	    		if($_SESSION['user']->order_level > 2) {
	    		$out .= '
	    	<td class="formLabel"><span style="color: #FF0000">*</span>Username:</td>
			<td class="formInput"><input name="login" type="text" id="login" tabindex="13" size="30" value="'.$login.'" maxlength="15" /></td>';
				} else {
					$out .= '
	        <td class="formLabel">&nbsp;</td><input name="login" type="hidden" id="login" value="'.$login.'" /><td class="formInput">&nbsp;</td>';
				}
			} else {
				$out .= "
	        <td class='formLabel'>&nbsp;</td><td class='formInput'>&nbsp;</td>";
	        }
		$out .= '</tr>
		<tr>
			<td class="formLabel">Phone:</td>
			<td class="formInput"><input name="phone" type="text" id="phone" tabindex="7" size="30" value="'.$phone.'" /></td>';
		if($_SESSION['user']->order_level > 2) {
			$out .= '
	        <td class="formLabel"><span style="color: #FF0000">*</span>Password:</td>
			<td class="formInput"><input name="password" type="password" id="password" tabindex="14" size="30" value="'.$password.'" maxlength="15" /></td>';
		} else {
			$out .= '
	        <td class="formLabel">&nbsp;</td><td class="formInput"><input name="password" type="hidden" id="password" value="'.$password.'" /></td>';
		}
		$out .= "</tr>
		<tr>
			<td class='formLabel'>Address 1:</td>
			<td class='formInput'><input name='address1' type='text' id='address1' tabindex='8' size='30' value='".$address1."' /></td>";
			if($_SESSION['user']->order_level > 2) {
				$out .= '
			<td colspan="2" style="text-align: center; font-size: .9em; color: #333333;">NOTE: Username and password are limited to 15 characters each.</td>';
			} else {
				$out .= '
			<td colspan="2" style="text-align: center; font-size: .9em; color: #333333;"></td>';
			}
			$out .= "
		</tr>
		<tr>
			<td class='formLabel'>Address 2:</td>
			<td class='formInput'><input name='address2' type='text' id='address2' tabindex='9' size='30' value='".$address2."' /></td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
		</tr>
		<tr>
			<td class='formLabel'>City/State/Zip:</td>
			<td colspan='3' class='formInput'><input name='city' type='text' id='city' tabindex='10' size='18' value='".$city."' />
				<select name='state' id='state' tabindex='11'>\n";
				foreach($states AS $key=>$val) {
					$selected = "";
					if($state == $key) {
						$selected = " selected=\"selected\"";
					}
					$out .= "<option value=\"".$key."\"".$selected.">".$val."</option>\n";
				}
				$out .= "</select>\n
			<input name='zip' type='text' id='zip' tabindex='12' size='7' value='".$zip."' /></td>
			<td>&nbsp;</td>
		</tr>
	</table>
	</form>";

	$header_left = '';
	$header_middle = $_SESSION['order_title']." Users";
	$header_right = '';
	
	require_once(TEMPLATES.'u_people_header.tpl');
	require_once(TEMPLATES.'u_people_footer.tpl');
}
$content = $out;

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

include(TEMPLATES.'main.tpl');
?>