<?php

if ( !defined('SRC') ) require_once('../globals.php');
require_once(SERVICES.'BaseService.php');

class OrderPeopleSearch extends BaseService
{
	
	function _search($post) {
		
		/*$start;
		$limit;
		$orderby;
		
		if($get['start']) {
			$start = $get['start'];
		}
		if($get['limit']) {
			$limit = $get['limit'];
		}
		if($get['orderby']) {
			$orderby = $$get['orderby'];
		}*/
		
		$searchby = $post['search_by'];
		$searchfor = $post['search_for'];
		$searchterm = $post['search_term'];
		
		$query = "SELECT People.id, CONCAT(People.first_name, ' ', People.last_name) AS last_name, People.email, Order_People.level AS order_level, Order_People.status FROM People,Order_People WHERE Order_People.order_id='".$post['order_id']."' AND";
		
		$mod = '';
		switch($searchby) {
			case 'is':
				$mod = '=';
				break;
			case 'less':
				$mod = '<';
				break;
			case 'more':
				$mod = '>';
				break;
			case 'not':
				$mod = ' NOT ';
				break;
		}
		
		switch($searchfor) {
			case 'id':
				if($searchby == 'like') {
					$query .= ' People.id LIKE "%'.$searchterm.'%"';
				} else {
					$query .= ' People.id'.$mod.'"'.$searchterm.'"';
				}
				break;
			case 'first_name':
				if($searchby == 'like') {
					$query .= ' People.first_name LIKE "%'.$searchterm.'%"';
				} else {
					$query .= ' People.first_name'.$mod.'"'.$searchterm.'"';
				}
				break;
			case 'last_name':
				if($searchby == 'like') {
					$query .= ' People.last_name LIKE "%'.$searchterm.'%"';
				} else {
					$query .= ' People.last_name'.$mod.'"'.$searchterm.'"';
				}
				break;
			case 'city':
				if($searchby == 'like') {
					$query .= ' People.city LIKE "%'.$searchterm.'%"';
				} else {
					$query .= ' People.city'.$mod.'"'.$searchterm.'"';
				}
				break;
			case 'state':
				if($searchby == 'like') {
					$query .= ' People.state LIKE "%'.$searchterm.'%"';
				} else {
					$query .= ' People.state'.$mod.'"'.$searchterm.'"';
				}
				break;
			case 'login':
				if($searchby == 'like') {
					$query .= ' People.login LIKE "%'.$searchterm.'%"';
				} else {
					$query .= ' People.login'.$mod.'"'.$searchterm.'"';
				}
				break;
			case 'date_added':
				if($searchby == 'like') {
					$query .= ' People.date_added LIKE "%'.$searchterm.'%"';
				} else {
					$query .= ' People.date_added'.$mod.'"'.$searchterm.'"';
				}
				break;
			case 'added_by_id':
				if($searchby == 'like') {
					$query .= ' People.added_by_id LIKE "%'.$searchterm.'%"';
				} else {
					$query .= ' People.added_by_id'.$mod.'"'.$searchterm.'"';
				}
				break;
			case 'status':
				if($searchby == 'like') {
					$query .= ' Order_people.status LIKE "%'.$searchterm.'%"';
				} else {
					$query .= ' Order_people.status'.$mod.'"'.$searchterm.'"';
				}
				break;
		}
		$query .= 'AND People.id=Order_People.person_id';
		$res = $this->sendAndGetMany($query);
		return($res);
		
	}
	
	function _draw($people) {
		$columns = array('ID','Last Name','Email','Level','Status','');
		$levels = array('0'=>'Inactive','1'=>'Demo User','2'=>'Contributor','3'=>'Committee','4'=>'Cochairperson','5'=>'Chairperson');
		$out = '
		<table>
			<tr>';
		foreach($columns as $c) {
			$out .= '<th><a href="#" class="subheaderLink" onclick="setContent(\'people_list\',{mode:\'redirect\',action:\'\',start:\'0\',limit:\'25\',orderby:\''.strtolower(str_replace(' ','_',$c)).'\'}">'.$c.'</a></th>';
		}
		$out .= '
			</tr>';
		foreach($people AS $p) {
			$out .= '
			<tr>';
			$status = '';
			if($p->status == '1') {
			    $status = "Active";
			} else {
			    $status = "Inactive";
			}
			$out .= '
				<td class="listItem">'.$p->id.'</td>';
		    $out .= '
		    	<td class="listItem">'.stripslashes(urldecode($p->first_name)).' '.stripslashes(urldecode($p->last_name)).'
		    	</td>';
		    $out .= '
		    	<td class="listItem">'.stripslashes(urldecode($p->email)).'</td>';
		    foreach($levels AS $k=>$v) {
		        if($k == $p->order_level) {
		            $the_level = $v;
		        }
		    }
		    $out .= '
		    	<td class="listItem">'.$the_level.'</td>';
		    $out .= '
		    	<td class="listItem">'.$status.'</td>';
			$out .= '
				<td class="listItem"><a href="#" onclick="setContent(\'people_edit\',{mode:\'redirect\',action:\'user_edit\',id:\''.$p->id.'\'})" class="listEdit">Edit</a></td>';
			$out .= '
			</tr>';
		}
		$out .= '
		</table>';
		return( $out );
	}
}

$list_name = 'people_search';
$nop = new OrderPeopleSearch();
$people = $nop->_search($_POST,$_GET);
if($people) {
	echo $nop->_draw($people);
} else {
	echo 'There are no records that meet the given search criteria. Please check your search and try again.<br />';
}
?>