<?php

session_start();

if(!$_SESSION['login'] == true) {
	header('Location: index.php');
}

$page = 'people_list';

require_once('../src/globals.php');

require_once(SERVICES.'People.php');

require_once(INCLUDES.'ListLimit.php');
require_once(INCLUDES.'BreadCrumb.php');
$bc = new BreadCrumb($page);

function getPeople($theperson,$start,$limit,$orderby,$level='') {
	$newPeople = new People();
	return( $newPeople->getPeopleList($theperson,$start,$limit,$orderby,$level) );
}

function getCount($type) {
	$newPeople = new People();
	$res = $newPeople->getPeopleCount($type);
	return( $res );
}

$action = $_GET['action'];

$start = 0;
$limit = 25;

if(!$_SESSION['list_limit']) {
	$_SESSION['list_limit'] = 25;
} else {
	$limit = $_SESSION['list_limit'];
}

$orderby = 'id';
if(isset($_GET['orderby'])) {
	$orderby = $_GET['orderby'];
}

if($_GET['page']) {
	$pagenum = $_GET['page'];
} else {
	$pagenum = 1;
}

if($_GET['start']) {
	$start = $_GET['start'];
} else {
	$start = '0';
}

if($_GET['limit']) {
	$_SESSION['list_limit'] = $_GET['limit'];
}

$header_left = '&nbsp;';
$header_right = '&nbsp;';

$total_people = getCount($action);

switch($action) {
	case 'customers':
		$title = "LIST CUSTOMERS";
		$tab = 1;
		$list_name = 'customer_list';
		$people = getPeople('customers',$start,$limit,$orderby);
		$columns = array('ID','Last Name','Email','Level','Status','');		
		break;
	case 'users':
		$title = "LIST USERS";
		$tab = 2;
		$list_name = 'user_list';
		$level = $_SESSION['user']->level;
		$people = getPeople('users',$start,$limit,$orderby,$level);
		$columns = array('ID','Last Name','Email','Level','Status','');
		break;
	case 'contractors':
		$title = "LIST CONTRACTORS";
		$tab = 3;
		$list_name = 'contractor_list';
		$people = getPeople('contractors',$start,$limit,$orderby);
		$columns = array('ID','Last Name','Email','State','Status','');
		break;
}

// Draw the header
$script = '
            function dateBlock(){
                var block = \'<select name="month">';
                    $months = array('01'=>'January',
                                    '02'=>'February',
                                    '03'=>'March',
                                    '04'=>'April',
                                    '05'=>'May',
                                    '06'=>'June',
                                    '07'=>'July',
                                    '08'=>'August',
                                    '09'=>'September',
                                    '10'=>'October',
                                    '11'=>'November',
                                    '12'=>'December');
                    foreach($months AS $k=>$v) {
                        $script .= '<option value="'.$k.'">'.$v.'</option>';
                    }
                $script .= '</select><select name="day">';
                for($i=1;$i<32;$i++) {
                    $script .= '<option value="'.$i.'">'.$i.'</option>';
                }
                $script .= '</select><select name="year">';
                $today = new DateTime();
                $year = date_format($today, 'Y');
                $years = array(($year-3),($year-2),($year-1),$year,($year+1),($year+2),($year+3));
                foreach($years AS $y) {
                    $selected = '';
                    if($y == $year) {
                        $selected = " selected=\"selected\"";
                    }
                    $script .= '<option value="'.$y.'"'.$selected.'>'.$y.'</option>';
                }
                $script .= '</select>\';
                return(block);
            }

            function setPeopleList(type) {
				setList(\''.$pagenum.'\',\''.$orderby.'\',\'people_list\',\'&action='.$action.'\');
			}
			document.observe(\'dom:loaded\', function() {
				
				showSet(currentSet);
				
				$( \'list_limit\' ).observe(\'change\', function(event){
					var select = $( \'list_limit\' );
					var val = select.selectedIndex >=0 && select.selectedIndex ? select.options[select.selectedIndex].value : undefined;
					setPeopleList(val);
				});
                
                $(\'people_search_for\').observe(\'change\', function(event){
                    var select = $( \'people_search_for\' );
                    var val = select.options[select.selectedIndex].value;
                    if(val == \'added_by_id\') {
                        $(\'people_term_td\').update();
                        getUsers();
                        $(\'people_search_by\').selectedIndex = 0;
                        $(\'people_search_by\').options[1].hide();
                        $(\'people_search_by\').options[2].hide();
                        $(\'people_search_by\').options[3].hide();
                        $(\'people_search_by\').options[4].hide();
                    } else if(val == \'date_added\') {
                        $(\'search_term_td\').update(dateBlock());
                        $(\'people_search_by\').selectedIndex = 1;
                        $(\'people_search_by\').options[0].hide();
                        $(\'people_search_by\').options[2].show();
                        $(\'people_search_by\').options[3].show();
                        $(\'people_search_by\').options[4].show();
                    } else if(val == \'id\') {
                        $$(\'select#people_search_by option\').each(function(o){ o.show(); });
                    } else {
                        $(\'search_term_td\').update(\'<input type="text" name="search_term">\');
                        $(\'people_search_by\').options[0].show();
                        $(\'people_search_by\').options[1].show();
                        $(\'people_search_by\').options[2].hide();
                        $(\'people_search_by\').options[3].hide();
                        $(\'people_search_by\').options[4].show();
                    }
                });
            });
            
            function doSearch(event) {
                Event.stop(event);
                var form = $(\'people_search\');
                var oOptions = {
                    method: "POST",
                    parameters: Form.serialize(form),
                    asynchronous: true,
                    onFailure: function (oXHR) {
                        alert(oXHR.statusText);
                        $(\'feedback\').update(oXHR.statusText);
                    },
                    onSuccess: function(oXHR) {
                        //$(\'feedback\').update(oXHR.responseText);
                        $(\'dynamic\').update(oXHR.responseText);
                    }
                };
            
                var modurl = window.includes + "process_search.php";
                var oRequest = new Ajax.Updater({success: oOptions.onSuccess.bindAsEventListener(oOptions)}, modurl, oOptions);
		  };';
$search = "
<form id=\"people_search\" onsubmit=\"doSearch(event); return false;\">\n
<input type=\"hidden\" name=\"m-submit\" value=\"people_search\">
<input type=\"hidden\" name=\"search_type\" value=\"".$action."\">
<table style=\"width: 100%\">\n
    \t<tr>\n
        \t\t<td class=\"formLabel\">Search for people where the</td>\n
        \t\t<td>\n
        \t\t<select id=\"people_search_for\" name=\"search_for\">\n
            \t\t\t<option value=\"id\">ID</option>\n
            \t\t\t<option value=\"first_name\">First Name</option>\n
            \t\t\t<option value=\"last_name\" selected=\"selected\">Last Name</option>\n
            \t\t\t<option value=\"email\">Email</option>\n
            \t\t\t<option value=\"city\">City</option>\n
            \t\t\t<option value=\"state\">State</option>\n
            \t\t\t<option value=\"login\">Username</option>\n
            \t\t\t<option value=\"date_added\">Date Added</option>\n
            \t\t\t<option value=\"added_by_id\">Added By ID</option>\n
            \t\t\t<option value=\"status\">Status</option>\n
        \t\t</select>\n
        \t\t</td>\n
        \t\t<td>\n
        \t\t<select name=\"search_by\" id=\"people_search_by\">\n
            \t\t\t<option value=\"is\">equals</option>\n
            \t\t\t<option value=\"like\" selected=\"selected\">is like</option>\n
            \t\t\t<option value=\"less\" style=\"display: none\">is less than</option>\n
            \t\t\t<option value=\"more\" style=\"display: none\">is more than</option>\n
            \t\t\t<option value=\"not\">is not</option>\n
        \t\t</select>\n
        \t\t</td>\n
        \t\t<td id=\"search_term_td\"><input type=\"text\" name=\"search_term\"></td>\n
        \t\t<td><input type=\"submit\" value=\"Go\" style=\"width: 45px\"></td>\n
        \t\t<td style=\"width: 50px\">&nbsp;</td>\n
        \t\t<td><input type=\"button\" value=\"Clear\" onclick=\"setList('1','id','people_list');\" style=\"width: 45px\"></td>\n
    \t</tr>\n
</table>\n
</form><div id=\"feedback\" style=\"width: 98%; margin: 0 5px 5px 5px\"></div>\n";

$out = "<table class='listTable' cellpadding='0' cellspacing='0'>\n";
$out .= "<tr>\n";
foreach($columns AS $c) {
	$order = strtolower(str_replace(' ','_',$c));
	$out .= '<td class="listHeader"><a href="#" class="subheaderLink" onclick="setContent(\'people_list\',{mode:\'redirect\',action:\''.$action.'\',start:\''.$start.'\',limit:\''.$_SESSION['list_limit'].'\',orderby:\''.$order.'\'})">'.$c.'</a></td>';
}
$out .= "</tr>\n";

$levels = array('Inactive','Demo User','Contributor','Committee','Cochairperson','Chairperson','Contractor','Customer Support','Administrator','Super User');
for($p=0;$p<count($people);$p++) {
	$out .= '<tr>';
	$status = '';
	$this_action = $action.'_edit';
	foreach($people[$p] as $e=>$v) {
		if($e == 'status') {
		    if($v == 1) {
		        $v = "Active";
		    } else {
		        $v = "Inactive";
		    }
		}
        if($e == 'level' || $e == 'order_level') {
            for($i=0;$i<count($levels);$i++) {
                if($v == $i) {
                    $v = $levels[$i];
                }
            }
        }
		$out .= '<td class="listItem">'.stripslashes(urldecode($v)).'</td>';
	}
	$out .= '<td class="listItem"><a href="#" onclick="setContent(\'people_edit\',{mode:\'redirect\',action:\''.$this_action.'\',id:\''.$people[$p]->id.'\'})" class="listEdit">Details</a></td>';
	$out .= '</tr>';
}

// Draw the close
$out .= '</table>';

require_once(TEMPLATES.'a_people_header.tpl');
$content = $out;

include(TEMPLATES.'admin.tpl');

?>