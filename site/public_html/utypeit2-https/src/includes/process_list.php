<?
define('FORCE_HTTPS', true);

session_start();

//ini_set('display_errors',1);
//error_reporting(-1);

if ( !defined('SRC') ) require_once('../globals.php');

require_once(SERVICES.'BaseService.php');
require_once(INCLUDES.'OrderedList.php');
require_once(INCLUDES.'Elements.php');
require_once(INCLUDES.'Recipe.php');

$nol = new OrderedList();
$nr = new Recipe();
$ne = new Elements();
$nb = new BaseService();

if(isset($_GET['type']))	{
	$type = $_GET['type'];
}
if(isset($_GET['count'])) {
	$count = $_GET['count'];
} else {
	$count = '';
}
if(isset($_GET['total'])) {
	$total = $_GET['total'];
} else {
	$total = '';
}
if(isset($_GET['parent'])) {
	$parent = $_GET['parent'];
} else {
	$parent = '';
}
if(isset($_GET['value'])) {
	$value = $_GET['value'];
} else {
	$value = '';
}
if(isset($_GET['switch'])) {
	$switch = $_GET['switch'];
}
if(isset($_GET['tabindex'])) {
	$tabindex = $_GET['tabindex'];
}

//print_r($_SESSION['subcategories']);

switch($type) {
	case 'organization':
		$list = $nol->BuildComboList($action,$value,$type);
		echo $list;
		break;
	case 'filler_sets':
        
        $type = $_GET['value'];
		$list = '<option value=""> -- </option>';
        
		if(file_exists(DATA.'xml/filler_sets.xml')) {
			$thefile = simplexml_load_file(DATA.'xml/filler_sets.xml');
			$data_set = json_encode($thefile);
			$array = json_decode($data_set);
			$this_set = $array->{$type}->set;
           foreach($this_set AS $s) {
               $list .= "<option value=\"".$s->{'@attributes'}->name."\">".$s->{'@attributes'}->name."</option>";
           }
		} else {
			$list = 'Error: Data Source Missing';
		}
		
		echo $list;
		break;
    case 'remove_subcategories':
        $order_id = $_SESSION['order_id'];
        $nb = new BaseService();
        $query = "UPDATE Order_Content SET subcategory='0' WHERE order_id='".$order_id."'";
        $nb->sendAndGetOne($query);
        $query = "DELETE FROM Order_Meta WHERE order_id='".$order_id."' AND name='subcategories'";
        $nb->sendAndDelete($query);
        echo "true";
        break;
	case 'subcategory':
		$result = $nol->_section('subcategory',$count,$total,$parent,$value);
		echo $result;
		break;
	case 'category':
		$result = $nol->_section('category',$count,$total,$parent,$value,$switch);
		echo $result;
		break;
	case 'contributor':
		$out = '<div class="contributorListSection" id="contributor" order="'.$count.'">
			<div class="contributorControls" id="contributorControls"><img src="images/blank.png" align="middle">	<img src="images/add_button.png" onclick="contributorListActions(this,\'add\',\'1\')" align="middle" /></div>
			<div class="contributorNameBlock">Name(first, last):<br />
				<input type="text" name="contributor-first-name_'.$count.'" id="contributor-first-name" onfocus="setSecret(this)" tabindex="'.$tabindex++.'" size="10" value="" maxlength="35">
				<input type="text" name="contributor-last-name_'.$count.'" id="contributor-last-name"  onfocus="setSecret(this)" tabindex="'.$tabindex++.'" size="10" value=""  maxlength="35"></div>
				<div class="contributorCreditsBlock">Credits:<br />
					<input type="text" name="contributor-line_1_'.$count.'" id="contributor-line_1" onfocus="setSecret(this)" tabindex="'.$tabindex++.'" value="" size="20"  maxlength="50">
					<input type="text" name="contributor-line_2_'.$count.'" id="contributor-line_2" onfocus="setSecret(this)" tabindex="'.$tabindex++.'" value="" size="20"  maxlength="50">
				</div>
			</div>';
		
		echo $out;
		break;
	case 'recipe_organize':
		//echo $parent.':'.$value.'<br />';
		$category = $parent;
		$_SESSION['org_cat'] = $category;
		$subcategory = $value;
		$_SESSION['org_subcat'] = $subcategory;
		if($_SESSION['general_info']->use_subcategories == 'yes') {
			$query = "SELECT * FROM Order_Content WHERE type='recipe' AND order_id='".$_SESSION['order_id']."' AND category='".$category."' AND subcategory='".$subcategory."' ORDER BY Order_Content.list_order,Order_Content.id ASC";
		} else {
			$query = "SELECT * FROM Order_Content WHERE type='recipe' AND order_id='".$_SESSION['order_id']."' AND category='".$category."' ORDER BY Order_Content.list_order,Order_Content.id ASC";
		}
		
		$recipes = $nb->sendAndGetMany($query);
		
		$recipes_list = array();
		for($o=0;$o<count($recipes);$o++) {
			$recipes_list[$o]['ID'] = $recipes[$o]->id;
			$recipes_list[$o]['Title'] = $recipes[$o]->title;
			$recipes_list[$o]['Order'] = $recipes[$o]->list_order;
			$recipes_list[$o]['Status'] = $recipes[$o]->status;
		}
		
		$total  = count($recipes_list);
		$out = '';
        for($p=0;$p<$total;$p++) {
			$new_order = $p+1;
			$out .= '<div id="row_'.($p+1).'" class="itemRow">';
			foreach($recipes_list[$p] as $e=>$v) {
				$width = 100;
				if($e == 'Status') {
					if($v == -1) {
						$v = 'Unselected';
						$bgcol = 'FF9E9E';
					} elseif($v == 0) {
						$v = 'Inactive';
						$bgcol = 'FF9E9E';
					} elseif($v == 1) {
						$v = 'Data Entry';
						$bgcol = 'FFD59E';
					} elseif($v == 2) {
						$v = 'Editorial';
						$bgcol = 'C6FF9E';
					} elseif($v == 3) {
						$v = 'Approved';
						$bgcol = '8CFF84';
					}
					$out .= '<div class="'.$e.'" style="background-color: #'.$bgcol.'">'.$v.'</div>';
				} elseif($e == 'Order') {
					$order = $p+1;
					$v = '<input type="hidden" name="id_'.$order.'" id="id" value="'.$recipes_list[$p]['ID'].'"><input type="text" name="order_'.$order.'" id="order" value="'.$order.'" class="null" size="2"/>';
					$out .= '<div class="'.$e.'">'.$v.'</div>';
				} else {
					if($e == 'Title') {
						$width = 500;
					}
					$out .= '<div class="'.$e.'" style="width: '.$width.'px">'.htmlspecialchars(urldecode($v)).'</div>';
				}
			}
	        $out .= "<img src=\"".IMAGES."move_button.png\" id=\"handle\">";
			$out .= '</div>';
		}
		
		echo $out;
		break;
	case 'subcat_list':
		$out = '<option value="0"> -- </option>';
		foreach($sub_array AS $s) {
			$sub_sub = explode(':',$s);
			$sub_sub_sub = explode(',',$sub_sub[0]);
			if($sub_sub_sub[1] == $category) {
				$out .= '<option value="'.$sub_sub_sub[0].','.$sub_sub_sub[1].':'.$sub_sub[1].'">'.$sub_sub[1].'</option>';
			}
		}
		echo $out;
		break;
	case 'person_list':
		echo $nol->BuildOneItem('person_part',$count,'',$parent,$value);
		break;
	case 'subcategories_list':
		$subcategories = $_SESSION['subcategories']->subcategories;
		print_r($_SESSION);
        $category = $_GET['parent'];
        $sublist = new Elements();
        $option = $sublist->createElement('option','',' -- ');
        $sublist->appendChild($option);
        foreach($subcategories AS $s) {
            if($s->parent == $category) {
                $option = $sublist->createElement('option','value='.$s->number,stripslashes(urldecode($s->name)));
                $sublist->appendChild($option);
            }
        }
		$out = $sublist->saveHTML();
		echo $out;
		break;
	case 'recipient':
        $demo = false;
        if($_SESSION['order_id'] == 1) {
            $demo = true;
        }
        if(!$demo) {
    		$query = "SELECT People.id,People.first_name,People.last_name FROM People,Order_People WHERE ";
    		if($switch != 0) {
    			$query .= "Order_People.level='".$switch."' AND";
    		}
    		$query .= " Order_People.order_id='".$_SESSION['order_id']."' AND Order_People.person_id=People.id";
    		$list_res = $nb->sendAndGetMany($query);
    		if($list_res) {
    		    $out = new Elements();
    		    if(isset($_GET['value'])) {
    		        if($_GET['value'] == 'multiple') {
    		            $attr_str = 'name=recipient[]&id=recipient&multiple=multiple';
    		        }
    		    } else {
    		        $attr_str = 'name=recipient&id=recipient';
    		    }
    		    $select = $out->createElement('select',$attr_str);
                foreach($list_res AS $p) {
                    $option = $out->createElement('option','value='.$p->id,stripslashes(urldecode($p->first_name))." ".stripslashes(urldecode($p->last_name)));
                    $select->appendChild($option);
                }
                $out->appendChild($select);
                echo $out->saveHTML();
    		} else {
    		    echo "<select name=\"recipient\" id=\"recipient\" disabled=\"disabled\"></select>";
    		}
    	} else {
    	    echo "<select name=\"recipient\" id=\"recipient\" disabled=\"disabled\">
    	       <option value=\"".$_SESSION['user']->id."\">".stripslashes(urldecode($_SESSION['user']->first_name))." ".stripslashes(urldecode($_SESSION['user']->last_name))."</option>
    	    </select>";
    	}
		
		
		break;
}

?>