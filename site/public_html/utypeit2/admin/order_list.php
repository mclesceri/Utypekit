<?php

session_start();

if(!$_SESSION['login'] == true) {
	header('Location: index.php');
}

require_once('../src/globals.php');

$page = 'order_list';
$tab = 0;
$start = 0;
$limit = 25;

require_once(SERVICES.'Orders.php');
$no = new Orders();

if(!$_SESSION['list_limit']) {
	$_SESSION['list_limit'] = $limit;
}

$action = '';
if($_GET['action']) {
	$action = $_GET['action'];
}

if(isset($_SESSION['user'])) {
	if($_SESSION['user']->level == 6) {
		$action = 'contractor';
	}
}

function getCount($type,$id='') {
	$newOrder = new Orders();
	$res = $newOrder->getOrderCount($type,$id);
	return( $res->COUNT );
}

if($_GET['start']) {
	$start = $_GET['start'];
} else {
	$start = '0';
}

if($_REQUEST['limit']) {
	$limit = $_REQUEST['limit'];
	$_SESSION['list_limit'] = $_REQUEST['limit'];
}

if(isset($_GET['orderby'])) {
	$orderby = $_GET['orderby'];
} else {
	$orderby = 'id';
}

if($_GET['page']) {
	$pagenum = $_GET['page'];
} else {
	$pagenum = 1;
}

$list_name = 'orders_list';

//id,title,DATE_FORMAT(date_added,"%M %d, %Y"),order_number,status
$columns = array('ID','Title','Date Added','Order Number','Chairperson','Status','');

$the_id = '';

if($action == 'customer') {
	$the_id = $_GET['id'];
	$orders = $no->getCustomerOrderList($the_id,$start,$_SESSION['list_limit'],$orderby);
} elseif($action == 'contractor') {
	if(isset($_GET['id'])) {
		$the_id = $_GET['id'];
	} else {
		$the_id = $_SESSION['user']->id;
	}
	$contractor = $the_id;
	if($_SESSION['user']->level == '6') {
		$contractor = $_SESSION['user']->id;
	}
	$orders = $no->getContractorOrderList($contractor,$start,$_SESSION['list_limit'],$orderby);
} else {
	$orders = $no->getOrderList($start,$_SESSION['list_limit'],$orderby);
}

$order_list = array();
for($o=0;$o<count($orders);$o++) {
	$order_list[$o]['ID'] = $orders[$o]->id;
	$order_list[$o]['Title'] = $orders[$o]->title;
	$add_date = new DateTime($orders[$o]->date_added);
	$order_list[$o]['Date Added'] = date_format($add_date,'M d, Y');
	$order_list[$o]['Order Number'] = $orders[$o]->order_number;
	$order_list[$o]['Chairperson'] = $orders[$o]->chairperson;
	if(isset($orders[$o]->added_by)) {
		$order_list[$o]['Chairperson'] = '<span style="color: red;">'.$orders[$o]->added_by.'</span>';
	}
	$order_list[$o]['Status'] = $orders[$o]->status;
	$order_list[$o]['Edit'] = $orders[$o]->id;
}
// Draw the header
$script = '
            function setOrdersList(type) {
				setList(\''.$pagenum.'\',\''.$orderby.'\',\'order_list\',\'\');
			}
			
			function getUsers() {
				var oOptions = {
					method: "GET",
					parameters: {action: \'order_users\'},
					asynchronous: true,
					onFailure: function (oXHR) {
						$(\'feedback\').update(oXHR.statusText);
					},
					onSuccess: function(oXHR) {
						//$(\'feedback\').update(oXHR.responseText);
						$(\'search_term_td\').update(oXHR.responseText);
					}
				};
			
				var modurl = "'.ADMIN_URL.'src/includes/process_form.php";
				var oRequest = new Ajax.Updater({success: oOptions.onSuccess.bindAsEventListener(oOptions)}, modurl, oOptions);
			}
			
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

			document.observe(\'dom:loaded\', function() {
				
				showSet(currentSet);
				
				$( \'list_limit\' ).observe(\'change\', function(event){
					var select = $( \'list_limit\' );
					var val = select.selectedIndex >=0 && select.selectedIndex ? select.options[select.selectedIndex].value : undefined;
					setOrdersList(val);
				});
				
				$(\'orders_search_for\').observe(\'change\', function(event){
					var select = $( \'orders_search_for\' );
					var val = select.options[select.selectedIndex].value;
					if(val == \'added_by_id\') {
						$(\'search_term_td\').update();
						getUsers();
						$(\'orders_search_by\').selectedIndex = 0;
						$(\'orders_search_by\').options[1].hide();
						$(\'orders_search_by\').options[2].hide();
						$(\'orders_search_by\').options[3].hide();
						$(\'orders_search_by\').options[4].hide();
					} else if(val == \'date_added\') {
						$(\'search_term_td\').update(dateBlock());
						$(\'orders_search_by\').selectedIndex = 1;
						$(\'orders_search_by\').options[0].hide();
						$(\'orders_search_by\').options[2].show();
						$(\'orders_search_by\').options[3].show();
						$(\'orders_search_by\').options[4].show();
					} else if(val == \'id\') {
						$$(\'select#orders_search_by option\').each(function(o){ o.show(); });
					} else {
						$(\'search_term_td\').update(\'<input type="text" name="orders_search_term">\');
						$(\'orders_search_by\').options[0].show();
						$(\'orders_search_by\').options[1].show();
						$(\'orders_search_by\').options[2].hide();
						$(\'orders_search_by\').options[3].hide();
						$(\'orders_search_by\').options[4].show();
					}
				});
			});
			
			function doSearch(event) {
				Event.stop(event);
				var form = $(\'orders_search\');
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
			
				var modurl = window.includes + "process_form.php";
				var oRequest = new Ajax.Updater({success: oOptions.onSuccess.bindAsEventListener(oOptions)}, modurl, oOptions);
			}';

$out = "<table class='listTable' cellpadding='0' cellspacing='0'>\n";
$out .= "<tr>\n";
foreach($columns AS $c) {
	$order = strtolower(str_replace(' ','_',$c));
	$out .= '<td class="listHeader"><a href="#" class="subheaderLink" onclick="setContent(\'order_list\',{mode:\'redirect\',start:\''.$start.'\',limit:\''.$_SESSION['list_limit'].'\',orderby:\''.$order.'\'})">'.$c.'</a></td>';
}
$out .= "</tr>\n";

for($p=0;$p<count($order_list);$p++) {
	$out .= '<tr>';
	foreach($order_list[$p] as $e=>$v) {
		if($e == 'Status') {
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
		} elseif($e != 'Edit') {
			$out .= '<td class="listItem">'.stripslashes(urldecode($v)).'</td>';
		} else {
			$edit = '<td class="listItem"><a href="#" onclick="setContent(\'order_edit\',{mode:\'redirect\',action:\'order_edit\',id:\''.$v.'\'})" class="listEdit">EDIT</a></td>';
			if($status == 'To Print' && $_SESSION['user']->level < 8) {
				$edit = '<td class="listItem">&nbsp;</td>';
			}
			$out .= $edit;
		}
	}
	$out .= '</tr>';
}

// Draw the close
$out .= '</table>';

$search = "
<form id=\"orders_search\" onsubmit=\"doSearch(event);\">\n
<input type=\"hidden\" name=\"action\" value=\"orders_search\">
<table style=\"width: 100%\">\n
	\t<tr>\n
		\t\t<td class=\"formLabel\">Search for orders where the</td>\n
		\t\t<td>\n
		\t\t<select id=\"orders_search_for\" name=\"orders_search_for\">\n
			\t\t\t<option value=\"id\">ID</option>\n
			\t\t\t<option value=\"title\">Title</option>\n
			\t\t\t<option value=\"order_number\" selected=\"selected\">Order Number</option>\n
			\t\t\t<option value=\"date_added\">Date Added</option>\n
			\t\t\t<option value=\"added_by_id\">Added By ID</option>\n
			\t\t\t<option value=\"status\">Status</option>\n
		\t\t</select>\n
		\t\t</td>\n
		\t\t<td>\n
		\t\t<select name=\"orders_search_by\" id=\"orders_search_by\">\n
			\t\t\t<option value=\"is\">equals</option>\n
			\t\t\t<option value=\"like\" selected=\"selected\">is like</option>\n
			\t\t\t<option value=\"less\" style=\"display: none\">is less than</option>\n
			\t\t\t<option value=\"more\" style=\"display: none\">is more than</option>\n
			\t\t\t<option value=\"not\">is not</option>\n
		\t\t</select>\n
		\t\t</td>\n
		\t\t<td id=\"search_term_td\"><input type=\"text\" name=\"orders_search_term\"></td>\n
		\t\t<td><input type=\"submit\" value=\"Go\" style=\"width: 45px\"></td>\n
		\t\t<td style=\"width: 50px\">&nbsp;</td>\n
		\t\t<td><input type=\"button\" value=\"Clear\" onclick=\"setList('1','id','order_list');\" style=\"width: 45px\"></td>\n
	\t</tr>\n
</table>\n
</form><div id=\"feedback\" style=\"width: 98%; margin: 0 5px 5px 5px\"></div>\n";
require_once(TEMPLATES.'a_orders_header.tpl');
$content = $out;

include(TEMPLATES.'admin.tpl');

?>