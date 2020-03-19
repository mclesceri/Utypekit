<?php

session_start();

if(!$_SESSION['login'] == true) {
	header('Location: index.php');
}

require_once('src/globals.php');

include_once(SERVICES.'Orders.php');
include_once(SERVICES.'Cookbook.php');
include_once(SERVICES.'People.php');
$no = new Orders();
$nc = new Cookbook();
$np = new People();

if(isset($_GET['action'])) {
	$action= $_GET['action'];
}
if(isset($_GET['id'])) {
	$id= $_GET['id'];
}

$orderby = 'id';
		
if(isset($_GET['order_by'])) {
	if($_GET['order_by'] == 'added_by') {
		$orderby = 'added_by_id';
	} else {
		$orderby = $_GET['order_by'];
	}
}

$body = '';

switch($action) {
	case 'customers_list':
		$list_title = "Customer List";
		$list_name = $action;
		
		$people = $np->getPeopleList('customers','','',$orderby);
		
		$columns = array('ID','Last Name','Email','Level','Status');
		$list = array();
		for($p=0;$p<count($people);$p++){
			$list[$p]['ID'] = $people[$p]->id;
			$list[$p]['Last Name']= htmlspecialchars(stripslashes(urldecode($people[$p]->first_name))).' '.htmlspecialchars(stripslashes(urldecode($people[$p]->last_name)));
			$list[$p]['Email'] = tripslashes(urldecode($people[$p]->email));
			$level = '';
			switch($people[$p]->order_level) {
				case '0':
					$level = 'Inactive Member';
					break;
				case '1':
					$level = 'Demo Account';
					break;
				case '2':
					$level = 'Contributor';
					break;
				case '3':
					$level = 'Committee Member';
					break;
				case '4':
					$level = 'Cochairperson';
					break;
				case '5':
					$level = 'Chairperson';
					break;
			}
			$list[$p]['Level'] = $level;
            if($people[$p]->status == 0) {
                $status = 'Inactive';
            }  else {
                $status = 'Active';
            }
			$list[$p]['Status'] = $status;
		}
		$body = drawList($list_title,$list_name,$columns,$list);
		break;
	case 'users_list':
		$list_title = "User List";
		$list_name = $action;
		
		$people = $np->getPeopleList('users','','',$orderby);
		
		$list = array();
		$columns = array('ID','Last Name','Email','Level','Status');
		for($p=0;$p<count($people);$p++){
			$list[$p]['ID'] = $people[$p]->id;
			$list[$p]['Last Name']= htmlspecialchars(stripslashes(urldecode($people[$p]->first_name))).' '.htmlspecialchars(stripslashes(urldecode($people[$p]->last_name)));
			$list[$p]['Email'] = htmlspecialchars(stripslashes(urldecode($people[$p]->email)));
			$tl = $people[$p]->level;
			$level = '';
			switch($tl) {
				case '7':
					$level = 'Customer Service';
					break;
				case '8':
					$level = 'Administrator';
					break;
				case '9':
					$level = 'Super User';
					break;
				
			}
			$list[$p]['Level'] = $level;
            if($people[$p]->status == 0) {
                $status = 'Inactive';
            } else {
                $status = 'Active';
            }
            $list[$p]['Status'] = $status;
		}
		$body = drawList($list_title,$list_name,$columns,$list);
		break;
	case 'contractors_list':
		$list_title = "Contractor List";
		$list_name = $action;
		
		$people = $np->getPeopleList('contractors','','',$orderby);
		
		$list = array();
		$columns = array('ID','Last Name','Email','State','Status');
		for($p=0;$p<count($people);$p++){
			$list[$p]['ID'] = $people[$p]->id;
			$list[$p]['Last Name']= htmlspecialchars(stripslashes(urldecode($people[$p]->first_name))).' '.htmlspecialchars(stripslashes(urldecode($people[$p]->last_name)));
			$list[$p]['Email'] = htmlspecialchars(stripslashes(urldecode($people[$p]->email)));
			$list[$p]['State'] = $people[$p]->state;
            if($people[$p]->status == 0) {
                $status = 'Inactive';
            } else {
                $status = 'Active';
            }
            $list[$p]['Status'] = $status;
		}
		$body = drawList($list_title,$list_name,$columns,$list);
		break;
	case 'person':
		$person = $np->getPerson($id);
		$body = drawPerson($person);
		break;
	case 'order_list':
		$list_title = "Orders List";
		$list_name = $action;
		
		$orders = $no->getOrderList('','',$orderby);
		
		$columns = array('ID','Title','Date Added','Order Number','Chairperson','Status');

		$list = array();
		for($o=0;$o<count($orders);$o++) {
			$list[$o]['ID'] = $orders[$o]->id;
			$list[$o]['Title'] = stripslashes(urldecode($orders[$o]->title));
			$add_date = new DateTime($orders[$o]->date_added);
			$list[$o]['Date Added'] = date_format($add_date,'M d, Y');
			$list[$o]['Order Number'] = $orders[$o]->order_number;
			$list[$o]['Chairperson'] = $orders[$o]->chairperson;
            //<option value="2">Editorial</option><option value="3">Customer Review</option><option value="4">Approved</option><option value="5">Proofing</option><option value="6">To Print</option>
            if($orders[$o]->status == 0) {
                $status = 'Inactive';
            } elseif($orders[$o]->status == 1) {
                $status = 'Data Entry';
            } elseif($orders[$o]->status == 2) {
                $status = 'Editorial';
            } elseif($orders[$o]->status == 3) {
                $status = 'Customer&nbsp;Review';
            } elseif($orders[$o]->status == 4) {
                $status = 'Approved';
            } elseif($orders[$o]->status == 5) {
                $status = 'Proofing';
            } elseif($orders[$o]->status == 6) {
                $status = 'To Print';
            }
			$list[$o]['Status'] = $status;
		}
		$body = drawList($list_title,$list_name,$columns,$list);
		break;
    case 'order_people_list':
        $list_title = 'User List for Order #'.$_SESSION['order_number'];
        $list_name = $action;
        
        $order_id = $_SESSION['order_id'];
        $res = $np->getOrderPeople($order_id,'','',$orderby);
        $columns = array('ID','Last Name','Email','Level','Status');
        
        $list = array();
        $o = 0;
        $levels = array('Inactive','Demo','Contributor','Committee','Cochairperson','Chairperson');
        foreach($res AS $p) {
            $list[$o]['ID'] = $p->id;
            $list[$o]['Last Name'] = htmlspecialchars(stripslashes(urldecode($p->first_name))).' '.htmlspecialchars(stripslashes(urldecode($p->last_name)));
            $list[$o]['Email'] = htmlspecialchars(stripslashes(urldecode($p->email)));
            $list[$o]['Level'] = $levels[$p->order_level];
            if($p->status == 0) {
                $status = 'Inactive';
            } else {
                $status = 'Active';
            }
            $list[$o]['Status'] = $status;
            $o++;
        }
        $body = drawList($list_title,$list_name,$columns,$list);
        break;
	case 'recipe_list':
		$list_title = 'Recipe List for Order #'.$_SESSION['order_number'];
		$list_name = $action;
		
		$order_id = $_SESSION['order_id'];
		$res = $nc->getRecipeList($order_id,'','','',$orderby);
		$columns = array('ID','Title','Date Added','Added By','Status');
		
		$recipes = $res;
		
		$list = array();
		for($o=0;$o<count($recipes);$o++) {
			$list[$o]['ID'] = $recipes[$o]->id;
			$list[$o]['Title'] = htmlspecialchars(stripslashes(urldecode($recipes[$o]->title)));
			$add_date = new DateTime($recipes[$o]->date_added);
			$list[$o]['Date Added'] = date_format($add_date,'M d, Y');
			$added_by = $recipes[$o]->added_by_id;
			$res= $np->getPerson($added_by);
			$added_name = htmlspecialchars(stripslashes(urldecode($res->first_name))).' '.htmlspecialchars(stripslashes(urldecode($res->last_name)));
			$list[$o]['Added By'] = $added_name;
            $status = '';
            if($recipes[$o]->status == 0) {
                $status = 'Inactive';
            } elseif($recipes[$o]->status == 1) {
                $status = 'Data Entry';
            } elseif($recipes[$o]->status == 2) {
                $status = 'Editorial';
            } elseif($recipes[$o]->status == 3) {
                $status = 'Approved';
            }
			$list[$o]['Status'] = $status;
		}
		$body = drawList($list_title,$list_name,$columns,$list);
		break;
    case 'reports':
        $list_title = "Report List";
        $list_name = $action;
        
        $columns = array('ID','Order Number','Title','Date Added','Added By Id','Status');
        $orders = $no->sendAndGetMany('SELECT id,order_number,title,date_added,added_by_id,status FROM Orders');
        
        $list = array();
        for($i=0;$i<count($orders);$i++) {
            foreach($orders[$i] AS $key=>$val) {
                if($key == 'date_added') {
                    $date = new DateTime($val);
                    $val = date_format($date, 'M d, Y');
                }
                $list[$i][ucwords(str_replace('_', ' ', $key))] = $val;
            }
        }
        $body = drawList($list_title,$list_name,$columns,$list);
        break;
}

function drawList($list_title,$list_name,$columns,$list) {
	// Draw the header
	$body = '
	<div class="listTitleBar">
	<div class="listTitleBarItem" id="content_head">'.$list_title.'</div>
	<div class="listTitleBarItem" id="print_link"><a href="#" onclick="window.print()">Print List<a></div>
	</div>';
	
	$body .= '
	<table class="listTable" cellpadding="0" cellspacing="0">
	   <tr>';
	   foreach($columns AS $c) {
	       $orderby = strtolower(str_replace(' ','_',$c));
	       $body .= '
	       <td class="listHeader"><a href="'.$_SERVER['PHP_SELF'].'?action='.$list_name.'&order_by='.(strtolower(str_replace(' ','_',$c))).'" class="subheaderLink">'.$c.'</td>';
       }
       $body .= '
       </tr>';	
	   for($p=0;$p<count($list);$p++) {
	       $body .= '
        <tr>';
            $id = $list[$p]['ID'];
            foreach($list[$p] as $e=>$v) {
                $body .= '
            <td class="listItem">'.$v.'</td>';
            }
       }
	   // Draw the close
	   $body .= '
    </table>';
	
	return($body);
}

function drawPerson($person) {
	$body .= '<div style="display: block; font-weight: bold; font-size: 14pt; width: 100%; padding: 4px">'.htmlspecialchars(stripslashes(urldecode($person->first_name))).' '.htmlspecialchars(stripslashes(urldecode($person->last_name))).'</div>';
	$body .= '<div style="display: block; font-size: 12pt; width: 100%; padding: 4px">'.htmlspecialchars(stripslashes(urldecode($person->address1))).'</div>';
	if($person->address2) {
		$body .= '<div style="display: block; font-size: 12pt; width: 100%; padding: 4px">'.htmlspecialchars(stripslashes(urldecode($person->address2))).'</div>';
	}
	$body .= '<div style="display: block; font-size: 12pt; width: 100%; padding: 4px">'.htmlspecialchars(stripslashes(urldecode($person->city))).', '.$person->state.' '.$person->zip.'</div>';
	if($person->email) {
		$body .= '<div style="display: block; font-size: 12pt; width: 100%; padding: 4px">Email: '.htmlspecialchars(stripslashes(urldecode($person->email))).'</div>';
	}
	if($person->phone) {
		$body .= '<div style="display: block; font-size: 12pt; width: 100%; padding: 4px">Phone: '.htmlspecialchars(stripslashes(urldecode($person->phone))).'</div>';
	}
	return( $body );
}

?>
<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>CPI OMS Admin</title>
<script src="<?=U_JS?>prototype.js" type="text/javascript"></script>
<link href="<?=U_CSS?>print_style.css" rel="stylesheet" type="text/css" />
</head>

<body>
<?=$body?>
</body>
</html>