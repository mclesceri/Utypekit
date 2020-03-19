<?php

session_start();

if(!$_SESSION['login'] == true) {
	header('Location: index.php');
}

ini_set('session.bug_compat_warn', 0);
ini_set('session.bug_compat_42', 0);

require_once('src/globals.php');

$page = 'order_edit';

require_once(SERVICES.'Orders.php');
require_once(SERVICES.'People.php');

require_once(INCLUDES.'Elements.php');
require_once(INCLUDES.'Order.php');
require_once(INCLUDES.'OrderedList.php');
require_once(INCLUDES."Warnings.php");

$ne = new Elements();
$no = new Orders();
$np = new People();

// null values for variables...
$organization_type = null;
$organization_name = null;
$order_id = null;
$order_number = '';
$order_title = null;

$book_title1 = null;
$book_title2 = null;
$book_title3 = null;
$book_count = null;
$book_style = null;

$order_form = null;
$order_form_name = null;
$order_form_address1 = null;
$order_form_address2 = null;
$order_form_city = null;
$order_form_state = null;
$order_form_zip = null;
$order_form_retail = null;
$order_form_shipping = null;

$contributors = null;
$order_recipes_by = null;
$design_type = null;
$recipes_continued = null;
$allow_notes = null;
$use_icons = null;
$use_fillers = null;
$nutritionals = null;
$use_subcategories = null;
$order_index_by = null;

$filler_set = null;
$filler_type = null;
$recipe_format = null;
$date_added = null;
$date_modified = null;
$added_by_id = null;
$_SESSION['warning'] = null;
 
 $demo = false;

 $tabindex = 0;
 
/*
 * 
 *  Load and pre-set the recipe format
 * 
 */
$fileref = DATA.'xml/recipe_formats.xml';
$data = simplexml_load_file($fileref);
$data_set = json_encode($data);
$format_array = json_decode($data_set);
$format_count = count($format_array->format);
$opt_str = '<option value="0"> -- </option>';

function getUsers($order_id) {
	$newPeople = new People();
	$result = $newPeople->sendAndGetMany('SELECT People.id,People.first_name,People.last_name,Order_People.level AS order_level FROM People,Order_People WHERE Order_People.order_id="'.$order_id.'" AND People.id=Order_People.person_id ORDER BY id DESC');
	return( $result );
}

function makeOrderPersonsList($order_id) {
	$newOrder = new Orders();
	$result = $newOrder->getOrderPeople($order_id);
	$o = new Order();
	$i = 0;
	foreach($result AS $r) {
		$attr = 'name='.$r->first_name.' '.$r->last_name.'&id='.$r->id.'&level='.$r->level.'&count='.($i+1);
		$out .= $o->_orderparts('person',$attr);
		$i++;
	}
	return( $out );
}

function getOrgInfo() {
	$order_id = 1;
	if(isset($_SESSION['order_id'])) {
		$order_id = $_SESSION['order_id'];
	}
	$no = new Orders();
    $query = 'SELECT Organizations.* FROM Organizations, Order_Organizations WHERE Order_Organizations.order_id="'.$order_id.'" AND Organizations.id=Order_Organizations.organization_id';
	$res = $no->sendAndGetOne($query);
	return($res);
}

$status_list = 'order_status';
$mod_date = date('Y-m-d H:i:s');

$action = $_REQUEST['action'];

if($action != 'order_add') {
	if(isset($_GET['id'])) {
		$order_id = $_GET['id'];
		$_SESSION['order_id'] = $order_id;
	}
	$order_id = $_SESSION['order_id'];
}
 if($order_id == 1) {
	 $demo = true;
 }

switch($action) {
	case 'order_list':
		$order_id = $_GET['id'];
		header('Location: order_edit.php?action=order_edit&id='.$order_id);
		break;
	case 'order_add':
		$title = "ADD NEW ORDER";
		$function = 'order_add';
		
		$status = 1;
		
		$cat_json = '{"categories":[{"number": "1","name": "Appetizers%2C+Beverages","order": "1","parent":"0"},{"number": "2","name": "Soups%2C+Salads","order": "2","parent":"0"},{"number": "3","name": "Vegetables","order": "3","parent":"0"},{"number": "4","name": "Main Dishes","order": "4","parent":"0"},{"number": "5","name": "Breads%2C+Rolls","order": "5","parent":"0"},{"number": "6","name": "Desserts","order": "6","parent":"0"},{"number": "7","name": "Miscellaneous","order": "7","parent":"0"}]}';
		$categories = json_decode($cat_json);
		$_SESSION['categories'] = $categories;
		unset($_SESSION['general_info']);
		break;
	case 'order_edit':
		$this_order = $no->getComposedOrder($order_id);
    	
        // parse out the variables into something useful
        // Order Information
        $order_title = $this_order->order->title;
        $order_number = $this_order->order->order_number;
        $_SESSION['order_number'] = $order_number;
        $added_by_id = $this_order->order->added_by_id;
        $added_by_type = $this_order->order->added_by_type;
        $add_date = new DateTime($this_order->order->date_added);
        $date_added = $add_date->format('M d, Y');
        $modify_date = new DateTime($this_order->order->date_modified);
        $date_modified = $modify_date->format('M d, Y');
        $status = 1;
        if(isset($this_order->order->status)) {
            $status = $this_order->order->status;
        }
        
        $title = "EDIT ORDER #".$order_number;
        $form_action = "order_edit";
		$function = 'order_edit';
		
        // Meta Information
        $general_info = new stdClass();
		$step = explode('|',$this_order->general_info);
		foreach($step AS $s) {
			$substep = explode(':',$s);
			if($substep[0]) {
				$general_info->{$substep[0]} = $substep{1};
				$$substep[0] = htmlspecialchars(stripslashes(urldecode($substep[1])));
			}
		}
		
		$_SESSION['general_info'] = $general_info;
		$recipe_format = 'Traditional';
		if(isset($_SESSION['general_info']->recipe_format)) {
		    $recipe_format = $_SESSION['general_info']->recipe_format;
		}
		$_SESSION['order_number'] = $order_number;
		
		//$_SESSION['utypeit_info'] = $utypeit_info
		if(isset($this_order->utypeit_info)) {
			$utypeit_info = new stdClass();
			$step = explode('|',$this_order->utypeit_info);
			foreach($step AS $s) {
				$substep = explode(':',$s);
				if($substep[0]) {
					$utypeit_info->{$substep[0]} = $substep[1];
				}
				$_SESSION['utypeit_info'] = $utypeit_info;
			}
		}
		
		$categories = '';
		$cat_json = '{"categories":[{"number": "1","name": "Appetizers%2C+Beverages","order": "1","parent":"0"},{"number": "2","name": "Soups%2C+Salads","order": "2","parent":"0"},{"number": "3","name": "Vegetables","order": "3","parent":"0"},{"number": "4","name": "Main Dishes","order": "4","parent":"0"},{"number": "5","name": "Breads%2C+Rolls","order": "5","parent":"0"},{"number": "6","name": "Desserts","order": "6","parent":"0"},{"number": "7","name": "Miscellaneous","order": "7","parent":"0"}]}';
		unset($_SESSION['categories']);
		if(isset($this_order->categories)) {
			$categories = json_decode($this_order->categories);
			if(!$categories) {
				$categories = json_decode($cat_json);
			}
		} else {
            $categories = json_decode($cat_json);
		}
		$_SESSION['categories'] = $categories;
		
		$subcategories = '';
		unset($_SESSION['subcategories']);
		if(isset($this_order->subcategories)) {
			$subcategories = json_decode($this_order->subcategories);
			$_SESSION['subcategories'] = $subcategories;
		}
        
		break;
}

$organization = getOrgInfo();
$script = "
		
        var of_required = '';
        var pf_required = '';
        var required = 'title';
        
        var formats = [";
        $formats = $format_array->format;
        foreach($formats AS $f) {
            $script .= '
            {name: "'.$f->name.'",description: "'.$f->description.'",flag: "'.$f->flag.'", image1: "'.$f->image1.'",image2: "'.$f->image2.'",thumbnail1: "'.$f->thumbnail1.'",thumbnail2:"'.$f->thumbnail2.'"},';
        }
        $script = substr($script, 0, -1);
        $script .= "
        ];
        var format_count = ".$format_count.";
        var ol;
		document.observe('dom:loaded',function() {
	        
	        if($('order_form_yes').checked == true) {
        		of_required = ',order_form_name,order_form_address1,order_form_city,order_form_state,order_form_zip';
			}
			
			if($('use_fillers_yes').checked == true) {
				$('filler_type').up('td').previous('td').insert({top:'<span style=\"color: #F00\">*</span>'});
				$('filler_set').up('td').previous('td').insert({top:'<span style=\"color: #F00\">*</span>'});
	        	pf_required = ',filler_type,filler_set';
	        }
	        
            fancyNav();
            
			ol = new OrderedList({type: 'div',root: 'list_parent', drag: true});
			ol._activate();
            
            selectFormat('".$recipe_format."');
            
            $('order_edit').observe('submit', function(event) {
                    Event.stop(event);
            });
			
            $('organization_type').observe('change',function(){
                if($('organization_type')[$('organization_type').selectedIndex].value == 'other') {
                    $('other_type').enable();
                } else {
                    $('other_type').disable();
                }
            })
            
            $('recipes_continued_no').observe('click', function(event){
            	$('recipes_continued_no').up('td',0).addClassName('designeroption_off');
				$('recipes_continued_no').up('td',0).next('td').addClassName('designeroption_on');
				$('use_fillers_yes').enable();
				$('use_fillers_no').enable();
				setRecipeFormatSelect('no');
			});
						
			$('recipes_continued_yes').observe('click', function(event){
				$('recipes_continued_no').up('td',0).next('td').removeClassName('designeroption_on');
				$('recipes_continued_no').up('td',0).next('td').addClassName('designeroption_off');
				$('use_fillers_no').checked = true;
				$('use_fillers_yes').disable();
				$('use_fillers_no').disable();
				
				$('filler_type').up('td').previous('td').update('Filler Type:');
				$('filler_set').up('td').previous('td').update('Filler Set:');
				pf_required = '';
				
				$('filler_type').selectedIndex=0;
				$('filler_type').disable();
				
				$('filler_set').selectedIndex=0;
				$('filler_set').disable();
				setRecipeFormatSelect('yes');
			});
							
			$('allow_notes_yes').observe('click', function(event){
				$('allow_notes_yes').up('td',0).next('td').removeClassName('designeroption_off');
				$('allow_notes_yes').up('td',0).next('td').addClassName('designeroption_on');
			});
			
			$('allow_notes_no').observe('click', function(event){
				$('allow_notes_no').up('td',0).next('td').removeClassName('designeroption_on');
				$('allow_notes_no').up('td',0).next('td').addClassName('designeroption_off');
			});
			
			$('use_icons_yes').observe('click', function(event){
				$('use_icons_yes').up('td',0).next('td').removeClassName('designeroption_off');
				$('use_icons_yes').up('td',0).next('td').addClassName('designeroption_on');
			});
			
			$('use_icons_no').observe('click', function(event){
				$('use_icons_no').up('td',0).next('td').removeClassName('designeroption_on');
				$('use_icons_no').up('td',0).next('td').addClassName('designeroption_off');
			});
			
			$('use_fillers_yes').observe('click', function(event){
				$('use_fillers_yes').up('td',0).next('td').removeClassName('designeroption_off');
				$('use_fillers_yes').up('td',0).next('td').addClassName('designeroption_on');
				// must make filler type and filler set required
				$('filler_type').up('td').previous('td').insert({top:'<span style=\"color: #F00\">*</span>'});
				$('filler_set').up('td').previous('td').insert({top:'<span style=\"color: #F00\">*</span>'});
				pf_required = 'filler_type,filler_set';
				$('filler_type').enable();
			});
			
			$('use_fillers_no').observe('click', function(event){
				$('use_fillers_yes').up('td',0).next('td').removeClassName('designeroption_on');
				$('use_fillers_yes').up('td',0).next('td').addClassName('designeroption_off');
				// must remove filler type and filler set required
				// must make filler type and filler set required
				$('filler_type').up('td').previous('td').update('Filler Type:');
				$('filler_set').up('td').previous('td').update('Filler Set:');
				pf_required = '';
				$('filler_type').selectedIndex=0;
				$('filler_type').disable();
				$('filler_set').selectedIndex=0;
				$('filler_set').disable();
			});
						
			$('filler_type').observe('change', function(event){
				var select = $( 'filler_type' );
				var val = select.selectedIndex >=0 && select.selectedIndex ? select.options[select.selectedIndex].value : undefined;
				setFillerType(val);
			});
			
			var currentFormat = '".$recipe_format."';
			$$('.recipe_format').each(function(ea) {
				ea.observe('click',function(event) {
					
					var add_exp = Array('Premiere','Fanciful','Casual','Black Tie');
					var oldFormat;
					$$('.recipe_format').each(function(ea){ if(ea.value == currentFormat) oldFormat = ea; });
					var target = event.target;
											
					if($('recipes_continued_yes').checked == true) {
						if($(target).readAttribute('flag') == 'rnc') {
							$(target).checked = false;
							oldFormat.checked = true;
							alert('This format is only available if recipes are not continued page to page');
						} else {
							if(target.value == 'CentSaver') {
								if(target.up('p').hasClassName('centsaver_off')){ target.up('p').removeClassName('centsaver_off').addClassName('centsaver_on'); }
							} else {
								$('format_slider').select('p').each(function(ea){if(ea.hasClassName('centsaver_on')){ ea.removeClassName('centsaver_on').addClassName('centsaver_off')} });
							}
							$(target).checked = true;
							currentFormat = $(target).value;
						}
						
					} else {
						if($(target).readAttribute('flag') == 'rc') {
							$(target).checked = false;
							oldFormat.checked = true;
							alert('This format is only available if recipes are continued page to page');
						} else {
							$('format_slider').select('p').each(function(ea){if(ea.hasClassName('centsaver_on')){ ea.removeClassName('centsaver_on').addClassName('centsaver_off')} });
							$('format_slider').select('p').each(function(ea){if(ea.hasClassName('designeroption_on')){ ea.removeClassName('designeroption_on').addClassName('designeroption_off')} });
							add_exp.each(function(ea){
								
								if(ea == target.value) {
									if(target.up('p').hasClassName('designeroption_off')){ target.up('p').removeClassName('designeroption_off').addClassName('designeroption_on'); }
								}
							});
							$(target).checked = true;
							currentFormat = $(target).value;
						}
					}
				});
			});
			
            $('order_edit').select('input.usub').each(function(ea) {
                ea.observe('click',function(event){
                    if(ea.id == 'use_subcategories_yes') {
                        //$('subtoc_yes').removeAttribute('disabled');
                        //$('subtoc_no').removeAttribute('disabled');
                        setSubcategories('yes');
                    } else {
                        //$('subtoc_yes').setAttribute('disabled','disabled');
                        //$('subtoc_no').setAttribute('disabled','disabled');
                        //$('subtoc_no').checked = true;
                        setSubcategories('no');
                    }
                });
            });
			
            $('order_edit').select('input.oform').each(function(ea){                
                ea.observe('change',function(){
                    if(ea.id == 'order_form_yes') {
                        $('order_form_tr').show();
                        window.of_required = ',order_form_name,order_form_address1,order_form_city,order_form_state,order_form_zip';
                    } else {
                        $('order_form_tr').hide();
                        window.of_required = '';
                    }
                });
            });
		});";
$out = '
<div id="feedback"">&nbsp;</div>
<div id="order_options_navigation">
    <ul>';
        $line_items = array('General Information','User\'s Information','Page Options','Recipe Sections','Design Options');
        $i = 0;
        foreach($line_items AS $li) {
            if($li == 'User\'s Information') {
                if($action == 'order_edit') {
                    $out .= '
                    <li class="inactive" onclick="options._setTab('.$i.')">'.$li.'</li>';
                    $i++;
                }
            } else {
                $out .= '
                <li class="inactive" onclick="options._setTab('.$i.')">'.$li.'</li>';
                $i++;
            }
        }
        $out .= '
    </ul>
</div>
<form id="order_edit" name="order_edit">
    <input type="hidden" name="sender" value="1">
    <input type="hidden" name="action" value="'.$function.'">';
if($action == 'order_edit') {
    $out .= '
    <input type="hidden" name="added_by_type" value="'.$added_by_type.'">
    <input type="hidden" name="id" value="'.$order_id.'">
    <input type="hidden" name="modified_by_id" value="'.$_SESSION['user']->id.'">';
} elseif($action == 'order_add') {
    $out .= '
    <input type="hidden" name="added_by_type" value="1">
    <input type="hidden" name="added_by_id" value="'.$_SESSION['user']->id.'">';
}
    $out .= '
    <input type="hidden" name="date_modified" value="'.$mod_date.'">
<div id="order_options_container">
<!--
*
* General Order Options
*
/-->
<div id="general" class="orderOption">
    <table>
        <tr>
            <th colspan="6">General Order Options</th>
        </tr>
        <tr>
            <td class="formLabel odd b-left b-bottom">Order added on</td>
            <td class="formInput odd b-bottom">'.$date_added.'</td>
            <td class="formLabel odd b-right b-bottom">&nbsp;</td>
            <td class="formLabel odd b-bottom">Order modified on</td>
            <td class="formInput odd b-bottom">'.$date_modified.'</td>
            <td class="formLabel odd b-right b-bottom">&nbsp;</td>
        </tr>
        <tr>
            <td class="formLabel even b-left b-bottom">Order added by</td>
            <td class="formInput even b-bottom"><a href="people_edit.php?action=user_edit&amp;id='.$added_by_id.'">'.$added_by_id.'</a></td>
            <td class="formLabel even b-right b-bottom">&nbsp;</td>
            <td class="formLabel even b-bottom">Status: </td>
            <td class="formInput even b-bottom">
                <select name="status" id="status">
                    <option value="-1"> Choose One...</option>';
                    $options = array('0'=>'Inactive','1'=>'Data Entry');
                    foreach($options as $key=>$val) {
                    	$selected = '';
                    	if($status == $key) {
                    		$selected = ' selected="selected"';
                    	}
						$out .= '
					<option value="'.$key.'"'.$selected.'>'.$val.'</option>
                    	';
                    }
                    $out .= '
                </select>
            </td>
            <td class="formLabel even b-right b-bottom"><a href="'.HELP.'order_status.html" title="Order Status" class="lightwindow help" params="lightwindow_width=500,lightwindow_height=300">?</a></td>
        </tr>
        <tr>
            <td class="formLabel odd b-left b-bottom"><span style="color: #F00">*</span>Order Title:</td>
            <td class="formInput odd b-bottom"><input name="title" id="title" value="'.$order_title.'" type="text"></td>
            <td class="formLabel odd b-right b-bottom"><a href="'.HELP.'order_title.html" class="lightwindow help" params="lightwindow_width=500,lightwindow_height=300" params="lightwindow_width=500,lightwindow_height=300"title="Order Title">?</a></td>
            <td class="formLabel odd b-bottom">Order Number:</td>
            <input name="order_number" id="order_number" value="'.$order_number.'" type="hidden">
            <td class="formInput odd b-bottom" style="vertical-align: middle;">'.$order_number.'</td>
            <td class="formLabel odd b-right b-bottom"><a href="'.HELP.'order_number.html" title="Order Number" class="lightwindow help" params="lightwindow_width=500,lightwindow_height=300">?</a></td>
        </tr>
        <tr>
            <td class="formLabel even b-left">Book Title:</td>
            <td class="formInput even" rowspan="3">
               <input name="book_title1" id="book_title1" value="'.$book_title1.'" type="text" maxlength="50"><br />
                <input name="book_title2" id="book_title2" value="'.$book_title2.'" type="text" maxlength="50" style="margin-top: 5px"><br />
                <input name="book_title3" id="book_title3" value="'.$book_title3.'" type="text" maxlength="50" style="margin-top: 5px">
            </td>
            <td class="formLabel even b-right"><a href="'.HELP.'cookbook_title.html" title="Book Title" class="lightwindow help" params="lightwindow_width=500,lightwindow_height=300">?</a></td>
            <td class="formLabel even b-bottom">Book Style:</td>
            <td class="formInput even b-bottom">
                <select name="book_style" id="book_style">';
                	$options = array(' -- ','Soft Cover','Hard Cover','3-Ring Binder');
					for($i=0;$i<count($options);$i++) {
						if($options[$i] == ' -- ') {
							$out .= '
					<option value=""';
						} else {
							$out .= '
					<option value="'.$options[$i].'"';
						}
						if($options[$i] == $book_style) {
							$out .= ' selected="selected"';
						}
						$out .= '>'.$options[$i].'</option>';
					}
                $out .= '
                </select>
            </td>
            <td class="formLabel even b-right b-bottom"><a href="'.HELP.'book_style.html" title="Book Style" class="lightwindow help" params="lightwindow_width=500,lightwindow_height=300">?</a></td>
        </tr>
        <tr>
            <td class="formLabel even b-right">(50 characters each)</td>
            <td class="even b-right">&nbsp;</td>
            <td class="formLabel odd b-bottom"># Books:</td>
            <td class="odd b-bottom"><input name="book_count" id="book_count" size="7" value="'.$book_count.'" type="text"></td>
            <td class="formSubmit odd b-right b-bottom"><a href="'.HELP.'book_count.html" title="Number of Books" class="lightwindow help" params="lightwindow_width=500,lightwindow_height=300">?</a></td>
        </tr>
        <tr>
            <td  class="even b-left b-bottom">&nbsp;</td>
            <td class="even b-right b-bottom">&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
        </tr>
        <tr>
            <th colspan="6">Organization Options</th>
        </tr>
        <tr>
            <td class="formLabel odd b-left b-bottom">Organization Type:</td>
            <td class="formInput odd b-bottom">
                <select name="organization_type" id="organization_type">
                    <option value="0"> Choose One...</option>';
                    if($organization) {
                        $organization_name = $organization->name;
                        $organization_type = $organization->type;
                    } else {
                        $organization_name = '';
                        $organization_type = '';
                    }
                    $type_res = $no->sendAndGetMany('SELECT DISTINCT(type) FROM Organizations WHERE status="1"');
                    foreach($type_res AS $t) {
                        if($t->type != '') {
                            if($t->type != 'other') {
                                if($t->type == $organization_type) {
                                    $selected = " selected='selected'";
                                } else {
                                    $selected = '';
                                }
                            $out .= '
                    <option value="'.$t->type.'"'.$selected.'>'.$t->type.'</option>';
                            }
                        }
                    }
					$out .= '
					<option value="other">Other</option>
                </select>
            </td>
            <td class="odd b-right b-bottom">&nbsp;</td>
            <td class="formLabel odd b-bottom">Organization Name:</td>
            <td class="formInput odd b-bottom"><input name="organization_name" id="organization_name" value="'; if($action != 'order_add') { $out .= $organization_name; } $out .= '" type="text"></td>
            <td class="odd b-right b-bottom">&nbsp;</td>
        </tr>
    </table>
</div>';
if($action != 'order_add') {
$out .= '<!--
*
* Order Users\' Options
*
/-->
<div id="users" class="orderOption" style="display: none;">
    <table>
        <tr>
            <th colspan="2">Current Users for Order #1001_13 &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="people_edit.php?action=user_add">Add User</a>&nbsp;<a href="people_list.php">Edit Users</a></th>
        </tr>';
            if(!$demo) {
				$users = getUsers($order_id);
				$count = 0;
				foreach($users AS $u) {
					$class = 'even';
					if($count%2 == 0) {
						$class = 'odd';
					}
					$out .= '
		<tr>
			<input name="person_1" value="10" type="hidden" />
			<input type="hidden" name="person_'.($count + 1).'" value="'.$u->id.'">
			<td class="formInput '.$class.' b-bottom">'.htmlspecialchars(stripslashes(urldecode($u->first_name)),ENT_QUOTES,'utf-8',false).' '.htmlspecialchars(stripslashes(urldecode($u->last_name))).'</td>
			<td class="formInput '.$class.' b-bottom">';
					$options = array('0'=>' -- ','1'=>'Demo','2'=>'Contributor','3'=>'Committee','4'=>'Cochairperson','5'=>'Chairperson');
					foreach($options as $k=>$o) {
						if($u->order_level == $k) {
							$out .= $o;
						}
                    }
					$out .= '
				</select>
			</td>
		</tr>';
					$count++;
				}
			} else {
				$out .= '
		<tr>
			<td>
				<input type="hidden" name="person_1" value="'.$_SESSION['user']->id.'">
				<input type="hidden" name="personlevel_1" value="5">
				<span style="line-height: 24px">'.htmlspecialchars(stripslashes(urldecode($_SESSION['user']->first_name))).' '.htmlspecialchars(stripslashes(urldecode($_SESSION['user']->last_name))).'</span>
			</td>
		</tr>';
			}
	$out .= '
	</table>
</div>';
}
$out .= '
<!--
*
* Page Options
*
/-->
<div id="page" class="orderOption" style="display: none;">
    <table>
        <tr>
            <th colspan="3">Custom Pages Options</th>
        </tr>';
        $yes_checked = '';
        $no_checked = '';
        if($nutritionals == 'yes') {
            $yes_checked = ' checked="checked"';
        } else {
            $no_checked  = ' checked="checked"';
        }
        $out .= '
        <tr>
            <td class="formLabel odd b-left b-bottom">FREE Nutritional Information Pages?</td>
            <td class="formInput odd b-bottom"><input name="nutritionals" value="yes" id="nutritionals_0" type="radio"'.$yes_checked.'>Yes  &nbsp;<input name="nutritionals" value="no" id="nutritionals_1" type="radio"'.$no_checked.'>No</td>
            <td class="odd b-right b-bottom"><a href="'.HELP.'nutritionals.html" title="Nutritional Information Pages" class="lightwindow help" params="lightwindow_width=500,lightwindow_height=300">?</a></td>
        </tr>';
        $yes_checked = '';
        $no_checked = '';
        if($use_subcategories == 'yes') {
            $yes_checked = ' checked="checked"';
        } else {
            $no_checked  = ' checked="checked"';
        }
        $out .= '
        <tr>
            <td class="formLabel even b-left b-bottom">Add subcategories to Recipe Sections?</td>';
			$out .= '<td class="formInput even b-bottom designeroption_off"><input class="usub" id="use_subcategories_yes" name="use_subcategories" value="yes" type="radio"'.$yes_checked.'>Yes &nbsp;<input class="usub" id="use_subcategories_no" name="use_subcategories" value="no" type="radio"'.$no_checked.'> No</td>';
			if($use_subcategories == 'yes') {
				$class = 'designeroption_on';
			} else {
				$class = 'designeroption_off';
			}
            $out .= '<td class="even b-right b-bottom '.$class.'"><a href="'.HELP.'use_subcategories.html" title="Subcategories" class="lightwindow help" params="lightwindow_width=500,lightwindow_height=300">?</a></td>
        </tr>';
        $yes_checked = '';
        $no_checked = '';
        if($contributors == 'yes') {
            $yes_checked = ' checked="checked"';
        } else {
            $no_checked  = ' checked="checked"';
        }
        $out .= '
        <tr>
            <td class="formLabel even b-left b-bottom" style="vertical-align: top; padding-top: 5px">Add a contributor index page to the cookbook?</td>
            <td class="formInput even b-bottom"><input name="contributors" value="yes" id="contributors_0" type="radio"'.$yes_checked.'>Yes &nbsp;<input name="contributors" value="no" id="contributors_1" type="radio"'.$no_checked.'>No</td>
            <td class="even b-right b-bottom"><a href="'.HELP.'contributors_index.html" title="Contributors Page" class="lightwindow help" params="lightwindow_width=500,lightwindow_height=300">?</a></td>
        </tr>
        <tr>
            <td class="formLabel odd b-left b-bottom" style="vertical-align: top; padding-top: 5px">Recipe index type:</td>
            <td class="formInput odd b-bottom" style="vertical-align: top">
                <select name="order_index_by" id="order_index_by">
                    <option value=""> Choose one...</option>';
                $options = array('0'=>' -- ','alphabetical'=>'Alphabetical','as entered'=>'As Entered');
                foreach($options as $key=>$val) {
                    $out .= '<option value="'.$key.'"';
                    if($key == $order_index_by) {
                        $out .= ' selected="selected"';
                    }
                    $out .= '>'.$val.'</option>';
                }
                $out .= '
                </select>
            </td>
            <td class="odd b-right b-bottom"><a href="'.HELP.'order_index_by.html" title="Recipe Index" class="lightwindow help" params="lightwindow_width=500,lightwindow_height=300">?</a></td>
        </tr>';
        $yes_checked = '';
        $no_checked = '';
        if($order_form_address1) {
            $yes_checked = ' checked="checked"';
        } else {
            $no_checked  = ' checked="checked"';
        }
        $out .= '
        <tr>
            <td class="formLabel even b-left b-bottom">Add an order form to the back of the cookbook?</td>
            <td class="formInput even b-bottom"><input class="oform" name="order_form" value="yes" id="order_form_yes" type="radio"'.$yes_checked.'> Yes<input class="oform" name="order_form" value="no" id="order_form_no" type="radio"'.$no_checked.'> No</td>
            <td class="even b-right b-bottom"><a href="'.HELP.'order_form.html" title="Order Form" class="lightwindow help" params="lightwindow_width=500,lightwindow_height=300">?</a></td>
        </tr>';
        $display = ' style="display: none"';
        if($order_form_address1) {
            $display = '';
        }
        $out .= '
        <tr id="order_form_tr"'.$display.'>
            <td colspan="3" class="even b-left b-right b-bottom">
            <table style="width: 600px">
                <tr>
                    <td class="formLabel">Name</td>
                    <td colspan="3" class="formInput"><input name="order_form_name" id="order_form_name" value="'.$order_form_name.'" type="text"></td>
                </tr>
                <tr>
                    <td class="formLabel">Address 1:</td>
                    <td class="formInput"><input name="order_form_address1" id="order_form_address1" value="'.$order_form_address1.'" type="text"></td>
                    <td class="formLabel">Address 2:</td>
                    <td class="formInput"><input name="order_form_address2" id="order_form_address2" value="'.$order_form_address2.'" type="text"></td>
                </tr>
                <tr>
                    <td class="formLabel">City:</td>
                    <td class="formInput"><input name="order_form_city" id="order_form_city" value="'.$order_form_city.'" type="text"></td>
                    <td class="formLabel">State/Zip:</td>
                    <td class="formInput">
                        <input name="order_form_state" id="order_form_state" size="15" value="'.$order_form_state.'" type="text"> <input name="order_form_zip" id="order_form_zip" size="7" value="'.$order_form_zip.'" type="text">
                    </td>
                </tr>
                <tr>
                    <td class="formLabel">Retail Price:</td>
                    <td class="formInput"><input name="order_form_retail" id="order_form_retail" value="'.$order_form_retail.'" type="text" maxlength="5"></td>
                    <td class="formLabel">Shipping Fee:</td>
                    <td class="formInput"><input name="order_form_shipping" id="order_form_shipping" value="'.$order_form_shipping.'" type="text" maxlength="5"></td>
                </tr>
            </table>
            </td>
        </tr>
    </table>
</div>
<!--
*
* Recipe Section Options
*
/-->
<div id="sections" class="orderOption" style="display: none;">
    <table>
        <tr>
            <th>Recipe Sections</th>
        </tr>
        <tr>
            <td>';
            $name = 'recipe_categories';
            if(!$use_subcategories == 'yes') {
                $subcategories = '';
            }
            $data = new stdClass();
            ///print_r($categories);
            $data->categories = $categories->categories;
            $templates = '{"templates":[';
            $templates .= '{"name":"list_parent","template":"'.TEMPLATES.'categories_list.tpl"},{"name":"categories","template":"'.TEMPLATES.'category.tpl"}';
            if($use_subcategories == 'yes') {
                $data->subcategories = $subcategories->subcategories;
                $templates .= ',{"name":"subcategories","template":"'.TEMPLATES.'subcategory.tpl"}';
            }
            $templates .= ']}';
            $ol = new OrderedList($templates);
            $res = $ol->_orderedlist($data,$name,$tabindex);
            $out .= $res[0];
            $tabindex = $res[1];
            $out .= '
            </td>
        </tr>
        <tr>
            <td>&nbsp;</td>
        </tr>
    </table>
</div>
<!--
*
* Recipe Options
*
/-->
<div id="design" class="orderOption" style="display: none;">
    <table>
        <tr>
            <th colspan="6">Design Options</th>
        </tr>
        <tr>
            <td class="formLabel odd b-left b-bottom" style="width: 150px;">Order Recipes: </td>
            <td class="formInput odd b-bottom">
                <select name="order_recipes_by">';
                $options = array('0'=>' -- ','alpha'=>'by Alphabet','custom'=>'Custom Order');
                foreach($options AS $key=>$val) {
                    if($order_recipes_by == $key) {
                        $selected = " selected='selected'";
                    } else {
                        $selected = "";
                    }
                    $out .= "<option value='".$key."'".$selected.">".$val."</option>";
                }
                $out .= '
                </select>
            </td>
            <td class="odd b-right b-bottom" style="width: 200px;"><a href="'.HELP.'order_recipes_by.html" title="Organize Recipes" class="lightwindow help" params="lightwindow_width=500,lightwindow_height=300">?</a></td>
            <! ------------------------------------------- />
            <td class="formLabel odd b-bottom" style="width: 210px;">Recipes Continued:<br /><span style="font-size: 0.75em">(Recipes may continue across pages)</span></td>';
            $checked = '';
            if($recipes_continued == 'yes') {
            	$checked = ' checked="checked"';
			}
            $out .= '<td class="formInput odd b-bottom" style="width: 150px;">
            	<input type="radio" name="recipes_continued" id="recipes_continued_yes"'.$checked.' value="yes"/>
            </td>';
            $out .= '
            <! ------------------------------------------- />
            <td class="odd b-right b-bottom"><a href="'.HELP.'recipes_continued.html" title="Recipes Continued" class="lightwindow help" style="margin-right: 30px" params="lightwindow_width=500,lightwindow_height=300">?</a></td>
		</tr>
		<tr>
			<td class="formLabel even b-bottom">Allow Notes:</td>
            <td class="formInput even b-bottom designeroption_off">';
            $radio_set = array('yes'=>'Yes','no'=>'No');
            if(!$allow_notes) {
                $allow_notes = 'no';
            }
            foreach($radio_set AS $key=>$val) {
                if($key == $allow_notes) {
                    $checked = "checked='checked'";
                } else {
                    $checked = '';
                }
                $out .= "<input type='radio'".$checked." name='allow_notes' id='allow_notes_".$key."' value='".$key."'>&nbsp;".$val."&nbsp;";
            }
            if($allow_notes == 'yes') {
				$class = 'designeroption_on';
			} else {
				$class = 'designeroption_off';
			}
            $out .= '</td>
            <td class="even b-right b-bottom '.$class.'"><a href="'.HELP.'recipe_notes.html" title="Allow Recipe Notes" class="lightwindow help" params="lightwindow_width=500,lightwindow_height=300">?</a></td>
            <! ------------------------------------------- />
            <td class="formLabel even b-left b-bottom">Recipes Not Continued:<br /><span style="font-size: 0.75em">(Recipes will not continue to another page)</span></td>';
            $checked = '';
            $class = 'designeroption_off';
            if($recipes_continued == 'no') {
            	$checked = ' checked="checked"';
			}
            $out .= '<td class="formInput even '.$class.'" style="width: 200px;">
            	<input type="radio" name="recipes_continued" id="recipes_continued_no"'.$checked.' value="no"/>
            </td>';
            if($recipes_continued == 'no') {
				$class = 'designeroption_on';
			}
           $out .= '
		   <td class="formLabel even b-left b-bottom '.$class.'">&nbsp;</td>
           <! ------------------------------------------- />
           </tr>
           <!--<tr>
            <td class="formLabel odd">Design Type:</td>
            <td class="formInput odd b-bottom">
                <select name="design_type" id="design_type">';
                $options = array('0'=>' -- ','pixami'=>'Use Pixami','user_custom'=>'Customer Custom Design','cpi_custom'=>'CPI Custom Design');
                foreach($options AS $o=>$v) {
                    $out .= '<option value="'.$o.'"';
                    if($o == $design_type) {
                        $out .= ' selected="selected" ';
                    }
                    $out .= '>'.$v.'</option>';
                }
                $out .= '
                </select>
            </td>
            <td class="odd b-right b-bottom"><a href="'.HELP.'design_type.html" title="Design Type" class="lightwindow help" params="lightwindow_width=500,lightwindow_height=300">?</a></td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
        </tr>/-->
        <tr>
            <td class="formLabel odd b-left b-bottom">Page Fillers:</td>
            <td class="formInput odd b-bottom designeroption_off">';
            $radio_set = array('yes'=>'Yes','no'=>'No');
            if(!$use_fillers) {
                $use_fillers = 'no';
            }
            $disabled = '';
            if($recipes_continued == "yes") {
                $disabled = " disabled='disabled'";
                $use_fillers = 'no';
            }
            foreach($radio_set AS $key=>$val) {
                if($use_fillers == $key) {
                    $checked = "checked='checked'";
                } else {
                    $checked = '';
                }
                $out .= "<input type='radio'".$checked." name='use_fillers' id='use_fillers_".$key."' value='".$key."'".$disabled.">&nbsp;".$val."&nbsp;";
            }
            if($use_fillers == 'yes') {
				$class = 'designeroption_on';
			} else {
				$class = 'designeroption_off';
			}
			$out .= '</td>
            <td class="odd b-right b-bottom '.$class.'"><a href="'.HELP.'use_fillers.html" title="Page Fillers" class="lightwindow help" params="lightwindow_width=500,lightwindow_height=300">?</a></td>
            <td class="formLabel odd b-bottom">Filler Type:</td>
            <td class="formInput odd b-bottom">';
                $disabled = '';
                if($use_fillers != 'yes') {
                    $disabled .= 'disabled="disabled" class="disabled"';
                }
                $out .= '
                <select name="filler_type" id="filler_type"'.$disabled.'>
                    <option value="0" selected="selected"> Choose one...</option>';
                    $out .= ">";
                    $options = array('text_fillers'=>'Text Fillers','image_fillers'=>'Image Fillers');
                    foreach($options AS $o=>$v) {
                        $out .= '
                    <option value="'.$o.'"';
                        if($o == $filler_type) {
                            $out .= ' selected="selected" ';
                        }
                        $out .= '>'.$v.'</option>';
                    }
                $out .= '
                </select>
            </td>
            <td class="odd b-right b-bottom">&nbsp;</td>
        </tr>
        <tr>
            <td class="formLabel even b-left b-bottom">Filler Set:</td>
            <td class="formInput even b-bottom" id="filler_set_td" colspan="2">
            ';
                    $disabled = (string) '';
                    $options = (string) '';
                    if($use_fillers == 'no') {
                        $disabled .= " disabled='disabled' class='disabled'>";
                    } else {
                        $options = '<option value=""> Choose one...</option>';
                        $thefile = simplexml_load_file(DATA.'xml/filler_sets.xml');
                        $data_set = json_encode($thefile);
                        $array = json_decode($data_set,TRUE);
                        $filler_sets = $array[$filler_type];
                        for($f=0;$f<count($filler_sets['set']);$f++) {
                            $set = $filler_sets['set'][$f];
                            $set_name = $set['@attributes']['name'];
                            if($filler_set == $set_name) {
                                $selected = " selected=\"selected\"";
                            } else {
                                $selected = '';
                            }
                            $options .= "
                    <option".$selected." value='".$set_name."'>".$set_name."</option>";
                        }
                    }
                $out .= '
                <select name="filler_set" id="filler_set"'.$disabled.'>'.$options.'</select>
            	<a href="#" onclick="popWin(\'filler_sets\')" class="help">Show Filler Sets</a></td>
            <td class="formLabel even b-bottom">Use Icons: </td>
            <td class="formInput even b-bottom designeroption_off">';
            $radio_set = array('yes'=>'Yes','no'=>'No');
            foreach($radio_set AS $key=>$val) {
                if($key == $use_icons) {
                    $checked = "checked='checked'";
                } else {
                    $checked = '';
                }
                $out .= "<input type='radio'".$checked." name='use_icons' id='use_icons_".$key."' value='".$key."'>&nbsp;".$val."&nbsp;";
            }
            if($use_icons == 'yes') {
				$class = 'designeroption_on';
			} else {
				$class = 'designeroption_off';
			}
            $out .= '</td>
             <td class="even b-right b-bottom '.$class.'"><a href="'.HELP.'recipe_icons.html" title="Use Recipe Icons" class="lightwindow help" params="lightwindow_width=500,lightwindow_height=300">?</a></td>
		</tr>
	</table>
	<div id="recipe_formats">
      <div id="format_slider">
          <div id="formats">';
          foreach($format_array->format AS $a) {
            $checked = '';
            $class = '';
            if($a->name == 'CentSaver') {
            	if($recipe_format == 'CentSaver') {
	            	$class = 'centsaver_on';
            	} else {
	            	$class = 'centsaver_off';
            	}
			} else {
				$add_exp = array('Premiere','Fanciful','Casual','Black Tie');
                foreach($add_exp AS $e) {
                    if($a->name == $e) {
                    	$class = 'designeroption_off';
						if($e == $recipe_format) {
							$class = 'designeroption_on';
						}
						
                    }
                }
				if($a->name == $recipe_format) {
					$checked = 'checked="checked"';
				}
			}
            $out .= '
                <div class="format">
                    <a class="lightwindow" rel="Formats[Formats]" href="'.IMAGES.$a->image1.'" title="'.$a->description.'"><img src="'.IMAGES.$a->thumbnail1.'"></a><a class="lightwindow" rel="Formats[Formats]" href="'.IMAGES.$a->image2.'" title="'.$a->description.'"><img src="'.IMAGES.$a->thumbnail2.'"></a>
                    <p class="'.$class.'">'.$a->description.' <input type="radio" name="recipe_format" class="recipe_format" value="'.$a->name.'" flag="'.$a->flag.'"'.$checked.' /></p>
                </div>';
        }
        $out .= '
         </div>
        </div>
        <div id="format_buttons">
            <button type="button" onclick="previousFormat(); return false;"> < Last Format</button>
            <div id="format_button_spacer"></div>
            <button type="button" onclick="nextFormat(); return false;"> Next Format > </button>
        </div>
    </div>
</div>
</div>';
if(!$demo) {
	if($action != 'order_add') {
		if($_SESSION['user']->order_level >= 4) {
			$out .= '
<div id="order_edit_save">
    <button type="submit" onclick="sendOrder(\''.$function.'\'); return false;">Save Order</button>
</div>
';
		}
	} else {
		$out .= '
<div id="order_edit_save">
    <button type="submit" onclick="sendOrder(\''.$function.'\'); return false;">Save Order</button>
</div>
';
	}
}
$out .= '
</form>';

$header_left = '&nbsp;';
if(substr($name, -1) == 's') {
    $name .= "'";
} else {
    $name .= "'s";
}
$header_middle = '';
if($action == 'order_edit') {
	$header_middle = 'Now Editing '.$order_title;
}
$header_right = "";

require_once(TEMPLATES.'u_orders_header.tpl');
require_once(TEMPLATES.'orders_footer.tpl');
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