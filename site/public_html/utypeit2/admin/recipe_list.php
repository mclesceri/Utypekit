<?php

session_start();

if(!$_SESSION['login'] == true) {
    header('Location: index.php');
}

$page = 'recipe_list';
$tab = 0;

require_once('../src/globals.php');

$script;

include_once(SERVICES.'Orders.php');
include_once(SERVICES.'Cookbook.php');
include_once(SERVICES.'People.php');
$no = new Orders();
$nc = new Cookbook();
$np = new People();

if(isset($_GET['id'])) {
    $order_id = $_GET['id'];
} elseif(isset($_SESSION['order_id'])) {
    $order_id = $_SESSION['order_id'];
}

function getCount($order_id) {
	$nc = new Cookbook();
	$res = $nc->getRecipeCount($order_id);
	return( $res );
}

$limit = 25;
$start = 0;

if(!$_SESSION['list_limit']) {
	$limit = 25;
	$_SESSION['list_limit'] = 25;
} else {
	$limit = $_SESSION['list_limit'];
}

if($_REQUEST['start']) {
	$start = $_REQUEST['start'];
}

if($_REQUEST['limit']) {
	$limit = $_REQUEST['limit'];
	$_SESSION['list_limit'] = $_REQUEST['limit'];
}

if($_REQUEST['orderby']) {
	$orderby = $_REQUEST['orderby'];
} else {
	$orderby = 'id';
}

$pagenum = 1;
if($_REQUEST['page']) {
    $pagenum = $_REQUEST['page'];
}

//id,date_added,title,status
$columns = array('ID','Title','Date Added','Added By ID','Status','');
$recipes = $nc->getRecipeList($order_id,'',$start,$limit,$orderby);//getRecipes($order_id,$person_id,$_SESSION['list_limit'],$start,$orderby);

if(!$recipes) {
	$out = "There are currently no recipes entered for this order. Click <a href='recipe_edit.php?action=recipe_add&order=".$order_id."'>HERE</a> to add a new recipe.";
} else {
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
	// Draw the header
	$script .= '
	           function setPage(type) {
					setList(\''.$pagenum.'\',\''.$orderby.'\',\'recipe_list\');
				}
				
				function doSearch(event) {
					Event.stop(event);
					var form = event.target;
					var oOptions = {
						method: "POST",
						parameters: Form.serialize(form),
						asynchronous: true,
						onFailure: function (oXHR) {
							$(\'feedback\').update(oXHR.statusText);
						},
						onSuccess: function(oXHR) {
							//$(\'feedback\').update(oXHR.responseText);
							$(\'dynamic\').update(oXHR.responseText);
						}
					};
				
					var modurl = window.includes + "process_form.php";
					var oRequest = new Ajax.Updater({success: oOptions.onSuccess.bindAsEventListener(oOptions)}, modurl, oOptions);
				}
				
				function getUsers() {
					var oOptions = {
						method: "GET",
						parameters: {action: \'recipe_users\',order_id: \''.$order_id.'\'},
						asynchronous: true,
						onFailure: function (oXHR) {
							$(\'feedback\').update(oXHR.statusText);
						},
						onSuccess: function(oXHR) {
							//$(\'feedback\').update(oXHR.responseText);
							$(\'search_term_td\').update(oXHR.responseText);
						}
					};
				
					var modurl = "'.INCLUDES.'process_form.php";
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
					$$(\'ul.sublist\')[currentSet].select(\'li\').each(function(ea){ if(ea.hasClassName(\'inactive\')) { ea.removeClassName(\'inactive\') }; });
					
					$(\'recipe_search_by\').selectedIndex = 1;
					$( \'list_limit\' ).observe(\'change\', function(event){
						var select = $( \'list_limit\' );
						var val = select.selectedIndex >=0 && select.selectedIndex ? select.options[select.selectedIndex].value : undefined;
						setPage(val);
					});
					
					$(\'recipe_search_for\').observe(\'change\', function(event){
						var select = $( \'recipe_search_for\' );
						var val = select.options[select.selectedIndex].value;
						if(val == \'added_by_id\') {
							$(\'search_term_td\').update();
							getUsers();
							$(\'recipe_search_by\').selectedIndex = 0;
							$(\'recipe_search_by\').options[1].hide();
							$(\'recipe_search_by\').options[2].hide();
							$(\'recipe_search_by\').options[3].hide();
							$(\'recipe_search_by\').options[4].hide();
						} else if(val == \'date_added\') {
							$(\'search_term_td\').update(dateBlock());
							$(\'recipe_search_by\').selectedIndex = 0;
							$(\'recipe_search_by\').options[1].hide();
							$(\'recipe_search_by\').options[2].show();
							$(\'recipe_search_by\').options[3].show();
							$(\'recipe_search_by\').options[4].show();
						} else if(val == \'id\') {
							$$(\'select#recipe_search_by option\').each(function(o){ o.show(); });
						} else {
							$(\'search_term_td\').update(\'<input type="text" name="recipe_search_term">\');
							$(\'recipe_search_by\').options[1].show();
							$(\'recipe_search_by\').options[2].hide();
							$(\'recipe_search_by\').options[3].hide();
							$(\'recipe_search_by\').options[4].show();
						}
					});
				})';
	
	if($_SESSION['utypeit_info']->max_recipes) {
		if($_SESSION['recipe_count'] >= $_SESSION['utypeit_info']->max_recipes) {
			$out .= '<span class="required">You have reached the total recipes for this cookbook. To change this limit, use the "Edit Member Options" link on the left.</span>';
		}
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
$search = "
<form id=\"recipe_search\" onsubmit=\"doSearch(event); return false;\">\n
<input type=\"hidden\" name=\"action\" value=\"recipe_search\">
<input type=\"hidden\" name=\"order_id\" value=\"".$order_id."\">
<table style=\"width: 100%\">\n
	\t<tr>\n
		\t\t<td class=\"formLabel\">Search for recipes where the</td>\n
		\t\t<td>\n
		\t\t<select id=\"recipe_search_for\" name=\"recipe_search_for\">\n
			\t\t\t<option value=\"id\">ID</option>\n
			\t\t\t<option value=\"title\" selected=\"selected\">Title</option>\n
			\t\t\t<option value=\"date_added\">Date Added</option>\n
			\t\t\t<option value=\"added_by_id\">Added By ID</option>\n
			\t\t\t<option value=\"status\">Status</option>\n
		\t\t</select>\n
		\t\t</td>\n
		\t\t<td>\n
		\t\t<select name=\"recipe_search_by\" id=\"recipe_search_by\">\n
			\t\t\t<option value=\"is\">equals</option>\n
			\t\t\t<option value=\"like\">is like</option>\n
			\t\t\t<option value=\"less\" style=\"display: none\">is less than</option>\n
			\t\t\t<option value=\"more\" style=\"display: none\">is more than</option>\n
			\t\t\t<option value=\"not\">is not</option>\n
		\t\t</select>\n
		\t\t</td>\n
		\t\t<td id=\"search_term_td\"><input type=\"text\" name=\"recipe_search_term\"></td>\n
		\t\t<td><input type=\"submit\" value=\"Go\" style=\"width: 45px\"></td>\n
		\t\t<td style=\"width: 50px\">&nbsp;</td>\n
		\t\t<td><input type=\"button\" value=\"Clear\" onclick=\"setList('1','id','recipe_list');\" style=\"width: 45px\"></td>\n
	\t</tr>\n
</table>\n
</form><div id=\"feedback\" style=\"width: 98%; margin: 0 5px 5px 5px\"></div>\n";

require_once(TEMPLATES.'a_recipes_header.tpl');
$content = $out;

include(TEMPLATES.'admin.tpl');
?>