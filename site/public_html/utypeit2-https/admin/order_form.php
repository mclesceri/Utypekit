<?
session_start();

if(!$_SESSION['login'] == true) {
	header('Location: index.php');
}

require_once('../src/globals.php');

$order_id = $_SESSION['order_id'];

require_once(SERVICES.'Orders.php');
require_once(SERVICES.'People.php');

class makeOrderParts {

	
	public function makeOrderPersonsList($order_id) {
		$no = new Orders();
		$np = new People();
		
		$people = $no->getOrderPeople($order_id);
		$out = '';
		for($k=0;$k<count($people);$k++) {
		    if($people[$k]->level > 3 && $people[$k]->level < 6) {
    			$out .= '<div style="display: inline-block; float: left; padding: 4px; margin-bottom: 4px; margin-right: 8px">';
    			$out .= '<span style="font-size: 11px; color: #666666">';
    			if($people[$k]->level == 5) {
    				$out .= 'Chairperson';
    			} elseif($people[$k]->level == 4) {
    				$out .= 'Cochairperson';
    			}
    			$out .= '</span><br />';
    			$out .= $people[$k]->first_name.' '.$people[$k]->last_name.'<br />';
    			$out .= $people[$k]->address1.'<br />';
    			$out .= $people[$k]->address2.'<br />';
    			$out .= $people[$k]->city.', '.$people[$k]->state.' '.$people[$k]->zip.'<br />';
    			if($res->phone) {
    				$out .= $people[$k]->phone.'<br />';
    			}
    			if($res->email) {
    				$out .= $people[$k]->email.'<br />';
    			}
    			$out .= '</div>';
            }
		}
		return( $out );
	}
}
$status_list = 'order_status';
$mod_date = date('Y-m-d H:i:s');

$newOrder = new Orders();
$this_order = $newOrder->getComposedOrder($order_id);
$order = $this_order->order;

// parse out the variables into something useful
// Order Information
$added_by_id = $order->added_by_id;
$added_by_type = $order->added_by_type;

$add_date = new DateTime($order->date_added);
$date_added = $add_date->format('M d, Y');

$modify_date = new DateTime($order->date_modified);
$date_modified = $modify_date->format('M d, Y');

$order_title = $order->title;
$order_number = $order->order_number;
$status = $order->status;

// People Information
$contractor = $this_order->contractor->id;

// Meta Information
$array_a = explode('|',$this_order->general_info);
$array_b = array();
foreach($array_a AS $b) {
	$array_b[substr($b,0,strpos($b,':'))] = substr($b,strpos($b,':')+1);
}
$general_info = $array_b;

$categories = $this_order->categories;
$categories = json_decode($categories);
$categories = $categories->categories;

if(isset($this_order->subcategories)) {
	$subcategories = $this_order->subcategories;
	$subcategories = json_decode($subcategories);
	$subcategories = $subcategories->subcategories;
}

foreach($general_info AS $key=>$val) {
	$$key = urldecode($val);
}

$ordered_list = '';

$cat_list = "<ul class=\"categoryList\">\n";
if(!$categories) {
	die('This order has no categories. This is a fatal error. Please be sure to save categories on the order settings page.');
}
foreach($categories AS $c) {
	$parent = $c->number;	
	
	$cat_list .= '<li>'.stripslashes(urldecode($c->name))."</li>\n";

	if($use_subcategories == 'yes') {
		$cat_list .= "<ul class=\"categoryList\">\n";
		$mysubcats = array();
		$i=0;
		if(count($subcategories) > 0) {
			foreach($subcategories AS $s) {
				// build temp array of subcats for this  cat
				if($s->parent == $parent) {
					$cat_list .= '<li>'.stripslashes(urldecode($s->name))."</li>\n";
				}
			}
			$cat_list .= "</ul>\n";
		}
	}
}
$cat_list .= "</ul>\n";

$newParts = new makeOrderParts();

if($use_subcategories == '') {
	$use_subcategories = 'no';
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>CPI OMS Admin</title>
<script src="<?=A_JS?>prototype.js" type="text/javascript"></script>
<link href="<?=A_CSS?>print_style.css" rel="stylesheet" type="text/css" />
</head>

<body>
<div id="feedback">&nbsp;</div>
<form id="order_form" name="order_form" action="process_order.php" method="POST">
	<input type='hidden' name="id" value="<?=$id?>">
	<input type='hidden' name="order_number" value="<?=$order_number?>">
	<table width="100%" border="0" cellspacing="0" cellpadding="0">
		<tr>
			<td colspan="4" class="formTitle">Order Number: <?=$order_number?></td>
		</tr>
		<tr>
			<td colspan="4">&nbsp;</td>
		</tr>
		<?
		if($_SESSION['user']->level > 6) {
		?>
		<tr>
			<td class="formLabel">Proof:</td>
			<td class="formInput"><input type="radio" name="path" value="SOAPRF" checked="checked"></td>
			<td class="formLabel">Final:</td>
			<td class="formInput"><input type="radio" name="path" value="CBSOAP"></td>
		</tr>
		<?
		} else {
		?>
		<input type='hidden' name="path" value="SOAPRF">
		<?
		}
		?>
		<tr>
			<td colspan="4">&nbsp;</td>
		</tr>
		<tr>
			<td class="formLabel">Order Title: </td>
			<td colspan="3" class="formInput"><?=$order_title?></td>
		</tr>
		<tr>
			<td class="formLabel">Order added: </td>
			<td class="formInput"><?=$date_added?></td>
			<td class="formLabel">Last modified: </td>
			<td class="formInput"><?=$date_modified?></td>
		</tr>
		<tr>
			<td class="formLabel">Added by: </td>
			<td class="formInput"><?=$added_by_id?></td>
			<td class="formLabel">Contractor: </td>
			<td class="formInput">	<?=$contractor?></td>
		</tr>
		<tr>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
		</tr>
		<tr>
			<td class="formLabel">Book Title: </td>
			<td class="formInput"><?=$book_title1?></td>
			<td class="formLabel">Book Style: </td>
			<td class="formInput"><?=$book_style?></select>
			</td>
		</tr>
		<tr>
			<td>&nbsp;</td>
			<td class="formInput"><?=$book_title2?></td>
			<td class="formLabel"># Books:</td>
			<td><?=$book_count?></td>
		</tr>
		<tr>
			<td>&nbsp;</td>
			<td class="formInput"><?=$book_title3?></td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
		</tr>
		<tr>
			<td class="formLabel" style="vertical-align: top; padding-top: 8px">Order Persons:</td>
			<td colspan="3" class="formInput">
				<?php
				echo $newParts->makeOrderPersonsList($order_id);
				?>
			</td>
		</tr>
		<tr>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
		</tr>
		<tr>
			<td colspan="2" class="formSubtitle">Page Options</td>
			<td colspan="2" class="formSubtitle">Section Options</td>
		</tr>
		<tr>
			<td class="formLabel">Nutritionals:</td>
			<td class="formInput"><?=$nutritionals?></td>
			<td class="formLabel">Subcategories:</td>
			<td class="formInput"><?=$use_subcategories?></td>
		</tr>
		<tr>
			<td class="formLabel" style="vertical-align: top; padding-top: 5px">Contributors Page:</td>
			<td class="formInput"><?=$contributors?></td>
			<td colspan="2" rowspan="4" style="vertical-align: top">
				<div id="category_sections">
					<?php
						echo $cat_list;
					?>
				</div>
			</td>
		</tr>
		<tr>
			<td class="formLabel" style="vertical-align: top; padding-top: 5px">Order Index By:</td>
			<td class="formInput" style="vertical-align: top"><?=$order_toc_by?></td>
		</tr>
		<tr>
			<td style="text-align: center"><b>Order Form</b></td>
			<td>&nbsp;</td>
		</tr>
		<tr>
			<td colspan="2" style="vertical-align: top; padding-top: 5px">
			<table width="100%" border="0" cellspacing="0" cellpadding="0">
				<tr>
					<td class="formLabel">Name</td>
					<td class="formInput"><?=$order_form_name?></td>
				</tr>
				<tr>
					<td class="formLabel">Address 1:</td>
					<td class="formInput"><?=$order_form_address1?></td>
				</tr>
				<tr>
					<td class="formLabel">Address 2:</td>
					<td class="formInput"><?=$order_form_address2?></td>
				</tr>
				<tr>
					<td class="formLabel">City:</td>
					<td class="formInput"><?=$order_form_city?></td>
				</tr>
				<tr>
					<td class="formLabel">State/Zip:</td>
					<td class="formInput"><?=$order_form_state?> <?=$order_form_zip?></td>
				</tr>
				<tr>
					<td class="formLabel">Retail Price:</td>
					<td class="formInput"><?=$order_form_retail?></td>
				</tr>
				<tr>
					<td class="formLabel">Shipping Fee:</td>
					<td class="formInput"><?=$order_form_shipping?></td>
				</tr>
			</table>
			</td>
		</tr>
		<tr>
			<td colspan="2" class="formSubtitle">Recipe Options</td>
			<td colspan="2" class="formSubtitle">Design Options</td>
		</tr>
		<tr>
			<td class="formLabel">Recipes Continued:</td>
			<td class="formInput"><?=$recipes_continued?></td>
			<td class="formLabel">Design Type:</td>
			<td class="formInput"><?=$design_type?></td>
		</tr>
		<tr>
			<td class="formLabel">Allow Notes:</td>
			<td class="formInput"><?=$allow_notes?></td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
		</tr>
		<tr>
			<td class="formLabel">Use Icons:</td>
			<td class="formInput"><?=$use_icons?></td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
		</tr>
		<tr>
			<td class="formLabel">Page Fillers:</td>
			<td class="formInput"><?=$use_fillers?></td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
		</tr>
		<tr>
			<td class="formLabel">Filler Type:</td>
			<td class="formInput"><?=$filler_type?></td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
		</tr>
		<tr>
			<td class="formLabel">Filler Set:</td>
			<td class="formInput"><?=$filler_set?></td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
		</tr>
		<tr>
			<td class="formLabel">Recipe Format</td>
			<td><?=$recipe_format?></td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
		</tr>
	</table>
	<div id="design_options" style="display: none">
	<table width="100%" border="0" cellpadding="0" cellspacing="0" id="custom_options">
		<tr>
			<td colspan="2" class="formSubtitle">Cover Details</td>
			<td colspan="2" class="formSubtitle">Divider Details</td>
		</tr>
		<tr>
			<td colspan="2">
			<table width="100%" border="0" cellspacing="0" cellpadding="0">
				<tr>
					<td class="formLabel">Category:</td>
					<td>
						<select name="cover_category" id="cover_category">
						</select>
					</td>
				</tr>
				<tr>
					<td class="formLabel">Type:</td>
					<td>
						<select name="cover_type" id="cover_type">
						</select>
					</td>
				</tr>
				<tr>
					<td class="formLabel">Style:</td>
					<td>
						<select name="cover_style" id="cover_style">
						</select>
					</td>
				</tr>
				<tr>
					<td class="formLabel">Add Lamination:</td>
					<td><input type="checkbox" name="cover_lamination" id="cover_lamination" /></td>
				</tr>
				<tr>
					<td colspan="2">Front Outside:</td>
				</tr>
				<tr>
					<td class="formLabel">Ink Type:</td>
					<td>
						<select name="front_outside_ink_type" id="front_outside_ink_type">
						</select>
					</td>
				</tr>
				<tr>
					<td class="formLabel">Standard Color:</td>
					<td>
						<select name="front_outside_standard_color" id="front_outside_standard_color">
						</select>
					</td>
				</tr>
				<tr>
					<td class="formLabel">Specific Color:</td>
					<td><input type="text" name="front_outside_specific_color" id="front_outside_specific_color" /></td>
				</tr>
				<tr>
					<td class="formLabel">Cover Front:</td>
					<td><select name="front_outside_image_type" id="front_outside_image_type">
					</select></td>
				</tr>
				<tr>
					<td class="formLabel">Cover Text:</td>
					<td><textarea name="front_outside_cover_text" id="front_outside_cover_text" cols="25" rows="3"></textarea></td>
				</tr>
				<tr>
					<td colspan="2">Back Outside</td>
				</tr>
				<tr>
					<td class="formLabel">Ink Type:</td>
					<td>
						<select name="back_outside_ink_type" id="back_outside_ink_type">
						</select>
					</td>
				</tr>
				<tr>
					<td class="formLabel">Standard Color:</td>
					<td>
						<select name="back_outside_standard_color" id="back_outside_standard_color">
						</select>
					</td>
				</tr>
				<tr>
					<td class="formLabel">Specific Color:</td>
					<td><input type="text" name="back_outside_specific_color" id="back_outside_specific_color" /></td>
				</tr>
				<tr>
					<td class="formLabel">Cover Front:</td>
					<td>
						<select name="back_outside_image_type" id="back_outside_image_type">
						</select>
					</td>
				</tr>
				<tr>
					<td class="formLabel">Cover Text:</td>
					<td><textarea name="back_outside_cover_text" id="back_outside_cover_text" cols="25" rows="3"></textarea></td>
				</tr>
				<tr>
					<td colspan="2">Front Inside</td>
				</tr>
				<tr>
					<td class="formLabel">Ink Type:</td>
					<td>
						<select name="front_inside_ink_type" id="front_inside_ink_type">
						</select>
					</td>
				</tr>
				<tr>
					<td class="formLabel">Standard Color:</td>
					<td>
						<select name="front_inside_standard_color" id="front_inside_standard_color">
						</select>
					</td>
				</tr>
				<tr>
					<td class="formLabel">Specific Color:</td>
					<td><input type="text" name="front_inside_specific_color" id="front_inside_specific_color" /></td>
				</tr>
				<tr>
					<td class="formLabel">Cover Front:</td>
					<td>
						<select name="front_inside_image_type" id="front_inside_image_type">
						</select>
					</td>
				</tr>
				<tr>
					<td class="formLabel">Cover Text:</td>
					<td><textarea name="front_inside_cover_text" id="front_inside_cover_text" cols="25" rows="3"></textarea></td>
				</tr>
				<tr>
					<td colspan="2">Back Inside</td>
				</tr>
				<tr>
					<td class="formLabel">Ink Type:</td>
					<td>
						<select name="back_inside_ink_type" id="back_inside_ink_type">
						</select>
					</td>
				</tr>
				<tr>
					<td class="formLabel">Standard Color:</td>
					<td>
						<select name="back_inside_standard_color" id="back_inside_standard_color">
						</select>
					</td>
				</tr>
				<tr>
					<td class="formLabel">Specific Color:</td>
					<td><input type="text" name="back_inside_specific_color" id="back_inside_specific_color" /></td>
				</tr>
				<tr>
					<td class="formLabel">Cover Front:</td>
					<td>
						<select name="back_inside_image_type" id="back_inside_image_type">
						</select>
					</td>
				</tr>
				<tr>
					<td class="formLabel">Cover Text:</td>
					<td><textarea name="back_inside_cover_text" id="back_inside_cover_text" cols="25" rows="3"></textarea></td>
				</tr>
			</table>
			</td>
			<td colspan="2"><table width="100%" border="0" cellspacing="0" cellpadding="0">
				<tr>
					<td class="formLabel">Category:</td>
					<td><select name="divider_category" id="divider_category">
					</select></td>
				</tr>
				<tr>
					<td class="formLabel">Type:</td>
					<td><select name="divider_type" id="divider_type">
					</select></td>
				</tr>
				<tr>
					<td class="formLabel">Style:</td>
					<td><select name="divider_style" id="divider_style">
					</select></td>
				</tr>
				<tr>
					<td class="formLabel">Paper Type:</td>
					<td><select name="divider_paper_type" id="divider_paper_type">
					</select></td>
				</tr>
				<tr>
					<td class="formLabel">Ink Type:</td>
					<td><select name="divider_ink_type" id="divider_ink_type">
					</select></td>
				</tr>
				<tr>
					<td class="formLabel">Standard Color:</td>
					<td><select name="divider_stock_color" id="divider_stock_color">
					</select></td>
				</tr>
				<tr>
					<td class="formLabel">Specific Color:</td>
					<td><input type="text" name="divider_specific_color" id="divider_specific_color" /></td>
				</tr>
				<tr>
					<td class="formLabel">Full Bleed:</td>
					<td><input type="radio" name="divider_full_bleed" value="yes" id="divider_full_bleed_0" />Yes &nbsp;<input type="radio" name="divider_full_bleed" value="no" id="divider_full_bleed_1" />No</td>
				</tr>
			</table>
			</td>
		</tr>
		<tr>
			<td colspan="2" class="formSubtitle">Binding Details</td>
			<td colspan="2" class="formSubtitle">Recipe Pages</td>
		</tr>
		<tr>
			<td colspan="2"><table width="100%" border="0" cellspacing="0" cellpadding="0">
				<tr>
					<td class="formLabel">Imprint on Binder:</td>
					<td><input type="radio" name="binder_imprint" value="yes" id="binder_imprint_0" />Yes &nbsp;<input type="radio" name="binder_imprint" value="no" id="binder_imprint_1" />No</td>
				</tr>
				<tr>
					<td class="formLabel">Imprint Text:</td>
					<td><input type="text" name="binder_text" id="binder_text" /></td>
				</tr>
				<tr>
					<td class="formLabel">Imprint Color:</td>
					<td><input type="text" name="binder_text_color" id="binder_text_color" /></td>
				</tr>
				<tr>
					<td class="formLabel">Binder Color (First Choice):</td>
					<td>
						<select name="binder_color_1" id="binder_color_1">
						</select>
					</td>
				</tr>
				<tr>
					<td class="formLabel">Binder Color (Second Choice):</td>
					<td>
						<select name="binder_color_2" id="binder_color_2">
						</select>
					</td>
				</tr>
			</table></td>
			<td colspan="2"><table width="100%" border="0" cellspacing="0" cellpadding="0">
				<tr>
					<td class="formLabel">Style:</td>
					<td>
						<select name="recipe_style" id="recipe_style">
						</select>
					</td>
				</tr>
				<tr>
					<td class="formLabel">Recipe Order:</td>
					<td>
						<select name="recipe_order" id="recipe_order">
						</select>
					</td>
				</tr>
				<tr>
					<td class="formLabel">Paper Type:</td>
					<td>
						<select name="recipe_paper_type" id="recipe_paper_type">
						</select>
					</td>
				</tr>
				<tr>
					<td class="formLabel">Ink Type:</td>
					<td>
						<select name="recipe_ink_type" id="recipe_ink_type">
						</select>
					</td>
				</tr>
				<tr>
					<td class="formLabel">Standard Color:</td>
					<td>
						<select name="recipe_standard_color" id="recipe_standard_color">
						</select>
					</td>
				</tr>
				<tr>
					<td class="formLabel">Specific Color:</td>
					<td><input type="text" name="recipe_specific_color" id="recipe_specific_color" /></td>
				</tr>
				<tr>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
				</tr>
				<tr>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
				</tr>
				<tr>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
				</tr>
				<tr>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
				</tr>
			</table></td>
		</tr>
		<tr>
			<td colspan="2" class="formSubtitle">Special Pages</td>
			<td colspan="2">&nbsp;</td>
		</tr>
		<tr>
			<td colspan="2">
			<table width="100%" border="0" cellspacing="0" cellpadding="0">
				<tr>
					<td class="formLabel">Color Type:</td>
					<td>
						<select name="special_pages_color_type" id="special_pages_color_type">
						</select>
					</td>
				</tr>
				<tr>
					<td class="formLabel">Standard Color:</td>
					<td>
						<select name="special_pages_standard_color" id="special_pages_standard_color">
						</select>
					</td>
				</tr>
				<tr>
					<td class="formLabel">Specific Color:</td>
					<td><input type="text" name="special_pages_specific_color" id="special_pages_specific_color" /></td>
				</tr>
				<tr>
					<td class="formLabel">Pages:</td>
					<td>&nbsp;</td>
				</tr>
				<tr>
					<td colspan="2"><div id="special_pages"></div></td>
					</tr>
			</table>
			</td>
			<td colspan="2">&nbsp;</td>
		</tr>
	</table>
	</div>
	<table width="100%" border="0" cellpadding="0" cellspacing="0" style="border-top: 1px #333333 solid">
		<tr>
			<td colspan="2">&nbsp;</td>
		</tr>
		<tr>
			<td class="formInput"><a href="#"onclick="window.close()">Close Window</a></td>
			<td class="formRight"><input type="submit"name="save_order" id="save_order" value="Get PDF"></td>
		</tr>
	</table>
</form>