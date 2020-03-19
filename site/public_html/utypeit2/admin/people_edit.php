<?php

session_start();

if(!$_SESSION['login'] == true) {
	header('Location: index.php');
}

$page = 'people_edit';

require_once('../src/globals.php');

require_once(INCLUDES.'Elements.php');
require_once(SERVICES.'People.php');

$states = array(
				'0'=>' -- ',
				'AL'=>"Alabama",
                'AK'=>"Alaska", 
                'AZ'=>"Arizona", 
                'AR'=>"Arkansas", 
                'CA'=>"California", 
                'CO'=>"Colorado", 
                'CT'=>"Connecticut", 
                'DE'=>"Delaware", 
                'DC'=>"District Of Columbia", 
                'FL'=>"Florida", 
                'GA'=>"Georgia", 
                'HI'=>"Hawaii", 
                'ID'=>"Idaho", 
                'IL'=>"Illinois", 
                'IN'=>"Indiana", 
                'IA'=>"Iowa", 
                'KS'=>"Kansas", 
                'KY'=>"Kentucky", 
                'LA'=>"Louisiana", 
                'ME'=>"Maine", 
                'MD'=>"Maryland", 
                'MA'=>"Massachusetts", 
                'MI'=>"Michigan", 
                'MN'=>"Minnesota", 
                'MS'=>"Mississippi", 
                'MO'=>"Missouri", 
                'MT'=>"Montana",
                'NE'=>"Nebraska",
                'NV'=>"Nevada",
                'NH'=>"New Hampshire",
                'NJ'=>"New Jersey",
                'NM'=>"New Mexico",
                'NY'=>"New York",
                'NC'=>"North Carolina",
                'ND'=>"North Dakota",
                'OH'=>"Ohio", 
                'OK'=>"Oklahoma", 
                'OR'=>"Oregon", 
                'PA'=>"Pennsylvania", 
                'RI'=>"Rhode Island", 
                'SC'=>"South Carolina", 
                'SD'=>"South Dakota",
                'TN'=>"Tennessee", 
                'TX'=>"Texas", 
                'UT'=>"Utah", 
                'VT'=>"Vermont", 
                'VA'=>"Virginia", 
                'WA'=>"Washington", 
                'WV'=>"West Virginia", 
                'WI'=>"Wisconsin", 
                'WY'=>"Wyoming");

$levels = array('0'=>' -- ','1'=>'Demo Account','2'=>'Contributor','3'=>'Committee Member','4'=>'Cochairperson','5'=>'Chairperson','6'=>'Contractor','7'=>'Customer Service','8'=>'Administrator','9'=>'Super User');
$statuses = array('0'=>'Inactive','1'=>'Active');

function getPerson($this_id) {
	$newperson = new People();
	$thisperson = $newperson->getPerson($this_id);
	return($thisperson);
}

$mod_date = date('Y-m-d H:i:s');

$action = $_REQUEST['action'];
$person_id = $_REQUEST['id'];

$form_action = '';
$level_list = '';
$status_list = '';
$level = '';
$feedback = '';

$disabled = false;

$header_left = '&nbsp;';

$np = new People();
$nf = new Elements();


function isUser($login,$password) {
	$np = new People();
	$query = "SELECT id FROM People WHERE login='".urlencode($login)."' AND password='".urlencode($password)."'";
	$res = $np->sendAndGetOne($query);
	if(!$res) {
		$query = "SELECT id FROM People WHERE login='".$login."' AND password='".$password."'";
		$res = $np->sendAndGetOne($query);
	}
	return($res);
}

function updatePerson($item)
{
   unset($item['action']);
   unset($item['case']);
   unset($item['organization_type']);
	$person = $item;
	$person['organization_id'] = $item['organization'];
	unset($person['organization']);
	$np = new People();
	$query = 'SELECT login, password FROM People WHERE id="'.$person['id'].'"';
	$res = $np->sendAndGetOne($query);
		$old_login = $res->login;
		$old_pass = $res->password;
	if($person['login'] != $old_login) {
		if($person['password'] != $old_pass) {
			$used = isUser($person['login'],$person['password']);
			if($used) {
				return('There is already a user with that username and password. Please use the Back button on your browser to select another login and try again.');
			} else {
				foreach($person AS $p=>$v) {
					$v = urlencode(stripslashes(html_entity_decode($v)));
				}
				$person = $np->updatePerson($person);
				return(intval($person));
			}
		} else {
			foreach($person AS $p=>$v) {
				$v = urlencode(stripslashes(html_entity_decode($v)));
			}
			$res = $np->updatePerson($person);
			return(intval($res));
		}
	} else {
		foreach($person AS $p=>$v) {
			$v = urlencode(stripslashes(html_entity_decode($v)));
		}
		$res = $np->updatePerson($person);
		return(intval($res));
	}
}

function createPerson($item) {
	$person = $item;
	unset($person['action']);
    unset($person['case']);
    
    $organization_id = null;
    if($person['organization']) {
    	$organization_id = $person['organization'];
    	unset($person['organization']);
    }
    if($person['organization_id']) {
    	$organization_id = $person['organization_id'];
    	unset($person['organization_id']);
    }
    
    $other_organization = null;
    if($person['other_organization']) {
    	$other_organization = $person['other_organization'];
    	
    }
    unset($person['other_organization']);
    
    $organization_type = null;
    if($person['organization_type']) {
    	$organization_type = $person['organization_type'];
    }
    unset($person['organization_type']);
    
    $other_organization_type = null;
    if($person['other_type']) {
	   $other_organization_type = $person['other_type'];
    }
    unset($person['other_type']);
	
	// check to see if that name already exists
	if($other_organization) {
		$neworg = new People();
		$result = $neworg->sendAndGetOne("SELECT id FROM Organizations WHERE name=\"".$other_organization."\"");
		if(!$result) {
			// if it doesn't exist, add  it --->
            $mod_date = date('Y-m-d H:i:s');
			$org_id = $neworg->insertAndGetOne('INSERT INTO Organizations (name,type,added_by_type,added_by_id,date_modified,status) VALUES ("'.$other_organization.'","'.$organization_type.'","'.$person['added_by_type'].'","'.$person['added_by_id'].'","'.$mod_date.'"," 1",)');
			$person['organization_id'] = $org_id;
		} else {
			$person['organization_id'] = $result->id;
		}
	} else {
		$person['organization_id'] = $organization_id;
	}
    
	$np = new People();
	$res = $np->sendAndGetOne("SELECT id FROM People WHERE first_name='".urlencode($_POST['first_name'])."' AND last_name='".urlencode($_POST['last_name'])."' AND email='".urlencode($_POST['email'])."' OR email='".$_POST['email']."'");
	$p_id = $res->id;
	if(!$p_id) { // this person is not in the system yet…
		// let's see if the username and password are unique…
		$used = isUser($person['login'],$person['password']);
		if($used) {
			return('There is already a user with that username and password. Please use the Back button on your browser to select another login and try again.');
		} else {
			return( intval($np->addPerson($person)) );
		}
	} else {
		return("A user with that name and email already exists.");
	}
	
}

$user_type = null;

switch( $action ) {
	case 'save':
		$handler = $_POST['case'];
		switch($handler) {
			case 'customers_add':
				$title = 'ADDING CUSTOMER';
				$tab = 1;
				$res = createPerson($_POST);//$np->addPerson($_POST);
				if(is_int($res)) {
					header('Location: people_edit.php?action=customers_edit&id='.$res);
				} else {
					$feedback = $res;//"There has been an error processing the request. Please check your values and try again. If this problem persists, please contact technical support.";	
				}
                $user_type = 'customers';
				break;
			case 'customers_edit':
				$tab = 1;
				$title = 'UPDATING CUSTOMER';
				$res = updatePerson($_POST);
				if(is_int($res)) {
					header('Location: people_edit.php?action=customers_edit&id='.$res);
				} else {
					$feedback = $res;//"There has been an error processing the request. Please check your values and try again. If this problem persists, please contact technical support.";	
				}
                $user_type = 'customers';
				break;
			case 'users_add':
				$tab = 2;
				$title = 'ADDING USER';
				$res = createPerson($_POST);//$np->addPerson($_POST);
				if(is_int($res)) {
					header('Location: people_edit.php?action=users_edit&id='.$res);
				} else {
					$feedback = $res;//"There has been an error processing the request. Please check your values and try again. If this problem persists, please contact technical support.";	
				}
				$user_type = 'users';
				break;
			case 'users_edit':
				$tab = 2;
				$title = 'UPDATING USER';
				$res = updatePerson($_POST);//$np->updatePerson($_POST);
				if(is_int($res)) {
					header('Location: people_edit.php?action=users_edit&id='.$res);
				} else {
					$feedback = $res;//"There has been an error processing the request. Please check your values and try again. If this problem persists, please contact technical support.";	
				}
				$user_type = 'users';
				break;
			case 'contractors_add':
				$tab = 3;
				$title = 'ADDING CONTRACTOR';
				$res = createPerson($_POST);//$np->addPerson($_POST);
				if(is_int($res)) {
					header('Location: people_edit.php?action=contractors_edit&id='.$res);
				} else {
					$feedback = $res;//"There has been an error processing the request. Please check your values and try again. If this problem persists, please contact technical support.";	
				}
				$user_type = 'contractors';
				break;
			case 'contractors_edit':
				$tab = 3;
				$title = 'UPDATING CONTRACTOR';
				$res = updatePerson($_POST);//$np->updatePerson($_POST);
				if(is_int($res)) {
					header('Location: people_edit.php?action=contractors_edit&id='.$res);
				} else {
					$feedback = $res;//"There has been an error processing the request. Please check your values and try again. If this problem persists, please contact technical support.";	
				}
				$user_type = 'contractors';
				break;
		}
		break;
	case 'customers_add':
		$title = "ADD CUSTOMER";
		$tab = 1;
		$header_right = '<div class="headerLink"><a href="people_list.php?action=customers">Customer List</a></div>';
		$status = "Active";
		$type = '1';
        $user_type = 'customers';
		break;
	case 'customers_edit':
		$tab = 1;
		$title = "EDIT CUSTOMER";
		$header_right = '<div class="headerLink"><a href="people_list.php?action=customers">Customer List</a></div>';
		// get form values
		$person = getPerson($person_id);
		foreach($person AS $key=>$val) {
			if($val != '0') {
				$$key = $val;
			}
		}
		$type = '1';
        $user_type = 'customers';
		break;
	case 'users_add':
		$tab = 2;
		$title = "ADD USER";
		$header_right = '<div class="headerLink"><a href="people_list.php?action=users">User List</a></div>';
		$type = '2';
		$user_type = 'users';
		break;
	case 'users_edit':
		$tab = 2;
		$title = "EDIT USER";
		$header_right = '<div class="headerLink"><a href="people_list.php?action=users">User List</a></div>';
		// get form values
		$person = getPerson($person_id);
		// parse out the variables for this person
		foreach($person AS $key=>$val) {
			if($val != '0') {
				$$key = $val;
			}
		}
		$type = '2';
		$user_type = 'users';
		if($_SESSION['user']->level < $level) {
			$disabled = true;
		}
		break;
	case 'contractors_add':
		$tab = 3;
		$title = "ADD CONTRACTOR";
		$header_right = '<div class="headerLink"><a href="people_list.php?action=contractors">Contractor List</a></div>';
		$status = "Active";
		$type = '2';
        $user_type = 'contractors';
		break;
	case 'contractors_edit':
		$tab = 3;
		$title = "EDIT CONTRACTOR";
		$header_right = '<div class="headerLink"><a href="people_list.php?action=contractors">Contractor List</a></div>';
		// get form values
		$person = getPerson($person_id);
		foreach($person AS $key=>$val) {
			if($val != '0') {
				$$key = $val;
			}
		}
		$type = '2';
        $user_type = 'contractors';
		break;
}

if($action == 'customers_add' || $action == 'customers_edit') {
$script .= "
	function setOrganizationsType(thetype) {
		if(thetype != 'Other') {
			$('organization').removeClassName('disabled').addClassName('enabled').enable();";
			if($action == 'customers_add') {
				$script .= "
			$('other_type').removeClassName('enabled').addClassName('disabled').enable();
			$('other_organization').removeClassName('enabled').addClassName('disabled').enable();";
			}
			$script .= "var url = window.includes + 'process_form.php';
			$('organization').update('');
			var b = new Ajax.Request(url,{
				method:'get',
				parameters: {action:'organization',type:thetype},
				 onFailure: function(transport) {
					 alert(transport.responseStatus);
				 },
				 onLoading: function() {
					 $('feedback').insert('<img src=\"' + window.images + 'ajax-loader.gif\">');
				 },
				 onSuccess: function (transport){
					 $('organization').update(transport.responseText);
					 $('feedback').select('img')[0].remove();
				 } 
			 });
		} else {";
			if($action == 'customers_add') {
				$script .= "
			$('other_type').removeClassName('disabled').addClassName('enabled').enable();
			$('organization').removeClassName('enabled').addClassName('disabled').disable();
			$('other_organization').removeClassName('disabled').addClassName('enabled').enable();
			$('other_type').value = '';";
			}
			$script .= "$('organization').selectedIndex = 0;
			
		}
	}
	
	function setOther(selection) {
		if(selection == 'Other') {
			$('organization').removeClassName('enabled').addClassName('disabled').disable();
			$('other_organization').removeClassName('disabled').addClassName('enabled').enable();
		}
	}";
}			
$script .= "
	document.observe('dom:loaded', function() {
				
		showSet(currentSet);
		$$('ul.sublist')[currentSet].select('li').each(function(ea){ if(ea.hasClassName('inactive')) { ea.removeClassName('inactive') }; });";
		
		if($action == 'customers_add' || $action == 'customers_edit') {
			$script .= "
		$('organization_type').observe('change', function(event){
			var select = $( 'organization_type' );
			var val = select.selectedIndex >=0 && select.selectedIndex ? select.options[select.selectedIndex].innerHTML : undefined;
			setOrganizationsType(val);
		});";
			if($action == 'customers_add') {
				$script .= "
		$('organization').observe('change', function(event){
			var select = $( 'organization' );
			var val = select.selectedIndex >0 &&  select.selectedIndex ? select.options[select.selectedIndex].innerHTML : undefined;
			setOther(val);
		});";
			}
		}
	$script .= "
	});
";

$out .= "<form id='people_edit' name='people_edit' action='".$_SERVER['PHP_SELF']."' method='POST'>
	<input type='hidden' name='type' value='".$type."'>
	<input type='hidden' name='action' value='save'>
	<input type='hidden' name='case' value='".$action."'>";
if(substr($action,-5) == '_edit') {
	$out .= "<input type='hidden' name='id' value='".$id."'>";
} else {
	$out .= "<input type='hidden' name='added_by_type' value='2'>
	<input type='hidden' name='added_by_id' value='".$_SESSION['user']->id."'>";
}
$out .= "<input type='hidden' name='date_modified' value='".$mod_date."'>
	<div class='formTitle' id='formTitle'>Fields Marked with <span style='color: #FF0000'>*</span> below are required.</div>
	<div id='feedback'>".$feedback;
if($action == 'customers_edit' && $level > 1){
	$out .= "<br />Please be advised: this customer is associated with an order. Changing this person's organization type, organization, or level will effect that order. Please proceed with caution.";
}
$out .= "</div>
<table width='100%' border='0' cellspacing='0' cellpadding='4'>";
if(substr($action,-5) == '_edit') {
	$out .= "<tr>
		<td class='formLabel'>User ID:</td>
		<td class='formInput'>".$id."</td>
		<td class='formLabel'>User Since:</td>";
		$d = explode('-', substr($date_added,0,10));
		$display_date_added = date("M d, Y",mktime(0, 0, 0,$d[1], $d[2], $d[0]));
		$out .= "<td class='formInput'>".$display_date_added."</td>
	</tr>
	<tr>
		<td class='formLabel'>Last Modified:</td>";
		$d = explode('-', substr($date_modified,0,10));
		$display_date_modified = date("M d, Y",mktime(0, 0, 0, $d[1], $d[2], $d[0]));
		$out .= "<td class='formInput'>".$display_date_modified."</td>
	</tr>";
}
if($action == 'customers_add') {
	$out .= "<tr>
		<td class='formLabel'>Organization Type:</td>
		<td class='formInput'>
		  <select name=\"organization_type\" id=\"organization_type\">
		      <option value=\"\"> -- </option>";
		      $res = $np->sendAndGetMany('SELECT DISTINCT(type) FROM Organizations WHERE status="1"');
		      for($r=0;$r<count($res);$r++) {
		          $out .= "<option value=\"".$type."\">".$res[$r]->type."</option>";
              }
              $out .= "
			  <option value=\"other\">Other</option>
	       </select>
		</td>
		<td class='formLabel'>Organization:</td>
		<td class='formInput'>
		  <select class='disabled' name='organization_id' id='organization' tabindex='3' disabled='disabled'></select>
	   </td>
	</tr>
	<tr>
		<td class='formLabel'>Other:</td>
		<td class='formInput'><input class='disabled' name='other_type' type='text' id='other_type' tabindex='1' size='30' disabled='disabled' /></td>
		<td class='formLabel'>Other:</td>
		<td class='formInput'><input class='disabled' name='other_organization' type='text' id='other_organization' tabindex='1' size='30' disabled='disabled' /></td>
	</tr>
	<tr>";
} else {
	if($action == 'users_add' || $action == 'contractors_add') {
		$out .= '<td class="formInput">&nbsp;</td>';
		$out .= '<td>&nbsp;</td>';
		$out .= '<td class="formLabel">Organization:</td>';
		$out .= '<td class="formInput">Cookbook Publishers, Inc.</td>';
		$out .= '<input type="hidden" name="organization_type" value="Business">';
		$out .= '<input type="hidden" name="organization" value="2">';
	} elseif(substr($action,-5) == '_edit') {
		if($action == 'customers_edit') {
			$out .= '<td class="formLabel">Organization Type:</td>';
			$out .= '<td class="formInput">';
			// Find out what organization this person belongs to, and find out what type of organization it is...
            $organization_type = null;
            $query = "SELECT Organizations.type FROM Organizations,People WHERE People.organization_id=Organizations.id AND People.id='".$person_id."'";
			$res = $np->sendAndGetOne($query);
			$organization_type = $res->type;
			// get all organization types
			$res = $np->sendAndGetMany('SELECT DISTINCT(type) FROM Organizations WHERE status="1"');
            $out .= "
            <select name=\"organization_type\" id=\"organization_type\">
                <option value=\"\"> -- </option>";
                foreach($res AS $org) {
                    $selected = '';
                    if($organization_type == $org->type) {
                        $selected = " selected=\"selected\"";
                    }
                    $out .= "
                    <option value=\"".$org->type."\"".$selected.">".$org->type."</option>";
                }
            $out  .= "</select>";
			$out .= '</td>';
			$out .= '<td class="formLabel">Organization:</td>';
			$out .= '<td class="formInput">';
			$res = $np->sendAndGetMany('SELECT id,name FROM Organizations WHERE type="'.$organization_type.'" AND status="1"');
			$out .= "
            <select name=\"organization\" id=\"organization\">
                <option value=\"\"> -- </option>";
                foreach($res AS $org) {
                    $selected = '';
                    if($organization_id == $org->id) {
                        $selected = " selected=\"selected\"";
                    }
                    $out .= "
                    <option value=\"".$org->id."\"".$selected.">".$org->name."</option>";
                }
            $out  .= "</select>";
			$out .= '</td>';
		} else {
			$out .= '<td class="formInput">&nbsp;</td>';
			$out .= '<td>&nbsp;</td>';
			$out .= '<td class="formLabel">Organization:</td>';
			$out .= '<td class="formInput">Cookbook Publishers, Inc.</td>';
			$out .= '<input type="hidden" name="organization_type" value="Business">';
			$out .= '<input type="hidden" name="organization_id" value="2">';
		}
	}
}

$out .= '
	</tr>
	<tr>
		<td class="formLabel"><span style="color: #FF0000">*</span>First Name/Last Name:</td>
		<td class="formInput"><input name="first_name" type="text" id="first_name" tabindex="3" size="13" value="'.stripslashes(urldecode($first_name)).'" />
		<input name="last_name" type="text" id="last_name" tabindex="4" size="13" value="'.stripslashes(urldecode($last_name)).'" /></td>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
	</tr>
	<tr>';
    $casearr = explode('_',$action);
     if($casearr[0] == 'customers') {
         $out .= "<input type='hidden' name='type' value='1'>";
     } else {
         $out .= "<input type='hidden' name='type' value='2'>";
     }
     $level_out = '';
     $status_out = '';
    if($_SESSION['user']->level >= $level) {
        //echo $casearr[0];
        switch($casearr[0]) {
            case 'customers':
                $level_out .= "<input type=\"hidden\" name=\"level\" value=\"1\">";
                break;
            case 'contractors':
            $level_out .= "<input type=\"hidden\" name=\"level\" value=\"6\"> 6";
                break;
            case 'users':
            $level_out = "
            <select name='level' id='level'>";
                for($i=6;$i<9;$i++) {
                    $selected = '';
                    if($level == $i) {
                        $selected = " selected=\"selected\"";
                    }
                    $level_out .= "<option value=\"".$i."\"".$selected.">".$levels[$i]."</option>";
                }
                break;
        }
        $level_out .= "
        </select>";
        $status_out = "
            <select name='status' id='status'>
                <option value=\"\"> -- </option>";
                foreach ($statuses as $key => $val) {
                    $selected = '';
                    if($status == $key) {
                        $selected = " selected=\"selected\"";
                    }
                    $status_out .= "
                <option value=\"".$key."\"".$selected.">".$val."</option>";
                }
                $status_out .= "
            </select>";
    } else {
        foreach ($levels as $key => $val) {
            if($key == $level) {
                $level =  $val;
            }
        }
        $status_out = "<select name='status' id='status'>";
            foreach($statuses AS $key => $val) {
                if($status == $k) {
                    $selected = ' selected="selected"';
                } else {
                    $selected = '';
                }
                $status_out .= "
                <option value=\"".$k."\"".$selected.">".$v."</option>";
            }
            $status_out .= "
            </select>";
    }
   $out .= "
        <td class='formLabel'>Level:</td>
        <td class='formInput'>".$level_out."</td>
        <td class='formLabel'>Status:</td>
        <td class='formInput'>".$status_out."</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>";
	$out .= "</tr>
	<tr>
		<td class='formLabel'><span style='color: #FF0000'>*</span>Email:</td>
		<td class='formInput'><input type='text' name='email' id='email' tabindex='6' size='30' value='".stripslashes(urldecode($email))."' /></td>";
		if($_SESSION['user']->level >= $person->level) {
			$out .= "<td class='formLabel'><span style='color: #FF0000'>*</span>Username:</td>
			<td class='formInput'><input name='login' type='text' id='login' tabindex='13' size='30' value='".stripslashes(urldecode($login))."' /></td>";
		} else {
			$out .= "<td class='formLabel'>&nbsp;</td><td class='formInput'>&nbsp;</td>";
		}
	$out .= "</tr>
	<tr>
		<td class='formLabel'>Phone:</td>
		<td class='formInput'><input name='phone' type='text' id='phone' tabindex='7' size='30' value='".stripslashes(urldecode($phone))."' /></td>";
		if($_SESSION['user']->level >= $person->level) {
			$out .= "<td class='formLabel'><span style='color: #FF0000'>*</span>Password:</td>
			<td class='formInput'><input name='password' type='text' id='password' tabindex='14' size='30' value='".stripslashes(urldecode($password))."' /></td>";
		} else {
			$out .= "<td class='formLabel'>&nbsp;</td>
			<td class='formInput'>&nbsp;</td>";
		}
	$out .= "</tr>
	<tr>
		<td class='formLabel'>Address 1:</td>
		<td class='formInput'><input name='address1' type='text' id='address1' tabindex='8' size='30' value='".stripslashes(urldecode($address1))."' /></td>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
	</tr>
	<tr>
		<td class='formLabel'>Address 2:</td>
		<td class='formInput'><input name='address2' type='text' id='address2' tabindex='9' size='30' value='".stripslashes(urldecode($address2))."' /></td>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
	</tr>
	<tr>
		<td class='formLabel'>City/State/Zip:</td>
		<td colspan='3' class='formInput'><input name='city' type='text' id='city' tabindex='10' size='18' value='".stripslashes(urldecode($city))."' />
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
if($action == 'customers_edit') {
// list all of the people associated with this person
}

require_once(TEMPLATES.'a_people_header.tpl');
require_once(TEMPLATES.'a_people_footer.tpl');
$content = $out;

include(TEMPLATES.'admin.tpl');
?>