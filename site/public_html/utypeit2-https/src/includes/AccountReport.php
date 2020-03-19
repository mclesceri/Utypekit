<?php

ini_set('memory_limit','512M');

if ( !defined('SRC') ) require_once('../globals.php');

require_once(SERVICES.'BaseService.php');
require_once(SERVICES.'Email.php');

require_once(INCLUDES.'ReportCrumb.php');
require_once(INCLUDES.'Elements.php');

class AccountReport
{
	
	var $total;
	var $limit;
	var $lpage;
	
	public function _report($return,$response,$filter=null) {
		/*
		 * ORDER:
		 * Order id, order number, order name, date created, date modified, order status
		 * 
		 * CONTENT:
		 * Date last recipe entered, number of recipes
		 *
		 * PEOPLE:
		 * Contact name, contact info (username, password, phone number, 
		 * email address, postal address, cookbook newsletter signup
		 * 
		 * ORGANIZATION
		 * Organization name, organization type
		 * 
		 */
		$nb = new BaseService();
		
		// set up the base query...
		if(!$filter) {
			// Get all orders...
			//$this->limit = 10;
			//$this->lpage = 0;
			$query = "SELECT * FROM Orders ORDER BY id ASC";
			$all_orders = $nb->sendAndGetMany($query);
			// Now get each order's content...
			foreach($all_orders AS $o) {
				// Get the date of the last recipe entered for this order...
				$query = "SELECT MAX(date_added) AS last_recipe FROM Order_Content WHERE order_id='".$o->id."'";
				$last = $nb->sendAndGetOne($query);
				$o->last_recipe = $last->last_recipe;
				// Get the total number of recipes for this order...
				$query = "SELECT COUNT(id) as total_recipes FROM Order_Content WHERE order_id='".$o->id."'";
				$total_recipes = $nb->sendAndGetOne($query);
				$o->total_recipes = $total_recipes->total_recipes;
				// Get the chairperson associated with this order...
				$query = "SELECT People.* FROM People,Order_People WHERE Order_People.order_id='".$o->id."' AND Order_People.level='5' AND People.id=Order_People.person_id";
				$chairperson = $nb->sendAndGetOne($query);
				$o->chairperson = $chairperson;
				// Get the organizational information for this order...
				$query = "SELECT Organizations.id,Organizations.name,Organizations.type FROM Organizations,Order_Organizations WHERE Order_Organizations.order_id='".$o->id."' AND Organizations.id=Order_Organizations.order_id";
				$organization = $nb->sendAndGetOne($query);
				$o->organization = $organization;
			}
		} else {
			if(isset($filter->limit)) {
				$this->lpage = $filter->lpage;
				$this->limit = $filter->limit;
				$set = ($filter->lpage * $filter->limit);
			}
			if(isset($filter->search_by)) {
				$searchfor = array();
				if(isset($filter->search_for_1)) {
					$searchfor[0] = $filter->search_for_1;
				}
				if(isset($filter->search_for_2)) {
					$searchfor[1] = $filter->search_for_2;
				}
				if($filter->search_by != '') {
					// date_added,added_by_type,last_recipe,total_recipes
					switch($filter->search_by) {
						case 'last_recipe':
							$query = 'SELECT id FROM Orders';
							$query .= " ORDER BY Orders.".$filter->order_by." ASC";
							$res = $nb->sendAndGetMany($query);
							$orders = array();
							foreach($res AS $o) {
								$query = 'SELECT MAX(date_modified) AS UNIX_TIMESTAMP(date_modified) FROM Order_Content WHERE order_id="'.$o->id.'"';
								$subres = $nb->sendAndGetOne($query);
								
								$date_mod = $subres->{'UNIX_TIMESTAMP(date_modified)'};
								
								switch($filter->search_mod) {
									case 'is':
										$searchfor = strtotime($searchfor[0].' 00:00:01');
										if($subres->{'COUNT(id)'} == $searchfor) {
											$orders[] = $o->id;
										}
										break;
									case 'greater':
										$searchfor = strtotime($searchfor[0].' 00:00:01');
										if($subres->{'COUNT(id)'} >= $searchfor) {
											$orders[] = $o->id;
										}
										break;
									case 'less':
										$searchfor = strtotime($searchfor[0].' 00:00:01');
										if($subres->{'COUNT(id)'} <= $searchfor) {
											$orders[] = $o->id;
										}
										break;
									case 'range':
										$searchfor[0] = strtotime($searchfor[0].' 00:00:01');
										$searchfor[1] = strtotime($searchfor[1].' 00:00:01');
										if($subres->{'COUNT(id)'} >= $searchfor[0] && $subres->{'COUNT(id)'} <= $searchfor[1]) {
											$orders[] = $o->id;
										}
										break;
								}
							}
							if(count($orders) > 0) {
								$total = count($orders);
								if(isset($filter->limit)) {
									$orders = array_slice($orders,$set,$filter->limit);
								}
								$all_orders = array();
								foreach($orders AS $o) {
									$query = "SELECT Orders.id";
									if(isset($filter->ORDER)) {
										if($filter->ORDER == 'true') {
											//order number, order name, date created, date modified, order status
											$possible = array('order_number','title','date_added','date_modified','status');
											
											foreach($filter AS $key=>$val) {
												for($i=0;$i<count($possible);$i++) {
													if($key == $possible[$i]) {
														$query .= ',Orders.'.$possible[$i];
													}
												}
											}
										}	
									}
									$query .= " WHERE Orders.id='".$o."'";
									$res = $nb->sendAndGetOne($query);
									$all_orders[] = $o;
								}
								if($all_orders) {
									$all_orders = $this->_assemble($all_orders,$filter);
								}
							}
							break;
						case 'total_recipes':
							$query = 'SELECT id FROM Orders';
							$query .= " ORDER BY Orders.".$filter->order_by." ASC";
							if(isset($filter->limit)) {
								$query .= " LIMIT ".$set.",".$filter->limit;
							}
							$res = $nb->sendAndGetMany($query);
							$orders = array();
							foreach($res AS $o) {
								$query = 'SELECT COUNT(id) FROM Order_Content WHERE order_id="'.$o->id.'"';
								$subres = $nb->sendAndGetOne($query);
								switch($filter->search_mod) {
									case 'is':
										if($subres->{'COUNT(id)'} == $searchfor[0]) {
											$orders[] = $o->id;
										}
										break;
									case 'greater':
										if($subres->{'COUNT(id)'} >= $searchfor[0]) {
											$orders[] = $o->id;
										}
										break;
									case 'less':
										if($subres->{'COUNT(id)'} <= $searchfor[0]) {
											$orders[] = $o->id;
										}
										break;
									case 'range':
										if($subres->{'COUNT(id)'} >= $searchfor[0] && $subres->{'COUNT(id)'} <= $searchfor[1]) {
											$orders[] = $o->id;
										}
										break;
								}
							}
							if(count($orders) > 0) {
								$total = count($orders);
								if(isset($filter->limit)) {
									$orders = array_slice($orders,$set,$limit);
								}
								$all_orders = array();
								foreach($orders AS $o) {
									$query = "SELECT Orders.id";
									if(isset($filter->ORDER)) {
										if($filter->ORDER == 'true') {
											//order number, order name, date created, date modified, order status
											$possible = array('order_number','title','date_added','date_modified','status');
											
											foreach($filter AS $key=>$val) {
												for($i=0;$i<count($possible);$i++) {
													if($key == $possible[$i]) {
														$query .= ',Orders.'.$possible[$i];
													}
												}
											}
										}	
									}
									$query .= " WHERE Orders.id='".$o."'";
									$res = $nb->sendAndGetOne($query);
									$all_orders[] = $o;
								}
								if($all_orders) {
									$all_orders = $this->_assemble($all_orders,$filter);
								}
							}
							break;
						default:
							$query = "SELECT Orders.id";
							if(isset($filter->ORDER)) {
								if($filter->ORDER == 'true') {
									//order number, order name, date created, date modified, order status
									$possible = array('order_number','title','date_added','date_modified','status');
									
									foreach($filter AS $key=>$val) {
										for($i=0;$i<count($possible);$i++) {
											if($key == $possible[$i]) {
												$query .= ',Orders.'.$possible[$i];
											}
										}
									}
								}	
							}
							$query .= " FROM Orders";
							$query .= " WHERE Orders.".$filter->search_by;
							if($filter->search_by == 'date_added') {
								$searchfor[0] = $searchfor[0].' 00:00:01';
								if(isset($searchfor[1])) {
									$searchfor[1] = $searchfor[1].' 00:00:01';
								}
							}
							switch($filter->search_mod) {
								case 'is':
									$query .= "='".$searchfor[0]."'";
									break;
								case 'greater':
									$query .= ">'".$searchfor[0]."'";
									break;
								case 'less':
									$query .= "<'".$searchfor[0]."'";
									break;
								case 'range':
									$query .= " BETWEEN '".$searchfor[0]."' AND '".$searchfor[1]."'";
									break;
							}
							$query .= " ORDER BY Orders.".$filter->order_by." ASC";
							
							$all_orders = $nb->sendAndGetMany($query);
							$total = count($all_orders);
							if(isset($filter->limit)) {
								$all_orders = array_slice($all_orders,$set,$limit);
							}
							if($all_orders) {
								$all_orders = $this->_assemble($all_orders,$filter);
							}
							break;
					}
				} else {
					$query = "SELECT COUNT(id) FROM Orders";
					$res = $nb->sendAndGetOne($query);
					$total = $res->{'COUNT(id)'};
					$this->total = $total;
					
					$query = "SELECT Orders.id";
					if(isset($filter->ORDER)) {
						if($filter->ORDER == 'true') {
							//order number, order name, date created, date modified, order status
							$possible = array('order_number','title','date_added','date_modified','status');
							
							foreach($filter AS $key=>$val) {
								for($i=0;$i<count($possible);$i++) {
									if($key == $possible[$i]) {
										$query .= ',Orders.'.$possible[$i];
									}
								}
							}
						}	
					}
					$query .= " FROM Orders";
					$query .= " ORDER BY Orders.".$filter->order_by." ASC";
					if(isset($filter->limit)) {
						$query .= " LIMIT ".$set.",".$filter->limit;
					}
					$all_orders = $nb->sendAndGetMany($query);
					if($all_orders) {
						$all_orders = $this->_assemble($all_orders,$filter);
					}
				}
			}
		}
		if($all_orders) {
			$out = $this->_draw($return,$all_orders);
			if($response == 'wait') {
				if($out) {
					if($return == 'html') {
						return($out);
					} else {
						return('<a href="'.$out.'">CSV File</a>');
					}
					
				} else {
					return('ERROR');
				}
			} else {
				if($return == 'html') {
					// Send an email with a link to the HTML output...
					$today = new DateTime();
					$report_date = date_format($today, 'm-d-y');
					$fname = 'account_report_'.$report_date.'.html';
					$fpath = DATA.'reports/'.$fname;
					$file = fopen($fpath,'w');
					fwrite($file, $out);
					fclose($file);
					// Force the download
					//return($fpath);
					if (file_exists($fpath)) {
						$base = UTI_URL.'data/reports/';
						$furl = $base.$fname;
						$out = $furl;
						exit;
					}
				}
				
				$ne = new Email();
				$message = "<p>Your report is ready!</p>
								<p>Click <a href=\"".$out."?fno=".date('s')."\">HERE</a> to download the proof file.</p>";
				// vars = array(recipient_email(s),reply_to,sender_email,sender_name,subject,message)
				$vars = array(
					'recipient_email'=>$_SESSION['user']->first_name.' '.$_SESSION['user']->last_name.' <'.$_SESSION['user']->email.'>',
					'reply_to'=>'info@dev.cbp.ctcsdev.com',
					'sender_email'=>'info@dev.cbp.ctcsdev.com',
					'sender_name'=>'UTypeIt Administration Utility',
					'subject'=>'Your Report is Ready',
					'message'=>$message);
				$res = $ne->_mail($vars);
				return(false);
			}
		} else {
			$out = 'There are no orders that match the search criteria provided.';
			return($out);
		}
	}
	
	protected function _assemble($all_orders,$filter) {
		$nb = new BaseService();
		foreach($all_orders AS $o) {
			if(isset($filter->CONTENT)) {
				if($filter->CONTENT == 'true') {
					// Date last recipe entered, number of recipes
					if(isset($filter->last_recipe)) {
						$query = "SELECT MAX(date_added) AS last_recipe FROM Order_Content WHERE order_id='".$o->id."'";
						$last = $nb->sendAndGetOne($query);
						$o->last_recipe = $last->last_recipe;
					}
					if(isset($filter->recipe_count)) {
						$query = "SELECT COUNT(id) FROM Order_Content WHERE order_id='".$o->id."'";
						$total_recipes = $nb->sendAndGetOne($query);
						$o->total_recipes = $total_recipes->{'COUNT(id)'};
					}
					
				}
			}
			if(isset($filter->PEOPLE)) {
				if($filter->PEOPLE == 'true') {
					$possibles = array('first_name','last_name','email','phone','address1','address2','city','state','zip','login','password','meta');
					$query = "SELECT People.id";
					foreach($filter AS $key=>$val) {
						for($i=0;$i<count($possibles);$i++) {
							if($key == $possibles[$i]) {
								$query .= ",People.".$possibles[$i];
							}
						}
					}
					$query .= " FROM People,Order_People WHERE Order_People.order_id='".$o->id."' AND Order_People.level='5' AND People.id=Order_People.person_id";
					$chairperson = $nb->sendAndGetOne($query);
					$o->chairperson = $chairperson;
				}
			}
			if(isset($filter->ORGANIZATION)) {
				if($filter->ORGANIZATION == 'true') {
					// Get the organizational information for this order...
					$query = "SELECT Organizations.id";
					$possibles = array('name','type');
					foreach($filter AS $key=>$val) {
						$sub = explode('_',$key);
						for($i=0;$i<count($possibles);$i++) {
							if($sub[0] == 'organization' && $sub[1] == $possibles[$i]) {
								$query .= ",Organizations.".$possibles[$i];
							}
						}
					}
					$query .= " FROM Organizations,Order_Organizations WHERE Order_Organizations.order_id='".$o->id."' AND Organizations.id=Order_Organizations.order_id";
					$organization = $nb->sendAndGetOne($query);
					$o->organization = $organization;	
				}
			}
		}
		return($all_orders);
	}
	
	protected function _draw($return,$orders) {
	    
	    $this->doc = new Elements();
		
		switch($return) {
			case 'html':
                $report = $this->doc->createElement('div','id=report');
				if(isset($this->limit)) {
					$report->appendChild($this->_breadcrumb());
				}
				foreach($orders AS $o) {
				    $item = $this->doc->createElement('div','class=report_item');
					
					$h4 = $this->doc->createElement('h4','','Order Info');
					$item->appendChild($h4);
					$last_modified;
					$total_recipes;
                    if(isset($o->last_recipe)) {
                    	$dateTime = new dateTime($o->last_recipe);
                    	$time = $dateTime->format('M d, Y H:i:s');
                    	$last_modified = $time;
                    	unset($o->last_recipe);
                    }
                    if(isset($o->total_recipes)) {
                    	$total_recipes = $o->total_recipes;
                    	unset($o->total_recipes);
                    }
					foreach($o AS $key=>$val) {
						if(substr($key,0,4) == 'date') {
							$dateTime = new DateTime($val);
							$time = $dateTime->format('M d, Y H:i:s');
							$val = $time;
						}
						if(substr($key,0,6) == 'status') {
							if($val == '0') {
								$val = $val.':Inactive';
							} elseif($val == '1') {
								$val = $val.':Data Entry';
							} elseif($val == '2') {
								$val = $val.':Editorial';
							} elseif($val == '3') {
								$val = $val.':Customer Review';
							} elseif($val == '4') {
								$val = $val.':Approved';
							} elseif($val == '5') {
								$val = $val.':Proofing';
							} elseif($val == '6') {
								$val = $val.':To Print';
							}
						}
						if($key == 'added_by_type') {
							if($val == '1') {
								$val = 'U-Type-It&trade; Online';
							} else {
								$val = 'CPI Admin';
							}
						}
						if(!is_object($val) && $val) {
							$el = $this->doc->createElement('div','id='.$key);
							$sp = $this->doc->createElement('span','class=label',ucwords(str_replace('_',' ',$key)).': ');
							$el->appendChild($sp);
							$sp = $this->doc->createElement('span','class=val',htmlspecialchars(urldecode($val)));
							$el->appendChild($sp);
						}
						
						$item->appendChild($el);
					}
					
					if($o->chairperson) {
						$h4 = $this->doc->createElement('h4','','Chariperson Info');
                        $item->appendChild($h4);
						foreach($o->chairperson AS $key=>$val) {
							if(substr($key,0,4) == 'date') {
								$dateTime = new DateTime($val);
								$time = $dateTime->format('M d, Y H:i:s');
								$val = $time;
							}
							if(substr($key,0,4) == 'meta') {
								$meta = array();
								$first = explode(',',$val);
								foreach($first AS $m) {
									$sub = explode(':',$m);
									$meta[$sub[0]] = $sub[1];
								}
								if($val) {
									foreach($meta AS $skey=>$sval) {
										if($key == 'newsletter') {
											$el = $this->doc->createElement('div','id='.$skey);
											$sp = $this->doc->createElement('span','class=label',ucwords(str_replace('_',' ',$skey)).': ');
											$el->appendChild($sp);
											$sp = $this->doc->createElement('span','class=val',htmlspecialchars(urldecode($sval)));
											$el->appendChild($sp);
										}
									}
								} else {
									$el = $this->doc->createElement('div','id=newsletter');
									$sp = $this->doc->createElement('span','class=label',ucwords(str_replace('_',' ','newsletter')).': ');
									$el->appendChild($sp);
									$sp = $this->doc->createElement('span','class=val','no');
									$el->appendChild($sp);
								}
							} else {
								$el = $this->doc->createElement('div','id='.$key);
								$sp = $this->doc->createElement('span','class=label',ucwords(str_replace('_',' ',$key)).': ');
								$el->appendChild($sp);
								$sp = $this->doc->createElement('span','class=val',htmlspecialchars(urldecode($val)));
								$el->appendChild($sp);
							}
                            
							$item->appendChild($el);
						}
					}
					
					if($o->organization) {
					    $h4 = $this->doc->createElement('h4','','Organization Info');
                        $item->appendChild($h4);
						foreach($o->organization AS $key=>$val) {
							$el = $this->doc->createElement('div','id='.$key);
						
							$sp = $this->doc->createElement('span','class=label',ucwords(str_replace('_',' ',$key)).': ');
							$el->appendChild($sp);
							$sp = $this->doc->createElement('span','class=val',htmlspecialchars($val));
							$el->appendChild($sp);
							
							$item->appendChild($el);
						}
					}
					if($total_recipes || $last_modified) {
						 $h4 = $this->doc->createElement('h4','','Useage Info');
                        $item->appendChild($h4);
					}
					if($total_recipes) {
						// Display the recipe count
						$el = $this->doc->createElement('div','id=recipe_count');
						
						$sp = $this->doc->createElement('span','class=label',ucwords(str_replace('_',' ','recipe_count')).': ');
						$el->appendChild($sp);
						$sp = $this->doc->createElement('span','class=val',$total_recipes);
						$el->appendChild($sp);
                        $item->appendChild($el);
					}
					if($last_modified) {
						$el = $this->doc->createElement('div','id=last_recipe');
						$sp = $this->doc->createElement('span','class=label',ucwords(str_replace('_',' ','last_mod_date')).': ');
						$el->appendChild($sp);
						$sp = $this->doc->createElement('span','class=val',$last_modified);
						$el->appendChild($sp);
                        $item->appendChild($el);
					}
					$report->appendChild($item);
				}
                $this->doc->appendChild($report);
				return($this->doc->saveHTML());
				break;
			case 'csv':
				
				// Draw the first row, made up of the column names for this report
				// order, person, organization
				$all_possible = array(
		            'id',
		            'title',
		            'date_added',
		            'date_modified',
		            'order_number',
		            'added_by_type',
		            'added_by_id',
		            'status',
		            'last_recipe',
		            'total_recipes',
		            'chairperson-id',
		            'chairperson-organization_id',
		            'chairperson-first_name',
		            'chairperson-last_name',
		            'chairperson-email',
		            'chairperson-phone',
		            'chairperson-cell_phone',
		            'chairperson-address1',
		            'chairperson-address2',
		            'chairperson-city',
		            'chairperson-state',
		            'chairperson-zip',
		            'chairperson-login',
		            'chairperson-password',
		            'chairperson-level',
		            'chairperson-type',
		            'chairperson-meta',
		            'chairperson-date_added',
		            'chairperson-added_by_type',
		            'chairperson-added_by_id',
		            'chairperson-date_modified',
		            'chairperson-status',
		            'organization-id',
		            'organization-name',
		            'organization-type',
		        );
								
				$output = array();
				$output[0] = $all_possible;
				
				// Assemble all the data into rows, matching the order of the first row
				foreach($orders AS $o) {
					$tmp = array();
					for($i=0;$i<count($all_possible);$i++) {
						$assign = '';
						foreach($o AS $key=>$val) {
							if(!is_object($val)) {
								if($all_possible[$i] == $key) {
									$assign = $val;
								}
							} else {
								foreach($val AS $sk=>$sv) {
									if($all_possible[$i] == $key.'-'.$sk) {
										$assign = $sv;
									}
								}
							}
							/**/
						}
						$tmp[$i] = $assign;
					}
					$output[] = $tmp;
				}
				// Draw all successive rows into the csv, making sure to fill in a space where the values are empty
				$today = new DateTime();
				$report_date = date_format($today, 'm-d-y');
				$fname = 'account_report_'.$report_date.'.csv';
				$fpath = DATA.'reports/'.$fname;
				$file = fopen($fpath,'w');
				foreach($output AS $o) {
					fputcsv($file,$o);
				}
				fclose($file);
				
				// Force the download
				//return($fpath);
				if (file_exists($fpath)) {
					$base = UTI_URL.'src/data/reports/';
					$furl = $base.$fname;
					return($furl);
					exit;
				} else {
					return(false);
				}
				
				break;
		}
	}

	protected function _breadcrumb(){
	    $crumb = $this->doc->createElement('div','id=breadcrumb');
		$nbc = new ReportCrumb();
		/*
		 * $attr is a string that receives:
		 * total, start, limit, orderby, lpage, action, select
		 */
		$attr = "total=".$this->total."&limit=".$this->limit.'&lpage='.$this->lpage;
		$out = $nbc->_paginate($attr);
        $tmp = new Elements();
        if($out) {
            $tmp->loadHTML($out);
            $new = $this->doc->importNode($tmp->documentElement->firstChild,true);
            $crumb->appendChild($new);
        }
		return($crumb);
	}
}

//if(isset($_POST['action'])) {
	/*
	 * ORDER:
	 * Order id, order number, order name, date created, date modified, order status
	 * 
	 * CONTENT:
	 * Date last recipe entered, number of recipes
	 *
	 * PEOPLE:
	 * Contact name, contact info (username, password, phone number, 
	 * email address, postal address, cookbook newsletter signup
	 * 
	 * ORGANIZATION
	 * Organization name, organization type
	 * 
	 */
	 $type = $_POST['type'];
	 $response = $_POST['response'];
	 unset($_POST['type']);
	 unset($_POST['response']);
	 unset($_POST['action']);
	 
	 $filter = json_encode($_POST);
	 $filter = json_decode($filter);
	 
	 //echo $type.'<br />';
	 //echo $response.'<br />';
	 //print_r($filter);

	 $nar = new AccountReport();
	 $res = $nar->_report($type,$response,$filter);
	 if($res) {
		echo $res; 
	 }
//}

?>
