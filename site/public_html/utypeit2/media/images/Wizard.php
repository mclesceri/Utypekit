<?
$DOCROOT  = $_SERVER['DOCUMENT_ROOT'].'/utypeit2/';
$DATAROOT = $DOCROOT.'data/';
$INCLUDES = $DOCROOT.'includes/';
$IMAGES = $DOCROOT.'images/';
$SERVICES = $DOCROOT.'services/';

require_once($SERVICES.'BaseService.php');
require_once($INCLUDES.'HTMLElements.php');
require_once($INCLUDES.'FormElements.php');
require_once($INCLUDES.'DataGrid.php');
require_once($INCLUDES.'OrderedList.php');

class Wizard
{
	var $states = array(
				'-1'=>' -- ',
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
    
    protected function getOrganizationTypesList() {
		$newBase = new BaseService();
		$typelist = $newBase->sendAndGetMany('SELECT DISTINCT(type) FROM Organizations WHERE status="1"');
		$retlist = '0:Select One...|';
		foreach($typelist AS $t) {
			$retlist .= $t->type.':'.$t->type.'|';
		}
		
		$retlist .= "other:Other";
		$retlist = $retlist;
		return( $retlist );
	}
    
    protected function getXMLData($file,$type='') {
		
		if(file_exists('data/'.$file.'.xml')) {
		
			$thefile = simplexml_load_file('data/'.$file.'.xml');
			$data_set = json_encode($thefile);
			$array = json_decode($data_set,TRUE);
			return( $array );
		} else {
			return( null );
		}
		
	}
	
	public function signupWelcome() {
		$filename =  $_SERVER['DOCUMENT_ROOT'].'/utypeit2/data/signup_welcome.html';
		$welcome = fopen($filename,'r');
		$welcome_message = fread($welcome, filesize($filename));
		fclose($welcome);
		return($welcome_message);
	}
	
	public function signupWarning() {
		$filename =  $_SERVER['DOCUMENT_ROOT'].'/utypeit2/data/signup_warning.html';
		$warning = fopen($filename,'r');
		$warning_message = fread($warning, filesize($filename));
		fclose($warning);
		return($warning_message);
	}
	
	public function warningConfirm() {
		$dom = new DOMDocument('1.0');
		
		$organization_types = $this->getOrganizationTypesList();
		
		$element = array(
								'form'=>array(
									array('name'=>'name','value'=>'set_warning'),
									array('name'=>'id','value'=>'set_warning'),
									array('name'=>'method','value'=>'POST')
								),
								'hidden'=>array(
									array('name'=>'action','value'=>'set_warning')
								)
							);
		
		$nf = new FormElements();
		$nt = new HTMLElements();
		
		$form = $nf->_formelements('form',$dom,$element);
		
		$table = $nt->_htmlelements('table',$dom);
		
		$tr = $nt->_htmlelements('tr',$dom);
		
		$element = array('attr'=>'class=formLabel');
		$td = $nt->_htmlelements('td',$dom,$element);
		$element = array('value'=>'I agree to the terms and conditions ');
		$label = $nf->_formelements('label',$dom,$element);
		$td->appendChild($label);
		$tr->appendChild($td);
		
		$element = array('attr'=>'class=formInput');
		$td = $nt->_htmlelements('td',$dom,$element);
		$element = array('attr'=>'type=checkbox&name=agree_to_terms&id=agree_to_terms');
		$input = $nf->_formelements('input',$dom,$element);
		$td->appendChild($input);
		$tr->appendChild($td);
		
		$element = array('attr'=>'&class=formRight');
		$td = $nt->_htmlelements('td',$dom,$element);
		$element = array('attr'=>'type=submit&name=next&id=next&value=Next...');
		$input = $nf->_formelements('input',$dom,$element);
		$td->appendChild($input);
		$tr->appendChild($td);
		
		$table->appendChild($tr);
		$form->appendChild($table);
		$dom->appendChild($form);
		
		return($dom->saveHTML());
		
	}
	
	public function chairpersonRegister() {
		
		$states_list = '';
		$i=0;
		foreach($this->states AS $key=>$val) {
			if($i < count($this->states)-1) {
				$states_list .= $key.':'.$val.'|';
				$i++;
			} else {
				$states_list .= $val.':'.$key;
			}
		}
		
		$person_id = $_SESSION['user']->id;
		$base = new BaseService();
		$person = $base->sendAndGetOne('SELECT * FROM People Where id="'.$person_id.'"');
		
		$dom = new DOMDocument('1.0');
		
		$element = array(
								'form'=>array(
									array('name'=>'name','value'=>'set_chairperson'),
									array('name'=>'id','value'=>'set_chairperson'),
									array('name'=>'method','value'=>'POST')
								),
								'hidden'=>array(
									array('name'=>'action','value'=>'set_chairperson'),
									array('name'=>'chair_id','value'=>$person->id),
									array('name'=>'first_name','value'=>$person->first_name),
									array('name'=>'last_name','value'=>$person->last_name),
									array('name'=>'organization_id','value'=>$person->organization_id)
								)
							);
		
		$nf = new FormElements();
		$nt = new HTMLElements();
		
		$form = $nf->_formelements('form',$dom,$element);
		
		$table = $nt->_htmlelements('table',$dom);
		
		$element = array('value'=>'CHAIRPERSON INFORMATION','attr'=>'colspan=6');
		$th = $nt->_htmlelements('th',$dom,$element);
		$table->appendChild($th);
		
		$tr = $nt->_htmlelements('tr',$dom);
		
		$element = array('attr'=>'class=formHelp');
		$td = $nt->_htmlelements('td',$dom,$element);
		$element = array('value'=>'?','a'=>"href=data/help/chairperson_info.html&rel=ibox&title=Chairperson Info");
		$a = $nt->_htmlelements('a',$dom,$element);
		$td->appendChild($a);
		$tr->appendChild($td);
		
		$table->appendChild($tr);
		
		$tr = $nt->_htmlelements('tr',$dom);
		
		$element = array('attr'=>'class=formLabel');
		$td = $nt->_htmlelements('td',$dom,$element);
		$element = array('value'=>'Name (first/last): ');
		$label = $nf->_formelements('label',$dom,$element);
		$td->appendChild($label);
		$tr->appendChild($td);
		
		$element = array('attr'=>'class=formInput&colspan=2');
		$td = $nt->_htmlelements('td',$dom,$element);
		$element = array('value'=>$person->first_name.' '.$person->last_name);
		$label = $nf->_formelements('label',$dom,$element);
		$td->appendChild($label);
		$tr->appendChild($td);
		
		$element = array('attr'=>'class=formLabel');
		$td = $nt->_htmlelements('td',$dom,$element);
		$element = array('value'=>'Username: ','req'=>'true');
		$label = $nf->_formelements('label',$dom,$element);
		$td->appendChild($label);
		$tr->appendChild($td);
		
		$element = array('attr'=>'class=formInput&colspan=2');
		$td = $nt->_htmlelements('td',$dom,$element);
		$element = array('attr'=>'type=text&name=username&id=username&value='.$person->login);
		$input = $nf->_formelements('input',$dom,$element);
		$td->appendChild($input);
		$tr->appendChild($td);
		
		$table->appendChild($tr);
		
		$tr = $nt->_htmlelements('tr',$dom);
		
		$element = array('attr'=>'class=formLabel');
		$td = $nt->_htmlelements('td',$dom,$element);
		$element = array('value'=>'Email Address: ','req'=>'true');
		$label = $nf->_formelements('label',$dom,$element);
		$td->appendChild($label);
		$tr->appendChild($td);
		
		$element = array('attr'=>'class=formInput&colspan=2');
		$td = $nt->_htmlelements('td',$dom,$element);
		$element = array('attr'=>'type=text&name=email&id=email&value='.$person->email);
		$input = $nf->_formelements('input',$dom,$element);
		$td->appendChild($input);
		$tr->appendChild($td);
		
		$element = array('attr'=>'class=formLabel');
		$td = $nt->_htmlelements('td',$dom,$element);
		$element = array('value'=>'Password: ','req'=>'true');
		$label = $nf->_formelements('label',$dom,$element);
		$td->appendChild($label);
		$tr->appendChild($td);
		
		$element = array('attr'=>'class=formInput&colspan=2');
		$td = $nt->_htmlelements('td',$dom,$element);
		$element = array('attr'=>'type=password&name=password&id=password&value='.$person->password);
		$input = $nf->_formelements('input',$dom,$element);
		$td->appendChild($input);
		$tr->appendChild($td);
		
		$table->appendChild($tr);
		
		$tr = $nt->_htmlelements('tr',$dom);
		
		$element = array('attr'=>'class=formLabel');
		$td = $nt->_htmlelements('td',$dom,$element);
		$element = array('value'=>'Phone: ');
		$label = $nf->_formelements('label',$dom,$element);
		$td->appendChild($label);
		$tr->appendChild($td);
		
		$element = array('attr'=>'class=formInput&colspan=5');
		$td = $nt->_htmlelements('td',$dom,$element);
		$element = array('attr'=>'type=text&name=phone&id=phone');
		$input = $nf->_formelements('input',$dom,$element);
		$td->appendChild($input);
		$tr->appendChild($td);
		
		$table->appendChild($tr);
		
		$tr = $nt->_htmlelements('tr',$dom);
		
		$element = array('attr'=>'class=formLabel');
		$td = $nt->_htmlelements('td',$dom,$element);
		$element = array('value'=>'Address 1: ','req'=>'true');
		$label = $nf->_formelements('label',$dom,$element);
		$td->appendChild($label);
		$tr->appendChild($td);
		
		$element = array('attr'=>'class=formInput&colspan=5');
		$td = $nt->_htmlelements('td',$dom,$element);
		$element = array('attr'=>'type=text&name=address1&id=address1');
		$input = $nf->_formelements('input',$dom,$element);
		$td->appendChild($input);
		$tr->appendChild($td);
		
		$table->appendChild($tr);
		
		$tr = $nt->_htmlelements('tr',$dom);
		
		$element = array('attr'=>'class=formLabel');
		$td = $nt->_htmlelements('td',$dom,$element);
		$element = array('value'=>'Address 2: ');
		$label = $nf->_formelements('label',$dom,$element);
		$td->appendChild($label);
		$tr->appendChild($td);
		
		$element = array('attr'=>'class=formInput&colspan=5');
		$td = $nt->_htmlelements('td',$dom,$element);
		$element = array('attr'=>'type=text&name=address2&id=address2');
		$input = $nf->_formelements('input',$dom,$element);
		$td->appendChild($input);
		$tr->appendChild($td);
		
		$table->appendChild($tr);
		
		$tr = $nt->_htmlelements('tr',$dom);
		
		$element = array('attr'=>'class=formLabel');
		$td = $nt->_htmlelements('td',$dom,$element);
		$element = array('value'=>'City: ','req'=>'true');
		$label = $nf->_formelements('label',$dom,$element);
		$td->appendChild($label);
		$tr->appendChild($td);
		
		$element = array('attr'=>'class=formInput&colspan=5');
		$td = $nt->_htmlelements('td',$dom,$element);
		$element = array('attr'=>'type=text&name=city&id=city');
		$input = $nf->_formelements('input',$dom,$element);
		$td->appendChild($input);
		$tr->appendChild($td);
		
		$table->appendChild($tr);
		
		$tr = $nt->_htmlelements('tr',$dom);
		
		$element = array('attr'=>'class=formLabel');
		$td = $nt->_htmlelements('td',$dom,$element);
		$element = array('value'=>'State/Zip: ','req'=>'true');
		$label = $nf->_formelements('label',$dom,$element);
		$td->appendChild($label);
		$tr->appendChild($td);
		
		$element = array('attr'=>'class=formInput&colspan=5');
		$td = $nt->_htmlelements('td',$dom,$element);
		
		$element = array('attr'=>'name=state&id=state','options'=>$states_list);
		$select = $nf->_formelements('select',$dom,$element);
		$td->appendChild($select);
		
		$element = array('attr'=>'type=text&name=zip&id=zip&size=7');
		$input = $nf->_formelements('input',$dom,$element);
		$td->appendChild($input);
		$tr->appendChild($td);
		
		$table->appendChild($tr);
		
		$tr = $nt->_htmlelements('tr',$dom);
		
		$element = array('attr'=>'&class=formRight&colspan=6');
		$td = $nt->_htmlelements('td',$dom,$element);
		$element = array('attr'=>'type=submit&name=next&id=next&value=Next...');
		$input = $nf->_formelements('input',$dom,$element);
		$td->appendChild($input);
		$tr->appendChild($td);
		
		$table->appendChild($tr);
		
		$form->appendChild($table);
		$dom->appendChild($form);
		
		return($dom->saveHTML());
		
	}

	public function billingRegister() {
		
		$states_list = '';
		$i=0;
		foreach($this->states AS $key=>$val) {
			if($i < count($this->states)-1) {
				$states_list .= $key.':'.$val.'|';
				$i++;
			} else {
				$states_list .= $key.':'.$val;
			}
		}
		
		$tabidx = 0;
		
		$dom = new DOMDocument('1.0');
		
		$element = array(
								'form'=>array(
									array('name'=>'name','value'=>'set_billing'),
									array('name'=>'id','value'=>'set_billing'),
									array('name'=>'method','value'=>'POST')
								),
								'hidden'=>array(
									array('name'=>'action','value'=>'set_billing')
								)
							);
		
		$nf = new FormElements();
		$nt = new HTMLElements();
		
		$form = $nf->_formelements('form',$dom,$element);
		
		$table = $nt->_htmlelements('table',$dom);
		
		$element = array('value'=>'BILLING INFORMATION','attr'=>'colspan=6');
		$th = $nt->_htmlelements('th',$dom,$element);
		$table->appendChild($th);
		
		$tr = $nt->_htmlelements('tr',$dom);
		
		$element = array('attr'=>'class=formLabel&colspan=5');
		$td = $nt->_htmlelements('td',$dom,$element);
		$element = array('value'=>'I want to skip this step for now and come back to it later ');
		$label = $nf->_formelements('label',$dom,$element);
		$td->appendChild($label);
		$tr->appendChild($td);
		
		$element = array('attr'=>'class=formInput&colspan=5');
		$td = $nt->_htmlelements('td',$dom,$element);
		$element = array('attr'=>'type=checkbox&name=skip_billing&id=skip_billing&tabindex=1');
		$input = $nf->_formelements('input',$dom,$element);
		$td->appendChild($input);
		$tr->appendChild($td);
		
		$table->appendChild($tr);
		
		$tr = $nt->_htmlelements('tr',$dom);
		
		$element = array('attr'=>'class=formLabel&colspan=5');
		$td = $nt->_htmlelements('td',$dom,$element);
		$element = array('value'=>'Billing Address is the same as the Chairperson Information ');
		$label = $nf->_formelements('label',$dom,$element);
		$td->appendChild($label);
		$tr->appendChild($td);
		
		$element = array('attr'=>'class=formInput&colspan=5');
		$td = $nt->_htmlelements('td',$dom,$element);
		$element = array('attr'=>'type=checkbox&name=chairperson_forward&id=chairperson_forward&tabindex=2');
		$input = $nf->_formelements('input',$dom,$element);
		$td->appendChild($input);
		$tr->appendChild($td);
		
		$table->appendChild($tr);
		
		$tr = $nt->_htmlelements('tr',$dom);
		
		$element = array('attr'=>'colspan=3&class=formCenter');
		$td = $nt->_htmlelements('td',$dom,$element);
		$element = array('value'=>'Billing Address');
		$h3 = $nt->_htmlelements('h3',$dom,$element);
		$td->appendChild($h3);
		$tr->appendChild($td);
		
		$element = array('attr'=>'colspan=3&class=formCenter');
		$td = $nt->_htmlelements('td',$dom,$element);
		$element = array('value'=>'Shipping Address');
		$h3 = $nt->_htmlelements('h3',$dom,$element);
		$td->appendChild($h3);
		$tr->appendChild($td);
		
		$table->appendChild($tr);
		
		$tr = $nt->_htmlelements('tr',$dom);
		
		$element = array('attr'=>'class=formLabel');
		$td = $nt->_htmlelements('td',$dom,$element);
		$element = array('value'=>'Name: ');
		$label = $nf->_formelements('label',$dom,$element);
		$td->appendChild($label);
		$tr->appendChild($td);
		
		$element = array('attr'=>'class=formInput&colspan=2');
		$td = $nt->_htmlelements('td',$dom,$element);
		$element = array('attr'=>'type=text&name=billing_name&id=billing_name&tabindex=3');
		$input = $nf->_formelements('input',$dom,$element);
		$td->appendChild($input);
		$tr->appendChild($td);
		
		$element = array('attr'=>'class=formLabel');
		$td = $nt->_htmlelements('td',$dom,$element);
		$element = array('value'=>'Name: ');
		$label = $nf->_formelements('label',$dom,$element);
		$td->appendChild($label);
		$tr->appendChild($td);
		
		$element = array('attr'=>'class=formInput&colspan=2');
		$td = $nt->_htmlelements('td',$dom,$element);
		$element = array('attr'=>'type=text&name=shipping_name&id=shipping_name&tabindex=9');
		$input = $nf->_formelements('input',$dom,$element);
		$td->appendChild($input);
		$tr->appendChild($td);
		
		$table->appendChild($tr);
		
		$tr = $nt->_htmlelements('tr',$dom);
		
		$element = array('attr'=>'class=formLabel');
		$td = $nt->_htmlelements('td',$dom,$element);
		$element = array('value'=>'Address1: ');
		$label = $nf->_formelements('label',$dom,$element);
		$td->appendChild($label);
		$tr->appendChild($td);
		
		$element = array('attr'=>'class=formInput&colspan=2');
		$td = $nt->_htmlelements('td',$dom,$element);
		$element = array('attr'=>'type=text&name=billing_address1&id=billing_address1&tabindex=4');
		$input = $nf->_formelements('input',$dom,$element);
		$td->appendChild($input);
		$tr->appendChild($td);
		
		$element = array('attr'=>'class=formLabel');
		$td = $nt->_htmlelements('td',$dom,$element);
		$element = array('value'=>'Address1: ');
		$label = $nf->_formelements('label',$dom,$element);
		$td->appendChild($label);
		$tr->appendChild($td);
		
		$element = array('attr'=>'class=formInput&colspan=2');
		$td = $nt->_htmlelements('td',$dom,$element);
		$element = array('attr'=>'type=text&name=shipping_address1&id=shipping_address1&tabindex=10');
		$input = $nf->_formelements('input',$dom,$element);
		$td->appendChild($input);
		$tr->appendChild($td);
		
		$table->appendChild($tr);
		
		$tr = $nt->_htmlelements('tr',$dom);
		
		$element = array('attr'=>'class=formLabel');
		$td = $nt->_htmlelements('td',$dom,$element);
		$element = array('value'=>'Address2: ');
		$label = $nf->_formelements('label',$dom,$element);
		$td->appendChild($label);
		$tr->appendChild($td);
		
		$element = array('attr'=>'class=formInput&colspan=2');
		$td = $nt->_htmlelements('td',$dom,$element);
		$element = array('attr'=>'type=text&name=billing_address2&id=billing_address2&tabindex=5');
		$input = $nf->_formelements('input',$dom,$element);
		$td->appendChild($input);
		$tr->appendChild($td);
		
		$element = array('attr'=>'class=formLabel');
		$td = $nt->_htmlelements('td',$dom,$element);
		$element = array('value'=>'Address2: ');
		$label = $nf->_formelements('label',$dom,$element);
		$td->appendChild($label);
		$tr->appendChild($td);
		
		$element = array('attr'=>'class=formInput&colspan=2');
		$td = $nt->_htmlelements('td',$dom,$element);
		$element = array('attr'=>'type=text&name=shipping_address2&id=shipping_address2&tabindex=11');
		$input = $nf->_formelements('input',$dom,$element);
		$td->appendChild($input);
		$tr->appendChild($td);
		
		$table->appendChild($tr);
		
		$tr = $nt->_htmlelements('tr',$dom);
		
		$element = array('attr'=>'class=formLabel');
		$td = $nt->_htmlelements('td',$dom,$element);
		$element = array('value'=>'City: ');
		$label = $nf->_formelements('label',$dom,$element);
		$td->appendChild($label);
		$tr->appendChild($td);
		
		$element = array('attr'=>'class=formInput&colspan=2');
		$td = $nt->_htmlelements('td',$dom,$element);
		$element = array('attr'=>'type=text&name=billing_city&id=billing_city&tabindex=6');
		$input = $nf->_formelements('input',$dom,$element);
		$td->appendChild($input);
		$tr->appendChild($td);
		
		$element = array('attr'=>'class=formLabel');
		$td = $nt->_htmlelements('td',$dom,$element);
		$element = array('value'=>'City: ');
		$label = $nf->_formelements('label',$dom,$element);
		$td->appendChild($label);
		$tr->appendChild($td);
		
		$element = array('attr'=>'class=formInput&colspan=2');
		$td = $nt->_htmlelements('td',$dom,$element);
		$element = array('attr'=>'type=text&name=shipping_city&id=shipping_city&tabindex=12');
		$input = $nf->_formelements('input',$dom,$element);
		$td->appendChild($input);
		$tr->appendChild($td);
		
		$table->appendChild($tr);
		
		$tr = $nt->_htmlelements('tr',$dom);
		
		$element = array('attr'=>'class=formLabel');
		$td = $nt->_htmlelements('td',$dom,$element);
		$element = array('value'=>'State/Zip: ');
		$label = $nf->_formelements('label',$dom,$element);
		$td->appendChild($label);
		$tr->appendChild($td);
		
		$element = array('attr'=>'class=formInput&colspan=2');
		$td = $nt->_htmlelements('td',$dom,$element);
		$element = array('attr'=>'name=billing_state&id=billing_state&tabindex=7','options'=>$states_list);
		$select = $nf->_formelements('select',$dom,$element);
		$td->appendChild($select);
		$element = array('attr'=>'type=text&name=billing_zip&id=billing_zip&size=7&tabindex=8');
		$input = $nf->_formelements('input',$dom,$element);
		$td->appendChild($input);
		$tr->appendChild($td);
		
		$element = array('attr'=>'class=formLabel');
		$td = $nt->_htmlelements('td',$dom,$element);
		$element = array('value'=>'State/Zip: ');
		$label = $nf->_formelements('label',$dom,$element);
		$td->appendChild($label);
		$tr->appendChild($td);
		
		$element = array('attr'=>'class=formInput&colspan=2');
		$td = $nt->_htmlelements('td',$dom,$element);
		$element = array('attr'=>'name=shipping_state&id=shipping_state&tabindex=12','options'=>$states_list);
		$select = $nf->_formelements('select',$dom,$element);
		$td->appendChild($select);
		$element = array('attr'=>'type=text&name=shipping_zip&id=shipping_zip&size=7&tabindex=14');
		$input = $nf->_formelements('input',$dom,$element);
		$td->appendChild($input);
		$tr->appendChild($td);
		
		$table->appendChild($tr);
		
		$tr = $nt->_htmlelements('tr',$dom);
		
		$element = array('attr'=>'class=formLabel&colspan=5');
		$td = $nt->_htmlelements('td',$dom,$element);
		$element = array('value'=>'My shipping address is the same as my billing address ');
		$label = $nf->_formelements('label',$dom,$element);
		$td->appendChild($label);
		$tr->appendChild($td);
		
		$element = array('attr'=>'class=formInput&colspan=5');
		$td = $nt->_htmlelements('td',$dom,$element);
		$element = array('attr'=>'type=checkbox&name=billing_forward&id=billing_forward&tabindex=15');
		$input = $nf->_formelements('input',$dom,$element);
		$td->appendChild($input);
		$tr->appendChild($td);
		
		$table->appendChild($tr);
		
		$tr = $nt->_htmlelements('tr',$dom);
		
		$element = array('attr'=>'class=spacer&colspan=6');
		$td = $nt->_htmlelements('td',$dom,$element);
		$tr->appendChild($td);
		
		$table->appendChild($tr);
		
		$tr = $nt->_htmlelements('tr',$dom);
		
		$element = array('attr'=>'class=formLabel&colspan=5');
		$td = $nt->_htmlelements('td',$dom,$element);
		$element = array('value'=>'Add a Cochairperson to this order? ');
		$label = $nf->_formelements('label',$dom,$element);
		$td->appendChild($label);
		$tr->appendChild($td);
		
		$element = array('attr'=>'class=formInput');
		$td = $nt->_htmlelements('td',$dom,$element);
		
		$element = array('attr'=>'type=radio&name=add_cochair&id=add_cochair&checked=checked&value=yes&tabindex=16');
		$input = $nf->_formelements('input',$dom,$element);
		$td->appendChild($input);
		
		$element = array('value'=>' Yes');
		$label = $nf->_formelements('label',$dom,$element);
		$td->appendChild($label);
		
		$element = array('attr'=>'type=radio&name=add_cochair&id=add_cochair&value=no&tabindex=17');
		$input = $nf->_formelements('input',$dom,$element);
		$td->appendChild($input);
		
		$element = array('value'=>' No');
		$label = $nf->_formelements('label',$dom,$element);
		$td->appendChild($label);
		
		$tr->appendChild($td);
		
		$table->appendChild($tr);
		
		$tr = $nt->_htmlelements('tr',$dom);
		
		$element = array('attr'=>'class=formLabel&colspan=5');
		$td = $nt->_htmlelements('td',$dom,$element);
		$element = array('value'=>'Add other users to this order? ');
		$label = $nf->_formelements('label',$dom,$element);
		$td->appendChild($label);
		$tr->appendChild($td);
		
		$element = array('attr'=>'class=formInput');
		$td = $nt->_htmlelements('td',$dom,$element);
		
		$element = array('attr'=>'type=radio&name=add_users&id=add_users&checked=checked&value=yes&tabindex=18');
		$input = $nf->_formelements('input',$dom,$element);
		$td->appendChild($input);
		
		$element = array('value'=>' Yes');
		$label = $nf->_formelements('label',$dom,$element);
		$td->appendChild($label);
		
		$element = array('attr'=>'type=radio&name=add_users&id=add_users&value=no&tabindex=19');
		$input = $nf->_formelements('input',$dom,$element);
		$td->appendChild($input);
		
		$element = array('value'=>' No');
		$label = $nf->_formelements('label',$dom,$element);
		$td->appendChild($label);
		
		$tr->appendChild($td);
		
		$table->appendChild($tr);
		
		$tr = $nt->_htmlelements('tr',$dom);
		
		$element = array('attr'=>'class=spacer&colspan=6');
		$td = $nt->_htmlelements('td',$dom,$element);
		$tr->appendChild($td);
		
		$table->appendChild($tr);
		
		$tr = $nt->_htmlelements('tr',$dom);
		
		$element = array('attr'=>'&class=formRight&colspan=6');
		$td = $nt->_htmlelements('td',$dom,$element);
		$element = array('attr'=>'type=submit&name=next&id=next&value=Next...');
		$input = $nf->_formelements('input',$dom,$element);
		$td->appendChild($input);
		$tr->appendChild($td);
		
		$table->appendChild($tr);
		
		$form->appendChild($table);
		$dom->appendChild($form);
		
		return($dom->saveHTML());
		
	}
	
	public function cochairRegister() {
		
		$states_list = '';
		$i=0;
		foreach($this->states AS $key=>$val) {
			if($i < count($this->states)-1) {
				$states_list .= $val.'|'.$key.',';
				$i++;
			} else {
				$states_list .= $val.'|'.$key;
			}
		}
		
		$dom = new DOMDocument('1.0');
		
		$element = array(
								'form'=>array(
									array('name'=>'name','value'=>'set_cochairperson'),
									array('name'=>'id','value'=>'set_cochairperson'),
									array('name'=>'method','value'=>'POST')
								),
								'hidden'=>array(
									array('name'=>'action','value'=>'set_cochairperson'),
									array('name'=>'organization_id','value'=>$_SESSION['user']->organization_id)
								)
							);
		
		$nf = new FormElements();
		$nt = new HTMLElements();
		
		$form = $nf->_formelements('form',$dom,$element);
		
		$table = $nt->_htmlelements('table',$dom);
		
		$element = array('value'=>'COCHAIRPERSON INFORMATION','attr'=>'colspan=6');
		$th = $nt->_htmlelements('th',$dom,$element);
		$table->appendChild($th);
		
		$tr = $nt->_htmlelements('tr',$dom);
		
		$element = array('attr'=>'class=formHelp&colspan=6');
		$td = $nt->_htmlelements('td',$dom,$element);
		$element = array('value'=>'?','a'=>"href=data/help/cochairperson_info.html&rel=ibox&title=Cochairperson Information");
		$a = $nt->_htmlelements('a',$dom,$element);
		$td->appendChild($a);
		$tr->appendChild($td);
		
		$table->appendChild($tr);
		
		$tr = $nt->_htmlelements('tr',$dom);
		
		$element = array('attr'=>'class=formLabel');
		$td = $nt->_htmlelements('td',$dom,$element);
		$element = array('value'=>'Name (first/last): ','req'=>'true');
		$label = $nf->_formelements('label',$dom,$element);
		$td->appendChild($label);
		$tr->appendChild($td);
		
		$element = array('attr'=>'class=formInput&colspan=2');
		$td = $nt->_htmlelements('td',$dom,$element);
		$element = array('attr'=>'type=text&name=first_name&id=first_name&size=12');
		$input = $nf->_formelements('input',$dom,$element);
		$td->appendChild($input);
		$element = array('attr'=>'type=text&name=last_name&id=last_name&size=12');
		$input = $nf->_formelements('input',$dom,$element);
		$td->appendChild($input);
		$tr->appendChild($td);
		
		$element = array('attr'=>'class=formLabel');
		$td = $nt->_htmlelements('td',$dom,$element);
		$element = array('value'=>'Username: ','req'=>'true');
		$label = $nf->_formelements('label',$dom,$element);
		$td->appendChild($label);
		$tr->appendChild($td);
		
		$element = array('attr'=>'class=formInput&colspan=2');
		$td = $nt->_htmlelements('td',$dom,$element);
		$element = array('attr'=>'type=text&name=username&id=username');
		$input = $nf->_formelements('input',$dom,$element);
		$td->appendChild($input);
		$tr->appendChild($td);
		
		$table->appendChild($tr);
		
		$tr = $nt->_htmlelements('tr',$dom);
		
		$element = array('attr'=>'class=formLabel');
		$td = $nt->_htmlelements('td',$dom,$element);
		$element = array('value'=>'Email Address: ','req'=>'true');
		$label = $nf->_formelements('label',$dom,$element);
		$td->appendChild($label);
		$tr->appendChild($td);
		
		$element = array('attr'=>'class=formInput&colspan=2');
		$td = $nt->_htmlelements('td',$dom,$element);
		$element = array('attr'=>'type=text&name=email&id=email');
		$input = $nf->_formelements('input',$dom,$element);
		$td->appendChild($input);
		$tr->appendChild($td);
		
		$element = array('attr'=>'class=formLabel');
		$td = $nt->_htmlelements('td',$dom,$element);
		$element = array('value'=>'Password: ','req'=>'true');
		$label = $nf->_formelements('label',$dom,$element);
		$td->appendChild($label);
		$tr->appendChild($td);
		
		$element = array('attr'=>'class=formInput&colspan=2');
		$td = $nt->_htmlelements('td',$dom,$element);
		$element = array('attr'=>'type=password&name=password&id=password');
		$input = $nf->_formelements('input',$dom,$element);
		$td->appendChild($input);
		$tr->appendChild($td);
		
		$table->appendChild($tr);
		
		$tr = $nt->_htmlelements('tr',$dom);
		
		$element = array('attr'=>'class=formLabel');
		$td = $nt->_htmlelements('td',$dom,$element);
		$element = array('value'=>'Phone: ');
		$label = $nf->_formelements('label',$dom,$element);
		$td->appendChild($label);
		$tr->appendChild($td);
		
		$element = array('attr'=>'class=formInput&colspan=5');
		$td = $nt->_htmlelements('td',$dom,$element);
		$element = array('attr'=>'type=text&name=phone&id=phone');
		$input = $nf->_formelements('input',$dom,$element);
		$td->appendChild($input);
		$tr->appendChild($td);
		
		$table->appendChild($tr);
		
		$tr = $nt->_htmlelements('tr',$dom);
		
		$element = array('attr'=>'class=formLabel');
		$td = $nt->_htmlelements('td',$dom,$element);
		$element = array('value'=>'Address 1: ');
		$label = $nf->_formelements('label',$dom,$element);
		$td->appendChild($label);
		$tr->appendChild($td);
		
		$element = array('attr'=>'class=formInput&colspan=5');
		$td = $nt->_htmlelements('td',$dom,$element);
		$element = array('attr'=>'type=text&name=address1&id=address1');
		$input = $nf->_formelements('input',$dom,$element);
		$td->appendChild($input);
		$tr->appendChild($td);
		
		$table->appendChild($tr);
		
		$tr = $nt->_htmlelements('tr',$dom);
		
		$element = array('attr'=>'class=formLabel');
		$td = $nt->_htmlelements('td',$dom,$element);
		$element = array('value'=>'Address 2: ');
		$label = $nf->_formelements('label',$dom,$element);
		$td->appendChild($label);
		$tr->appendChild($td);
		
		$element = array('attr'=>'class=formInput&colspan=5');
		$td = $nt->_htmlelements('td',$dom,$element);
		$element = array('attr'=>'type=text&name=address2&id=address2');
		$input = $nf->_formelements('input',$dom,$element);
		$td->appendChild($input);
		$tr->appendChild($td);
		
		$table->appendChild($tr);
		
		$tr = $nt->_htmlelements('tr',$dom);
		
		$element = array('attr'=>'class=formLabel');
		$td = $nt->_htmlelements('td',$dom,$element);
		$element = array('value'=>'City: ');
		$label = $nf->_formelements('label',$dom,$element);
		$td->appendChild($label);
		$tr->appendChild($td);
		
		$element = array('attr'=>'class=formInput&colspan=5');
		$td = $nt->_htmlelements('td',$dom,$element);
		$element = array('attr'=>'type=text&name=city&id=city');
		$input = $nf->_formelements('input',$dom,$element);
		$td->appendChild($input);
		$tr->appendChild($td);
		
		$table->appendChild($tr);
		
		$tr = $nt->_htmlelements('tr',$dom);
		
		$element = array('attr'=>'class=formLabel');
		$td = $nt->_htmlelements('td',$dom,$element);
		$element = array('value'=>'State/Zip: ');
		$label = $nf->_formelements('label',$dom,$element);
		$td->appendChild($label);
		$tr->appendChild($td);
		
		$element = array('attr'=>'class=formInput&colspan=5');
		$td = $nt->_htmlelements('td',$dom,$element);
		$element = array('attr'=>'name=state&id=state','options'=>$states_list);
		$select = $nf->_formelements('select',$dom,$element);
		$td->appendChild($select);
		$element = array('attr'=>'type=text&name=zip&id=zip&size=7');
		$input = $nf->_formelements('input',$dom,$element);
		$td->appendChild($input);
		$tr->appendChild($td);
		
		$table->appendChild($tr);
		
		$tr = $nt->_htmlelements('tr',$dom);
		
		$element = array('attr'=>'class=spacer&colspan=6');
		$td = $nt->_htmlelements('td',$dom,$element);
		$tr->appendChild($td);
		
		$table->appendChild($tr);
		
		$tr = $nt->_htmlelements('tr',$dom);
		
		$element = array('attr'=>'&class=formRight&colspan=6');
		$td = $nt->_htmlelements('td',$dom,$element);
		$element = array('attr'=>'type=submit&name=next&id=next&value=Next...');
		$input = $nf->_formelements('input',$dom,$element);
		$td->appendChild($input);
		$tr->appendChild($td);
		
		$table->appendChild($tr);
		
		$form->appendChild($table);
		$dom->appendChild($form);
		
		return($dom->saveHTML());
	}
	
	public function userRegister() {
		$states_list = '';
		$i=0;
		foreach($this->states AS $key=>$val) {
			if($i < count($this->states)-1) {
				$states_list .= $val.'|'.$key.',';
				$i++;
			} else {
				$states_list .= $val.'|'.$key;
			}
		}
		
		$dom = new DOMDocument('1.0');
		
		$element = array(
								'form'=>array(
									array('name'=>'name','value'=>'set_user'),
									array('name'=>'id','value'=>'set_user'),
									array('name'=>'method','value'=>'POST')
								),
								'hidden'=>array(
									array('name'=>'action','value'=>'set_user'),
									array('name'=>'act_switch','value'=>'next'),
									array('name'=>'organization_id','value'=>$_SESSION['user']->organization_id)
								)
							);
		
		$nf = new FormElements();
		$nt = new HTMLElements();
		
		$form = $nf->_formelements('form',$dom,$element);
		
		$table = $nt->_htmlelements('table',$dom);
		
		$element = array('value'=>'USER INFORMATION','attr'=>'colspan=6');
		$th = $nt->_htmlelements('th',$dom,$element);
		$table->appendChild($th);
		
		$tr = $nt->_htmlelements('tr',$dom);
		
		$element = array('attr'=>'class=formHelp&colspan=6');
		$td = $nt->_htmlelements('td',$dom,$element);
		$element = array('value'=>'?','a'=>"href=data/help/user_info.html&rel=ibox&title=User Information");
		$a = $nt->_htmlelements('a',$dom,$element);
		$td->appendChild($a);
		$tr->appendChild($td);
		
		$table->appendChild($tr);
		
		$tr = $nt->_htmlelements('tr',$dom);
		
		$element = array('attr'=>'class=formLabel');
		$td = $nt->_htmlelements('td',$dom,$element);
		$element = array('value'=>'Name (first/last): ','req'=>'true');
		$label = $nf->_formelements('label',$dom,$element);
		$td->appendChild($label);
		$tr->appendChild($td);
		
		$element = array('attr'=>'class=formInput&colspan=2');
		$td = $nt->_htmlelements('td',$dom,$element);
		$element = array('attr'=>'type=text&name=first_name&id=first_name&size=12');
		$input = $nf->_formelements('input',$dom,$element);
		$td->appendChild($input);
		$element = array('attr'=>'type=text&name=last_name&id=last_name&size=12');
		$input = $nf->_formelements('input',$dom,$element);
		$td->appendChild($input);
		$tr->appendChild($td);
		
		$element = array('attr'=>'class=formLabel');
		$td = $nt->_htmlelements('td',$dom,$element);
		$element = array('value'=>'Username: ','req'=>'true');
		$label = $nf->_formelements('label',$dom,$element);
		$td->appendChild($label);
		$tr->appendChild($td);
		
		$element = array('attr'=>'class=formInput&colspan=2');
		$td = $nt->_htmlelements('td',$dom,$element);
		$element = array('attr'=>'type=text&name=username&id=username');
		$input = $nf->_formelements('input',$dom,$element);
		$td->appendChild($input);
		$tr->appendChild($td);
		
		$table->appendChild($tr);
		
		$tr = $nt->_htmlelements('tr',$dom);
		
		$element = array('attr'=>'class=formLabel');
		$td = $nt->_htmlelements('td',$dom,$element);
		$element = array('value'=>'Email Address: ','req'=>'true');
		$label = $nf->_formelements('label',$dom,$element);
		$td->appendChild($label);
		$tr->appendChild($td);
		
		$element = array('attr'=>'class=formInput&colspan=2');
		$td = $nt->_htmlelements('td',$dom,$element);
		$element = array('attr'=>'type=text&name=email&id=email');
		$input = $nf->_formelements('input',$dom,$element);
		$td->appendChild($input);
		$tr->appendChild($td);
		
		$element = array('attr'=>'class=formLabel');
		$td = $nt->_htmlelements('td',$dom,$element);
		$element = array('value'=>'Password: ','req'=>'true');
		$label = $nf->_formelements('label',$dom,$element);
		$td->appendChild($label);
		$tr->appendChild($td);
		
		$element = array('attr'=>'class=formInput&colspan=2');
		$td = $nt->_htmlelements('td',$dom,$element);
		$element = array('attr'=>'type=password&name=password&id=password');
		$input = $nf->_formelements('input',$dom,$element);
		$td->appendChild($input);
		$tr->appendChild($td);
		
		$table->appendChild($tr);
		
		$tr = $nt->_htmlelements('tr',$dom);
		
		$element = array('attr'=>'class=formLabel');
		$td = $nt->_htmlelements('td',$dom,$element);
		$element = array('value'=>'Phone: ');
		$label = $nf->_formelements('label',$dom,$element);
		$td->appendChild($label);
		$tr->appendChild($td);
		
		$element = array('attr'=>'class=formInput&colspan=5');
		$td = $nt->_htmlelements('td',$dom,$element);
		$element = array('attr'=>'type=text&name=phone&id=phone');
		$input = $nf->_formelements('input',$dom,$element);
		$td->appendChild($input);
		$tr->appendChild($td);
		
		$table->appendChild($tr);
		
		$tr = $nt->_htmlelements('tr',$dom);
		
		$element = array('attr'=>'class=formLabel');
		$td = $nt->_htmlelements('td',$dom,$element);
		$element = array('value'=>'Address 1: ');
		$label = $nf->_formelements('label',$dom,$element);
		$td->appendChild($label);
		$tr->appendChild($td);
		
		$element = array('attr'=>'class=formInput&colspan=5');
		$td = $nt->_htmlelements('td',$dom,$element);
		$element = array('attr'=>'type=text&name=address1&id=address1');
		$input = $nf->_formelements('input',$dom,$element);
		$td->appendChild($input);
		$tr->appendChild($td);
		
		$table->appendChild($tr);
		
		$tr = $nt->_htmlelements('tr',$dom);
		
		$element = array('attr'=>'class=formLabel');
		$td = $nt->_htmlelements('td',$dom,$element);
		$element = array('value'=>'Address 2: ');
		$label = $nf->_formelements('label',$dom,$element);
		$td->appendChild($label);
		$tr->appendChild($td);
		
		$element = array('attr'=>'class=formInput&colspan=5');
		$td = $nt->_htmlelements('td',$dom,$element);
		$element = array('attr'=>'type=text&name=address2&id=address2');
		$input = $nf->_formelements('input',$dom,$element);
		$td->appendChild($input);
		$tr->appendChild($td);
		
		$table->appendChild($tr);
		
		$tr = $nt->_htmlelements('tr',$dom);
		
		$element = array('attr'=>'class=formLabel');
		$td = $nt->_htmlelements('td',$dom,$element);
		$element = array('value'=>'City: ');
		$label = $nf->_formelements('label',$dom,$element);
		$td->appendChild($label);
		$tr->appendChild($td);
		
		$element = array('attr'=>'class=formInput&colspan=5');
		$td = $nt->_htmlelements('td',$dom,$element);
		$element = array('attr'=>'type=text&name=city&id=city');
		$input = $nf->_formelements('input',$dom,$element);
		$td->appendChild($input);
		$tr->appendChild($td);
		
		$table->appendChild($tr);
		
		$tr = $nt->_htmlelements('tr',$dom);
		
		$element = array('attr'=>'class=formLabel');
		$td = $nt->_htmlelements('td',$dom,$element);
		$element = array('value'=>'State/Zip: ');
		$label = $nf->_formelements('label',$dom,$element);
		$td->appendChild($label);
		$tr->appendChild($td);
		
		$element = array('attr'=>'class=formInput&colspan=5');
		$td = $nt->_htmlelements('td',$dom,$element);
		$element = array('attr'=>'name=state&id=state','options'=>$states_list);
		$select = $nf->_formelements('select',$dom,$element);
		$td->appendChild($select);
		$element = array('attr'=>'type=text&name=zip&id=zip&size=7');
		$input = $nf->_formelements('input',$dom,$element);
		$td->appendChild($input);
		$tr->appendChild($td);
		
		$table->appendChild($tr);
		
		$tr = $nt->_htmlelements('tr',$dom);
		
		$element = array('attr'=>'class=spacer&colspan=6');
		$td = $nt->_htmlelements('td',$dom,$element);
		$tr->appendChild($td);
		
		$table->appendChild($tr);
		
		$tr = $nt->_htmlelements('tr',$dom);
		
		$element = array('attr'=>'&class=formRight&colspan=3');
		$td = $nt->_htmlelements('td',$dom,$element);
		$element = array('attr'=>'type=submit&name=another&id=another&value=Save and Add Another User...');
		$input = $nf->_formelements('input',$dom,$element);
		$td->appendChild($input);
		$tr->appendChild($td);
		
		$element = array('attr'=>'&class=formRight&colspan=3');
		$td = $nt->_htmlelements('td',$dom,$element);
		$element = array('attr'=>'type=submit&name=next&id=next&value=Next...');
		$input = $nf->_formelements('input',$dom,$element);
		$td->appendChild($input);
		$tr->appendChild($td);
		
		$table->appendChild($tr);
		
		$form->appendChild($table);
		$dom->appendChild($form);
		
		return($dom->saveHTML());
	}
	
	public function demoSignup() {
		$states_list = '';
		$i=0;
		foreach($this->states AS $key=>$val) {
			if($i < count($this->states)-1) {
				$states_list .= $val.'|'.$key.',';
				$i++;
			} else {
				$states_list .= $val.'|'.$key;
			}
		}
		
		$base = new BaseService();
		$person = $_SESSION['user'];
		
		$dom = new DOMDocument('1.0');
		
		$element = array(
								'form'=>array(
									array('name'=>'name','value'=>'set_demo'),
									array('name'=>'id','value'=>'set_demo'),
									array('name'=>'method','value'=>'POST')
								),
								'hidden'=>array(
									array('name'=>'action','value'=>'set_demo'),
									array('name'=>'demo_id','value'=>$person->id),
									array('name'=>'first_name','value'=>$person->first_name),
									array('name'=>'last_name','value'=>$person->last_name),
									array('name'=>'organization_id','value'=>$person->organization_id)
								)
							);
		
		$nf = new FormElements();
		$nt = new HTMLElements();
		
		$form = $nf->_formelements('form',$dom,$element);
		
		$table = $nt->_htmlelements('table',$dom);
		
		$element = array('value'=>'ACCOUNT HOLDER INFORMATION','attr'=>'colspan=6');
		$th = $nt->_htmlelements('th',$dom,$element);
		$table->appendChild($th);
		
		$tr = $nt->_htmlelements('tr',$dom);
		
		$element = array('attr'=>'class=formHelp&colspan=6');
		$td = $nt->_htmlelements('td',$dom,$element);
		$element = array('value'=>'?','a'=>"href=data/help/account_holder.html&rel=ibox&title=Account Holder Information");
		$a = $nt->_htmlelements('a',$dom,$element);
		$td->appendChild($a);
		$tr->appendChild($td);
		
		$table->appendChild($tr);
		
		$tr = $nt->_htmlelements('tr',$dom);
		
		$element = array('attr'=>'class=formLabel');
		$td = $nt->_htmlelements('td',$dom,$element);
		$element = array('value'=>'Name (first/last): ','req'=>'true');
		$label = $nf->_formelements('label',$dom,$element);
		$td->appendChild($label);
		$tr->appendChild($td);
		
		$element = array('attr'=>'class=formInput&colspan=2');
		$td = $nt->_htmlelements('td',$dom,$element);
		$element = array('value'=>$person->first_name.' '.$person->last_name);
		$label = $nf->_formelements('label',$dom,$element);
		$td->appendChild($label);
		$tr->appendChild($td);
		
		$element = array('attr'=>'class=formLabel');
		$td = $nt->_htmlelements('td',$dom,$element);
		$element = array('value'=>'Username: ','req'=>'true');
		$label = $nf->_formelements('label',$dom,$element);
		$td->appendChild($label);
		$tr->appendChild($td);
		
		$element = array('attr'=>'class=formInput&colspan=2');
		$td = $nt->_htmlelements('td',$dom,$element);
		$element = array('attr'=>'type=text&name=username&id=username&value='.$person->login);
		$input = $nf->_formelements('input',$dom,$element);
		$td->appendChild($input);
		$tr->appendChild($td);
		
		$table->appendChild($tr);
		
		$tr = $nt->_htmlelements('tr',$dom);
		
		$element = array('attr'=>'class=formLabel');
		$td = $nt->_htmlelements('td',$dom,$element);
		$element = array('value'=>'Email Address: ','req'=>'true');
		$label = $nf->_formelements('label',$dom,$element);
		$td->appendChild($label);
		$tr->appendChild($td);
		
		$element = array('attr'=>'class=formInput&colspan=2');
		$td = $nt->_htmlelements('td',$dom,$element);
		$element = array('attr'=>'type=text&name=email&id=email&value='.$person->email);
		$input = $nf->_formelements('input',$dom,$element);
		$td->appendChild($input);
		$tr->appendChild($td);
		
		$element = array('attr'=>'class=formLabel');
		$td = $nt->_htmlelements('td',$dom,$element);
		$element = array('value'=>'Password: ','req'=>'true');
		$label = $nf->_formelements('label',$dom,$element);
		$td->appendChild($label);
		$tr->appendChild($td);
		
		$element = array('attr'=>'class=formInput&colspan=2');
		$td = $nt->_htmlelements('td',$dom,$element);
		$element = array('attr'=>'type=password&name=password&id=password&value='.$person->password);
		$input = $nf->_formelements('input',$dom,$element);
		$td->appendChild($input);
		$tr->appendChild($td);
		
		$table->appendChild($tr);
		
		$tr = $nt->_htmlelements('tr',$dom);
		
		$element = array('attr'=>'class=formLabel');
		$td = $nt->_htmlelements('td',$dom,$element);
		$element = array('value'=>'Phone: ');
		$label = $nf->_formelements('label',$dom,$element);
		$td->appendChild($label);
		$tr->appendChild($td);
		
		$element = array('attr'=>'class=formInput&colspan=5');
		$td = $nt->_htmlelements('td',$dom,$element);
		$element = array('attr'=>'type=text&name=phone&id=phone');
		$input = $nf->_formelements('input',$dom,$element);
		$td->appendChild($input);
		$tr->appendChild($td);
		
		$table->appendChild($tr);
		
		$tr = $nt->_htmlelements('tr',$dom);
		
		$element = array('attr'=>'class=formLabel');
		$td = $nt->_htmlelements('td',$dom,$element);
		$element = array('value'=>'Address 1: ');
		$label = $nf->_formelements('label',$dom,$element);
		$td->appendChild($label);
		$tr->appendChild($td);
		
		$element = array('attr'=>'class=formInput&colspan=5');
		$td = $nt->_htmlelements('td',$dom,$element);
		$element = array('attr'=>'type=text&name=address1&id=address1');
		$input = $nf->_formelements('input',$dom,$element);
		$td->appendChild($input);
		$tr->appendChild($td);
		
		$table->appendChild($tr);
		
		$tr = $nt->_htmlelements('tr',$dom);
		
		$element = array('attr'=>'class=formLabel');
		$td = $nt->_htmlelements('td',$dom,$element);
		$element = array('value'=>'Address 2: ');
		$label = $nf->_formelements('label',$dom,$element);
		$td->appendChild($label);
		$tr->appendChild($td);
		
		$element = array('attr'=>'class=formInput&colspan=5');
		$td = $nt->_htmlelements('td',$dom,$element);
		$element = array('attr'=>'type=text&name=address2&id=address2');
		$input = $nf->_formelements('input',$dom,$element);
		$td->appendChild($input);
		$tr->appendChild($td);
		
		$table->appendChild($tr);
		
		$tr = $nt->_htmlelements('tr',$dom);
		
		$element = array('attr'=>'class=formLabel');
		$td = $nt->_htmlelements('td',$dom,$element);
		$element = array('value'=>'City: ');
		$label = $nf->_formelements('label',$dom,$element);
		$td->appendChild($label);
		$tr->appendChild($td);
		
		$element = array('attr'=>'class=formInput&colspan=5');
		$td = $nt->_htmlelements('td',$dom,$element);
		$element = array('attr'=>'type=text&name=city&id=city');
		$input = $nf->_formelements('input',$dom,$element);
		$td->appendChild($input);
		$tr->appendChild($td);
		
		$table->appendChild($tr);
		
		$tr = $nt->_htmlelements('tr',$dom);
		
		$element = array('attr'=>'class=formLabel');
		$td = $nt->_htmlelements('td',$dom,$element);
		$element = array('value'=>'State/Zip: ');
		$label = $nf->_formelements('label',$dom,$element);
		$td->appendChild($label);
		$tr->appendChild($td);
		
		$element = array('attr'=>'class=formInput&colspan=5');
		$td = $nt->_htmlelements('td',$dom,$element);
		
		$element = array('attr'=>'name=state&id=state','options'=>$states_list);
		$select = $nf->_formelements('select',$dom,$element);
		$td->appendChild($select);
		
		$element = array('attr'=>'type=text&name=zip&id=zip&size=7');
		$input = $nf->_formelements('input',$dom,$element);
		$td->appendChild($input);
		$tr->appendChild($td);
		
		$table->appendChild($tr);
		
		$tr = $nt->_htmlelements('tr',$dom);
		
		$element = array('attr'=>'class=spacer&colspan=6');
		$td = $nt->_htmlelements('td',$dom,$element);
		$tr->appendChild($td);
		
		$table->appendChild($tr);
		
		$tr = $nt->_htmlelements('tr',$dom);
		
		$element = array('attr'=>'class=formNote&colspan=6');
		$td = $nt->_htmlelements('td',$dom,$element);
		$element = array('value'=>'This account is for demonstration purposes only. Your account information will be saved, but all recipes, or other cookbook information you enter will not be saved.');
		$label = $nf->_formelements('label',$dom,$element);
		$td->appendChild($label);
		$tr->appendChild($td);
		
		$table->appendChild($tr);
		
		$tr = $nt->_htmlelements('tr',$dom);
		
		$element = array('attr'=>'&class=formRight&colspan=6');
		$td = $nt->_htmlelements('td',$dom,$element);
		$element = array('attr'=>'type=submit&name=next&id=next&value=Next...');
		$input = $nf->_formelements('input',$dom,$element);
		$td->appendChild($input);
		$tr->appendChild($td);
		
		$table->appendChild($tr);
		
		$form->appendChild($table);
		$dom->appendChild($form);
		
		return($dom->saveHTML());
	}

	public function orderOptions() {
		
		$dom = new DOMDocument('1.0');
		
		$element = array(
								'form'=>array(
									array('name'=>'name','value'=>'set_order_options'),
									array('name'=>'id','value'=>'set_order_options'),
									array('name'=>'method','value'=>'POST')
								),
								'hidden'=>array(
									array('name'=>'action','value'=>'set_order_options')
								)
							);
		
		$nf = new FormElements();
		$nt = new HTMLElements();
		$ol = new OrderedList();
		
		$form = $nf->_formelements('form',$dom,$element);
		
		$element = array('value'=>'COOKBOOK ORDER OPTIONS ','attr'=>'class=optionsHeaderDiv');
		$header = $nt->_htmlelements('div',$dom,$element);
		$element = array('value'=>'?','a'=>"href=data/help/order_options.html&rel=ibox&title=Order Options");
		$a = $nt->_htmlelements('a',$dom,$element);
		$header->appendChild($a);
		$form->appendChild($header);
		
		// LEFT DIV
		$element = array('attr'=>'class=optionsLeftDiv');
		$leftdiv = $nt->_htmlelements('div',$dom,$element);
		
		$table = $nt->_htmlelements('table',$dom);
		
			$tr = $nt->_htmlelements('tr',$dom);
			
			$element = array('attr'=>'class=formLabel');
			$td = $nt->_htmlelements('td',$dom,$element);
			$element = array('value'=>'Cookbook Title: ');
			$label = $nf->_formelements('label',$dom,$element);
			$td->appendChild($label);
			$tr->appendChild($td);
			
			$element = array('attr'=>'class=formInput');
			$td = $nt->_htmlelements('td',$dom,$element);
			$element = array('attr'=>'type=text&name=book_title1&id=book_title1');
			$input = $nf->_formelements('input',$dom,$element);
			$td->appendChild($input);
			$tr->appendChild($td);
			
			$element = array('attr'=>'class=formHelp');
			$td = $nt->_htmlelements('td',$dom,$element);
			$element = array('value'=>'?','a'=>"href=data/help/cookbook_title.html&rel=ibox&title=Cookbook Title");
			$a = $nt->_htmlelements('a',$dom,$element);
			$td->appendChild($a);
			$tr->appendChild($td);
			
			$table->appendChild($tr);
			
			$tr = $nt->_htmlelements('tr',$dom);
			
			$element = array('attr'=>'class=formLabel');
			$td = $nt->_htmlelements('td',$dom,$element);
			$element = array('value'=>'&nbsp;');
			$label = $nf->_formelements('label',$dom,$element);
			$td->appendChild($label);
			$tr->appendChild($td);
			
			$element = array('attr'=>'class=formInput');
			$td = $nt->_htmlelements('td',$dom,$element);
			$element = array('attr'=>'type=text&name=book_title2&id=book_title2');
			$input = $nf->_formelements('input',$dom,$element);
			$td->appendChild($input);
			$tr->appendChild($td);
			
			$element = array('attr'=>'class=formHelp');
			$td = $nt->_htmlelements('td',$dom,$element);
			$element = array('value'=>'&nbsp;');
			$label = $nf->_formelements('label',$dom,$element);
			$td->appendChild($label);
			$tr->appendChild($td);
			
			$table->appendChild($tr);
			
			$tr = $nt->_htmlelements('tr',$dom);
			
			$element = array('attr'=>'class=formLabel');
			$td = $nt->_htmlelements('td',$dom,$element);
			$element = array('value'=>'&nbsp;');
			$label = $nf->_formelements('label',$dom,$element);
			$td->appendChild($label);
			$tr->appendChild($td);
			
			$element = array('attr'=>'class=formInput');
			$td = $nt->_htmlelements('td',$dom,$element);
			$element = array('attr'=>'type=text&name=book_title3&id=book_title3');
			$input = $nf->_formelements('input',$dom,$element);
			$td->appendChild($input);
			$tr->appendChild($td);
			
			$element = array('attr'=>'class=formHelp');
			$td = $nt->_htmlelements('td',$dom,$element);
			$element = array('value'=>'&nbsp;');
			$label = $nf->_formelements('label',$dom,$element);
			$td->appendChild($label);
			$tr->appendChild($td);
			
			$table->appendChild($tr);
			
			$tr = $nt->_htmlelements('tr',$dom);
			
			$element = array('attr'=>'class=formLabel');
			$td = $nt->_htmlelements('td',$dom,$element);
			$element = array('value'=>'Cookbook Style:');
			$label = $nf->_formelements('label',$dom,$element);
			$td->appendChild($label);
			$tr->appendChild($td);
			
			$element = array('attr'=>'class=formInput');
			$td = $nt->_htmlelements('td',$dom,$element);
			$element = array('attr'=>'name=book_style&id=book_style','options'=>'0: -- |Hard Cover:Hard Cover|Soft Cover:Soft Cover|3-Ring Binder:3-Ring Binder');
			$select = $nf->_formelements('select',$dom,$element);
			$td->appendChild($select);
			$tr->appendChild($td);
			
			$element = array('attr'=>'class=formHelp');
			$td = $nt->_htmlelements('td',$dom,$element);
			$element = array('value'=>'?','a'=>"href=data/help/book_style.html&rel=ibox&title=Book Style");
			$a = $nt->_htmlelements('a',$dom,$element);
			$td->appendChild($a);
			$tr->appendChild($td);
			
			$table->appendChild($tr);
			
			$tr = $nt->_htmlelements('tr',$dom);
			
			$element = array('attr'=>'class=formLabel');
			$td = $nt->_htmlelements('td',$dom,$element);
			$element = array('value'=>'# of books to order:');
			$label = $nf->_formelements('label',$dom,$element);
			$td->appendChild($label);
			$tr->appendChild($td);
			
			$element = array('attr'=>'class=formInput');
			$td = $nt->_htmlelements('td',$dom,$element);
			$element = array('attr'=>'type=text&name=book_count&id=book_count&size=5');
			$input = $nf->_formelements('input',$dom,$element);
			$td->appendChild($input);
			$tr->appendChild($td);
			
			$element = array('attr'=>'class=formHelp');
			$td = $nt->_htmlelements('td',$dom,$element);
			$element = array('value'=>'?','a'=>"href=data/help/book_count.html&rel=ibox&title=Book Count");
			$a = $nt->_htmlelements('a',$dom,$element);
			$td->appendChild($a);
			$tr->appendChild($td);
			
			$table->appendChild($tr);
			
			$tr = $nt->_htmlelements('tr',$dom);
			
			$element = array('attr'=>'class=formLabel');
			$td = $nt->_htmlelements('td',$dom,$element);
			$element = array('value'=>'Order recipes:');
			$label = $nf->_formelements('label',$dom,$element);
			$td->appendChild($label);
			$tr->appendChild($td);
			
			$element = array('attr'=>'class=formInput');
			$td = $nt->_htmlelements('td',$dom,$element);
			$element = array('attr'=>'type=text&name=order_recipes_by&id=order_recipes_by','options'=>'0:as Entered|alpha:by Alphabet|custom:Custom Order');
			$input = $nf->_formelements('select',$dom,$element);
			$td->appendChild($input);
			$tr->appendChild($td);
			
			$element = array('attr'=>'class=formHelp');
			$td = $nt->_htmlelements('td',$dom,$element);
			$element = array('value'=>'?','a'=>"href=data/help/order_recipes_by.html&rel=ibox&title=Order Recipes By");
			$a = $nt->_htmlelements('a',$dom,$element);
			$td->appendChild($a);
			$tr->appendChild($td);
			
			$table->appendChild($tr);
			
			$tr = $nt->_htmlelements('tr',$dom);
			
			$element = array('attr'=>'class=formLabel');
			$td = $nt->_htmlelements('td',$dom,$element);
			$element = array('value'=>'Use subcategories?');
			$label = $nf->_formelements('label',$dom,$element);
			$td->appendChild($label);
			$tr->appendChild($td);
			
			$element = array('attr'=>'class=formInput');
			$td = $nt->_htmlelements('td',$dom,$element);
			$element = array('attr'=>'type=checkbox&name=use_subcategories&id=use_subcategories&value=yes');
			$input = $nf->_formelements('input',$dom,$element);
			$td->appendChild($input);
			$tr->appendChild($td);
			
			$element = array('attr'=>'class=formHelp');
			$td = $nt->_htmlelements('td',$dom,$element);
			$element = array('value'=>'?','a'=>"href=data/help/book_count.html&rel=ibox&title=Book Count");
			$a = $nt->_htmlelements('a',$dom,$element);
			$td->appendChild($a);
			$tr->appendChild($td);
			
			$table->appendChild($tr);
			
			$tr = $nt->_htmlelements('tr',$dom);
			
			$element = array('attr'=>'colspan=3');
			$td = $nt->_htmlelements('td',$dom,$element);
			$element = array('title'=>'Categories','title_class'=>'wizardOLTitle','container_class'=>'wizardOLContainer','list_class'=>'wizardOLList','row_class'=>'orderListSection','row_id'=>'category');
			$source = array(
				array('draw'=>'input','attr'=>'type=text&name=title&count=1&id=category&value=Appetizers, Beverages&place=_section&class=orderListSection'),
				array('draw'=>'input','attr'=>'type=text&name=title&count=2&id=category&value=Soups, Salads&place=_section&class=orderListSection'),
				array('draw'=>'input','attr'=>'type=text&name=title&count=3&id=category&value=Vegetables&place=_section&class=orderListSection'),
				array('draw'=>'input','attr'=>'type=text&name=title&count=4&id=category&value=Main Dishes&place=_section&class=orderListSection'),
				array('draw'=>'input','attr'=>'type=text&name=title&count=5&id=category&value=Breads, Rolls&place=_section&class=orderListSection'),
				array('draw'=>'input','attr'=>'type=text&name=title&count=6&id=category&value=Desserts&place=_section&class=orderListSection'),
				array('draw'=>'input','attr'=>'type=text&name=title&count=7&id=category&value=Miscellaneous&place=_section&class=orderListSection')
			);
			
			$list = $ol->_orderedlist($dom,$source,$element,'recipe_categories');
			$td->appendChild($list);
			$tr->appendChild($td);
			
			$table->appendChild($tr);
		
		$leftdiv->appendChild($table);
		$form->appendChild($leftdiv);
		
		// RIGHT DIV
		$element = array('attr'=>'class=optionsRightDiv');
		$rightdiv = $nt->_htmlelements('div',$dom,$element);
		
		$table = $nt->_htmlelements('table',$dom);
		
		$tr = $nt->_htmlelements('tr',$dom);
		
		$element = array('attr'=>'colspan=4&class=formCenter');
		$td = $nt->_htmlelements('td',$dom,$element);
		$element = array('value'=>'Page Options');
		$h3 = $nt->_htmlelements('h3',$dom,$element);
		$td->appendChild($h3);
		$tr->appendChild($td);
		
		$table->appendChild($tr);
		
		$tr = $nt->_htmlelements('tr',$dom);
		
		$element = array('attr'=>'class=formLabel');
		$td = $nt->_htmlelements('td',$dom,$element);
		$element = array('value'=>'Free nutritional information: ');
		$label = $nf->_formelements('label',$dom,$element);
		$td->appendChild($label);
		$tr->appendChild($td);
		
		$element = array('attr'=>'class=formInput');
		$td = $nt->_htmlelements('td',$dom,$element);
		$element = array('attr'=>'type=radio&name=nutritionals&id=nutritionals_yes&value=yes');
		$input = $nf->_formelements('input',$dom,$element);
		$td->appendChild($input);
		$element = array('value'=>'Yes');
		$label = $nf->_formelements('label',$dom,$element);
		$td->appendChild($label);
		$tr->appendChild($td);
		
		$element = array('attr'=>'class=formInput');
		$td = $nt->_htmlelements('td',$dom,$element);
		$element = array('attr'=>'type=radio&name=nutritionals&id=nutritionals_no&value=no&checked=checked');
		$input = $nf->_formelements('input',$dom,$element);
		$td->appendChild($input);
		$element = array('value'=>'No');
		$label = $nf->_formelements('label',$dom,$element);
		$td->appendChild($label);
		$tr->appendChild($td);
		
		$element = array('attr'=>'class=formHelp');
		$td = $nt->_htmlelements('td',$dom,$element);
		$element = array('value'=>'?','a'=>"href=data/help/nutritionals.html&rel=ibox&title=Nutritional Information");
		$a = $nt->_htmlelements('a',$dom,$element);
		$td->appendChild($a);
		$tr->appendChild($td);
		
		$table->appendChild($tr);
		
		$tr = $nt->_htmlelements('tr',$dom);
		
		$element = array('attr'=>'class=formLabel');
		$td = $nt->_htmlelements('td',$dom,$element);
		$element = array('value'=>'Recipe contributors page: ');
		$label = $nf->_formelements('label',$dom,$element);
		$td->appendChild($label);
		$tr->appendChild($td);
		
		$element = array('attr'=>'class=formInput');
		$td = $nt->_htmlelements('td',$dom,$element);
		$element = array('attr'=>'type=radio&name=contributors&id=contributors_yes&value=yes');
		$input = $nf->_formelements('input',$dom,$element);
		$td->appendChild($input);
		$element = array('value'=>'Yes');
		$label = $nf->_formelements('label',$dom,$element);
		$td->appendChild($label);
		$tr->appendChild($td);
		
		$element = array('attr'=>'class=formInput');
		$td = $nt->_htmlelements('td',$dom,$element);
		$element = array('attr'=>'type=radio&name=contributors&id=contributors_no&value=no&checked=checked');
		$input = $nf->_formelements('input',$dom,$element);
		$td->appendChild($input);
		$element = array('value'=>'No');
		$label = $nf->_formelements('label',$dom,$element);
		$td->appendChild($label);
		$tr->appendChild($td);
		
		$element = array('attr'=>'class=formHelp');
		$td = $nt->_htmlelements('td',$dom,$element);
		$element = array('value'=>'?','a'=>"href=data/help/contributors.html&rel=ibox&title=Contributors");
		$a = $nt->_htmlelements('a',$dom,$element);
		$td->appendChild($a);
		$tr->appendChild($td);
		
		$table->appendChild($tr);
		
		$tr = $nt->_htmlelements('tr',$dom);
		
		$element = array('attr'=>'class=formLabel');
		$td = $nt->_htmlelements('td',$dom,$element);
		$element = array('value'=>'Order index page by: ');
		$label = $nf->_formelements('label',$dom,$element);
		$td->appendChild($label);
		$tr->appendChild($td);
		
		$element = array('attr'=>'class=formInput&colspan=2');
		$td = $nt->_htmlelements('td',$dom,$element);
		$element = array('attr'=>'name=order_index_by&id=order_index_by','options'=>'0: -- |As Entered:as entered|Alphabetically:alphabetical');
		$select = $nf->_formelements('select',$dom,$element);
		$td->appendChild($select);
		$tr->appendChild($td);
		
		$element = array('attr'=>'class=formHelp');
		$td = $nt->_htmlelements('td',$dom,$element);
		$element = array('value'=>' ');
		$a = $nt->_htmlelements('a',$dom,$element);
		$td->appendChild($a);
		$tr->appendChild($td);
		
		$table->appendChild($tr);
		
		$tr = $nt->_htmlelements('tr',$dom);
		
		$element = array('attr'=>'colspan=4&class=formCenter');
		$td = $nt->_htmlelements('td',$dom,$element);
		$element = array('value'=>'Custom Order Form in the Back of the Book');
		$h3 = $nt->_htmlelements('h3',$dom,$element);
		$td->appendChild($h3);
		$tr->appendChild($td);
		
		$table->appendChild($tr);
		
		$tr = $nt->_htmlelements('tr',$dom);
		$element = array('attr'=>'colspan=4&class=formCenter');
		$td = $nt->_htmlelements('td',$dom,$element);
			
			$subtable = $dom->createElement('table');
			
			$subtr = $dom->createElement('tr');
			
			$element = array('attr'=>'class=formLabel');
			$subtd = $nt->_htmlelements('td',$dom,$element);
			$element = array('value'=>'Name: ');
			$label = $nf->_formelements('label',$dom,$element);
			$subtd->appendChild($label);
			$subtr->appendChild($subtd);
			
			$element = array('attr'=>'class=formInput&colspan=2');
			$subtd = $nt->_htmlelements('td',$dom,$element);
			$element = array('attr'=>'type=text&name=order_form_name&id=order_form_name');
			$input = $nf->_formelements('input',$dom,$element);
			$subtd->appendChild($input);
			$subtr->appendChild($subtd);
			
			$element = array('attr'=>'class=formHelp');
			$subtd = $nt->_htmlelements('td',$dom,$element);
			$element = array('value'=>'?','a'=>"href=data/help/order_form.html&rel=ibox&title=Order Form");
			$a = $nt->_htmlelements('a',$dom,$element);
			$subtd->appendChild($a);
			$subtr->appendChild($subtd);
			
			$subtable->appendChild($subtr);
			
			$subtr = $dom->createElement('tr');
			
			$element = array('attr'=>'class=formLabel');
			$subtd = $nt->_htmlelements('td',$dom,$element);
			$element = array('value'=>'Address 1: ');
			$label = $nf->_formelements('label',$dom,$element);
			$subtd->appendChild($label);
			$subtr->appendChild($subtd);
			
			$element = array('attr'=>'class=formInput&colspan=3');
			$subtd = $nt->_htmlelements('td',$dom,$element);
			$element = array('attr'=>'type=text&name=order_form_address1&id=order_form_address1');
			$input = $nf->_formelements('input',$dom,$element);
			$subtd->appendChild($input);
			$subtr->appendChild($subtd);
			
			$subtable->appendChild($subtr);
			
			$subtr = $dom->createElement('tr');
			
			$element = array('attr'=>'class=formLabel');
			$subtd = $nt->_htmlelements('td',$dom,$element);
			$element = array('value'=>'Address 2: ');
			$label = $nf->_formelements('label',$dom,$element);
			$subtd->appendChild($label);
			$subtr->appendChild($subtd);
			
			$element = array('attr'=>'class=formInput&colspan=3');
			$subtd = $nt->_htmlelements('td',$dom,$element);
			$element = array('attr'=>'type=text&name=order_form_address2&id=order_form_address2');
			$input = $nf->_formelements('input',$dom,$element);
			$subtd->appendChild($input);
			$subtr->appendChild($subtd);
			
			$subtable->appendChild($subtr);
			
			$subtr = $dom->createElement('tr');
			
			$element = array('attr'=>'class=formLabel');
			$subtd = $nt->_htmlelements('td',$dom,$element);
			$element = array('value'=>'City: ');
			$label = $nf->_formelements('label',$dom,$element);
			$subtd->appendChild($label);
			$subtr->appendChild($subtd);
			
			$element = array('attr'=>'class=formInput');
			$subtd = $nt->_htmlelements('td',$dom,$element);
			$element = array('attr'=>'type=text&name=order_form_city&id=order_form_city&size=12');
			$input = $nf->_formelements('input',$dom,$element);
			$subtd->appendChild($input);
			$subtr->appendChild($subtd);
			
			$element = array('attr'=>'class=formLabel');
			$subtd = $nt->_htmlelements('td',$dom,$element);
			$element = array('value'=>'State/Zip: ');
			$label = $nf->_formelements('label',$dom,$element);
			$subtd->appendChild($label);
			$subtr->appendChild($subtd);
			
			$element = array('attr'=>'class=formInput');
			$subtd = $nt->_htmlelements('td',$dom,$element);
			$element = array('attr'=>'type=text&name=order_form_state&id=order_form_state&size=4');
			$input = $nf->_formelements('input',$dom,$element);
			$subtd->appendChild($input);
			$element = array('attr'=>'type=text&name=order_form_zip&id=order_form_zip&size=3');
			$input = $nf->_formelements('input',$dom,$element);
			$subtd->appendChild($input);
			$subtr->appendChild($subtd);
			
			$subtable->appendChild($subtr);
			
			$subtr = $dom->createElement('tr');
			
			$element = array('attr'=>'class=formLabel');
			$subtd = $nt->_htmlelements('td',$dom,$element);
			$element = array('value'=>'Retail Price: ');
			$label = $nf->_formelements('label',$dom,$element);
			$subtd->appendChild($label);
			$subtr->appendChild($subtd);
			
			$element = array('attr'=>'class=formInput');
			$subtd = $nt->_htmlelements('td',$dom,$element);
			$element = array('attr'=>'type=text&name=order_form_retail&id=order_form_retail&size=12');
			$input = $nf->_formelements('input',$dom,$element);
			$subtd->appendChild($input);
			$subtr->appendChild($subtd);
			
			$element = array('attr'=>'class=formLabel');
			$subtd = $nt->_htmlelements('td',$dom,$element);
			$element = array('value'=>'Shipping Fee: ');
			$label = $nf->_formelements('label',$dom,$element);
			$subtd->appendChild($label);
			$subtr->appendChild($subtd);
			
			$element = array('attr'=>'class=formInput');
			$subtd = $nt->_htmlelements('td',$dom,$element);
			$element = array('attr'=>'type=text&name=order_form_shipping&id=order_form_shipping&size=12&value=3.00');
			$input = $nf->_formelements('input',$dom,$element);
			$subtd->appendChild($input);
			$subtr->appendChild($subtd);
			
			$subtable->appendChild($subtr);
			
		$td->appendChild($subtable);
		$tr->appendChild($td);
		
		$table->appendChild($tr);
		
		$tr = $nt->_htmlelements('tr',$dom);
		
		$element = array('attr'=>'colspan=4&class=formCenter');
		$td = $nt->_htmlelements('td',$dom,$element);
		$element = array('value'=>'General User Settings');
		$h3 = $nt->_htmlelements('h3',$dom,$element);
		$td->appendChild($h3);
		$tr->appendChild($td);
		
		$table->appendChild($tr);
		
		$tr = $nt->_htmlelements('tr',$dom);
			
		$element = array('attr'=>'class=formLabel&colspan=2');
		$td = $nt->_htmlelements('td',$dom,$element);
		$element = array('value'=>'Recipe Entry Deadline: ');
		$label = $nf->_formelements('label',$dom,$element);
		$td->appendChild($label);
		$tr->appendChild($td);
		
		$element = array('attr'=>'class=formInput');
		$td = $nt->_htmlelements('td',$dom,$element);
		$element = array('attr'=>'type=text&name=recipe_deadline&id=recipe_deadline&size=12');
		$input = $nf->_formelements('input',$dom,$element);
		$td->appendChild($input);
		$element = array('attr'=>'src=images/calendar.png&border=0&id=calendar');
		$img = $nt->_htmlelements('img',$dom,$element);
		$td->appendChild($img);
		$tr->appendChild($td);
		
		$element = array('attr'=>'class=formHelp');
		$td = $nt->_htmlelements('td',$dom,$element);
		$element = array('value'=>'?','a'=>"href=data/help/recipe_deadline.html&rel=ibox&title=Recipe Entry Deadline");
		$a = $nt->_htmlelements('a',$dom,$element);
		$td->appendChild($a);
		$tr->appendChild($td);
		
		$table->appendChild($tr);
		
		$tr = $nt->_htmlelements('tr',$dom);
			
		$element = array('attr'=>'class=formLabel&colspan=2');
		$td = $nt->_htmlelements('td',$dom,$element);
		$element = array('value'=>'Max Recipes per Contributor: ');
		$label = $nf->_formelements('label',$dom,$element);
		$td->appendChild($label);
		$tr->appendChild($td);
		
		$element = array('attr'=>'class=formInput');
		$td = $nt->_htmlelements('td',$dom,$element);
		$element = array('attr'=>'type=text&name=max_recipes_ea&id=max_recipes_ea&size=12');
		$input = $nf->_formelements('input',$dom,$element);
		$td->appendChild($input);
		$tr->appendChild($td);
		
		$element = array('attr'=>'class=formHelp');
		$td = $nt->_htmlelements('td',$dom,$element);
		$element = array('value'=>'?','a'=>"href=data/help/max_recipes_ea.html&rel=ibox&title=Max Recipes Per User");
		$a = $nt->_htmlelements('a',$dom,$element);
		$td->appendChild($a);
		$tr->appendChild($td);
		
		$table->appendChild($tr);
		
		$tr = $nt->_htmlelements('tr',$dom);
			
		$element = array('attr'=>'class=formLabel&colspan=2');
		$td = $nt->_htmlelements('td',$dom,$element);
		$element = array('value'=>'Welcome message to users: ');
		$label = $nf->_formelements('label',$dom,$element);
		$td->appendChild($label);
		$tr->appendChild($td);
		
		$element = array('attr'=>'class=formInput');
		$td = $nt->_htmlelements('td',$dom,$element);
		$tr->appendChild($td);
		
		$element = array('attr'=>'class=formHelp');
		$td = $nt->_htmlelements('td',$dom,$element);
		$element = array('value'=>'?','a'=>"href=data/help/welcome_message.html&rel=ibox&Welcome Note");
		$a = $nt->_htmlelements('a',$dom,$element);
		$td->appendChild($a);
		$tr->appendChild($td);
		
		$table->appendChild($tr);
		
		$tr = $nt->_htmlelements('tr',$dom);
		
		$element = array('attr'=>'class=formCenter&colspan=4');
		$td = $nt->_htmlelements('td',$dom,$element);
		$element = array('attr'=>'name=welcome_note&id=welcome_note&rows=5');
		$label = $nf->_formelements('textarea',$dom,$element);
		$td->appendChild($label);
		$tr->appendChild($td);
		
		$table->appendChild($tr);
		
		$rightdiv->appendChild($table);
		$form->appendChild($rightdiv);
		
		// BASE DIV
		$element = array('attr'=>'class=optionsBaseDiv');
		$basediv = $nt->_htmlelements('div',$dom,$element);
		
		$table = $nt->_htmlelements('table',$dom);
		
		$tr = $nt->_htmlelements('tr',$dom);
		
		$element = array('attr'=>'class=optionsNextSubmit');
		$td = $nt->_htmlelements('div',$dom,$element);
		$element = array('attr'=>'type=submit&name=next&id=back&value=Next...');
		$input = $nf->_formelements('input',$dom,$element);
		$td->appendChild($input);
		$tr->appendChild($td);
		
		$table->appendChild($tr);
		
		$basediv->appendChild($table);
		$form->appendChild($basediv);
								
		$dom->appendChild($form);
		
		return($dom->saveHTML());
		
	}
	
	public function recipeOptions() {
		
		$dom = new DOMDocument('1.0');
		
		$element = array(
								'form'=>array(
									array('name'=>'name','value'=>'set_recipe_options'),
									array('name'=>'id','value'=>'set_recipe_options'),
									array('name'=>'method','value'=>'POST')
								),
								'hidden'=>array(
									array('name'=>'action','value'=>'set_recipe_options')
								)
							);
		
		$nf = new FormElements();
		$nt = new HTMLElements();
		$dg = new DataGrid();
		
		$form = $nf->_formelements('form',$dom,$element);
		
		$element = array('value'=>'RECIPE ENTRY OPTIONS ','attr'=>'class=optionsHeaderDiv');
		$header = $nt->_htmlelements('div',$dom,$element);
		$element = array('value'=>'?','a'=>"href=data/help/recipe_options.html&rel=ibox&title=Recipe Options");
		$a = $nt->_htmlelements('a',$dom,$element);
		$header->appendChild($a);
		$form->appendChild($header);
		
		$element = array('attr'=>'class=optionsLeftDiv');
		$leftdiv = $nt->_htmlelements('div',$dom,$element);
		
		$table = $nt->_htmlelements('table',$dom);
		
		$tr = $nt->_htmlelements('tr',$dom);
		
		$element = array('attr'=>'class=formLabel');
		$td = $nt->_htmlelements('td',$dom,$element);
		$element = array('value'=>'Maximum Recipes: ');
		$label = $nf->_formelements('label',$dom,$element);
		$td->appendChild($label);
		$tr->appendChild($td);
		
		$element = array('attr'=>'class=formInput&colspan=2');
		$td = $nt->_htmlelements('td',$dom,$element);
		$element = array('attr'=>'type=text&name=max_recipes&id=max_recipes&size=7');
		$input = $nf->_formelements('input',$dom,$element);
		$td->appendChild($input);
		$tr->appendChild($td);
		
		$element = array('attr'=>'class=formHelp');
		$td = $nt->_htmlelements('td',$dom,$element);
		$element = array('value'=>'?','a'=>"href=data/help/max_recipes.html&rel=ibox&title=Max Recipes");
		$a = $nt->_htmlelements('a',$dom,$element);
		$td->appendChild($a);
		$tr->appendChild($td);
		
		$table->appendChild($tr);
		
		$tr = $nt->_htmlelements('tr',$dom);
		
		$element = array('attr'=>'class=formLabel');
		$td = $nt->_htmlelements('td',$dom,$element);
		$element = array('value'=>'Recipes continued page to page: ');
		$label = $nf->_formelements('label',$dom,$element);
		$td->appendChild($label);
		$tr->appendChild($td);
		
		$element = array('attr'=>'class=formInput');
		$td = $nt->_htmlelements('td',$dom,$element);
		$element = array('attr'=>'type=radio&name=recipes_continued&id=recipes_continued_yes&value=yes');
		$input = $nf->_formelements('input',$dom,$element);
		$td->appendChild($input);
		$element = array('value'=>'Yes');
		$label = $nf->_formelements('label',$dom,$element);
		$td->appendChild($label);
		$tr->appendChild($td);
		
		$element = array('attr'=>'class=formInput');
		$td = $nt->_htmlelements('td',$dom,$element);
		$element = array('attr'=>'type=radio&name=recipes_continued&id=recipes_continued_no&value=no&checked=checked');
		$input = $nf->_formelements('input',$dom,$element);
		$td->appendChild($input);
		$element = array('value'=>'No');
		$label = $nf->_formelements('label',$dom,$element);
		$td->appendChild($label);
		$tr->appendChild($td);
		
		$element = array('attr'=>'class=formHelp');
		$td = $nt->_htmlelements('td',$dom,$element);
		$element = array('value'=>'?','a'=>"href=data/help/recipes_continued.html&rel=ibox&title=Recipes Continued Page to Page");
		$a = $nt->_htmlelements('a',$dom,$element);
		$td->appendChild($a);
		$tr->appendChild($td);
		
		$table->appendChild($tr);
		
		$tr = $nt->_htmlelements('tr',$dom);
		
		$element = array('attr'=>'class=formLabel');
		$td = $nt->_htmlelements('td',$dom,$element);
		$element = array('value'=>'Allow recipe notes: ');
		$label = $nf->_formelements('label',$dom,$element);
		$td->appendChild($label);
		$tr->appendChild($td);
		
		$element = array('attr'=>'class=formInput');
		$td = $nt->_htmlelements('td',$dom,$element);
		$element = array('attr'=>'type=radio&name=allow_notes&id=allow_notes&value=yes');
		$input = $nf->_formelements('input',$dom,$element);
		$td->appendChild($input);
		$element = array('value'=>'Yes');
		$label = $nf->_formelements('label',$dom,$element);
		$td->appendChild($label);
		$tr->appendChild($td);
		
		$element = array('attr'=>'class=formInput');
		$td = $nt->_htmlelements('td',$dom,$element);
		$element = array('attr'=>'type=radio&name=allow_notes&id=allow_notes&value=no&checked=checked');
		$input = $nf->_formelements('input',$dom,$element);
		$td->appendChild($input);
		$element = array('value'=>'No');
		$label = $nf->_formelements('label',$dom,$element);
		$td->appendChild($label);
		$tr->appendChild($td);
		
		$element = array('attr'=>'class=formHelp');
		$td = $nt->_htmlelements('td',$dom,$element);
		$element = array('value'=>'?','a'=>"href=data/help/allow_notes.html&rel=ibox&Allow Recipe Notes");
		$a = $nt->_htmlelements('a',$dom,$element);
		$td->appendChild($a);
		$tr->appendChild($td);
		
		$table->appendChild($tr);
		
		$tr = $nt->_htmlelements('tr',$dom);
		
		$element = array('attr'=>'class=formLabel');
		$td = $nt->_htmlelements('td',$dom,$element);
		$element = array('value'=>'Use recipe icons: ');
		$label = $nf->_formelements('label',$dom,$element);
		$td->appendChild($label);
		$tr->appendChild($td);
		
		$element = array('attr'=>'class=formInput');
		$td = $nt->_htmlelements('td',$dom,$element);
		$element = array('attr'=>'type=radio&name=use_icons&id=use_icons_yes&value=yes');
		$input = $nf->_formelements('input',$dom,$element);
		$td->appendChild($input);
		$element = array('value'=>'Yes');
		$label = $nf->_formelements('label',$dom,$element);
		$td->appendChild($label);
		$tr->appendChild($td);
		
		$element = array('attr'=>'class=formInput');
		$td = $nt->_htmlelements('td',$dom,$element);
		$element = array('attr'=>'type=radio&name=use_icons&id=use_icons_no&value=no&checked=checked');
		$input = $nf->_formelements('input',$dom,$element);
		$td->appendChild($input);
		$element = array('value'=>'No');
		$label = $nf->_formelements('label',$dom,$element);
		$td->appendChild($label);
		$tr->appendChild($td);
		
		$element = array('attr'=>'class=formHelp');
		$td = $nt->_htmlelements('td',$dom,$element);
		$element = array('value'=>'?','a'=>"href=data/help/use_icons.html&rel=ibox&Use Recipe Icons");
		$a = $nt->_htmlelements('a',$dom,$element);
		$td->appendChild($a);
		$tr->appendChild($td);
		
		$table->appendChild($tr);
		
		$tr = $nt->_htmlelements('tr',$dom);
		
		$element = array('attr'=>'class=formLabel');
		$td = $nt->_htmlelements('td',$dom,$element);
		$element = array('value'=>'Use  page fillers: ');
		$label = $nf->_formelements('label',$dom,$element);
		$td->appendChild($label);
		$tr->appendChild($td);
		
		$element = array('attr'=>'class=formInput');
		$td = $nt->_htmlelements('td',$dom,$element);
		$element = array('attr'=>'type=radio&name=use_fillers&id=use_fillers_yes&value=yes');
		$input = $nf->_formelements('input',$dom,$element);
		$td->appendChild($input);
		$element = array('value'=>'Yes');
		$label = $nf->_formelements('label',$dom,$element);
		$td->appendChild($label);
		$tr->appendChild($td);
		
		$element = array('attr'=>'class=formInput');
		$td = $nt->_htmlelements('td',$dom,$element);
		$element = array('attr'=>'type=radio&name=use_fillers&id=use_fillers_no&value=no&checked=checked');
		$input = $nf->_formelements('input',$dom,$element);
		$td->appendChild($input);
		$element = array('value'=>'No');
		$label = $nf->_formelements('label',$dom,$element);
		$td->appendChild($label);
		$tr->appendChild($td);
		
		$element = array('attr'=>'class=formHelp');
		$td = $nt->_htmlelements('td',$dom,$element);
		$element = array('value'=>'?','a'=>"href=data/help/use_fillers.html&rel=ibox&Use Page Fillers");
		$a = $nt->_htmlelements('a',$dom,$element);
		$td->appendChild($a);
		$tr->appendChild($td);
		
		$table->appendChild($tr);
		
		$tr = $nt->_htmlelements('tr',$dom);
		
		$element = array('attr'=>'class=formLabel');
		$td = $nt->_htmlelements('td',$dom,$element);
		$element = array('value'=>'Filler Type: ');
		$label = $nf->_formelements('label',$dom,$element);
		$td->appendChild($label);
		$tr->appendChild($td);
		
		$element = array('attr'=>'class=formInput&colspan=2');
		$td = $nt->_htmlelements('td',$dom,$element);
		$element = array('attr'=>'name=filler_type&id=filler_type&disabled=disabled&class=disabled','options'=>'0: -- |text_fillers:Text Fillers|image_fillers:Image Fillers');
		$select = $nf->_formelements('select',$dom,$element);
		$td->appendChild($select);
		$tr->appendChild($td);
		
		$element = array('attr'=>'class=formHelp');
		$td = $nt->_htmlelements('td',$dom,$element);
		$element = array('value'=>' ');
		$a = $nt->_htmlelements('a',$dom,$element);
		$td->appendChild($a);
		$tr->appendChild($td);
		
		$table->appendChild($tr);
		
		$tr = $nt->_htmlelements('tr',$dom);
		
		$element = array('attr'=>'class=formLabel');
		$td = $nt->_htmlelements('td',$dom,$element);
		$element = array('value'=>'Filler Set: ');
		$label = $nf->_formelements('label',$dom,$element);
		$td->appendChild($label);
		$tr->appendChild($td);
		
		$element = array('attr'=>'class=formInput&colspan=2&id=filler_set_td');
		$td = $nt->_htmlelements('td',$dom,$element);
		$element = array('attr'=>'name=filler_set&id=filler_set&class=disabled&disabled=disabled');
		$select = $nf->_formelements('select',$dom,$element);
		$td->appendChild($select);
		$tr->appendChild($td);
		
		$element = array('attr'=>'class=formHelp');
		$td = $nt->_htmlelements('td',$dom,$element);
		$element = array('value'=>" ");
		$a = $nt->_htmlelements('a',$dom,$element);
		$td->appendChild($a);
		$tr->appendChild($td);
		
		$table->appendChild($tr);
		
		$tr = $nt->_htmlelements('tr',$dom);
		
		$element = array('attr'=>'class=formCenter&colspan=4');
		$td = $nt->_htmlelements('td',$dom,$element);
		$element = array('value'=>'Show me the sets','a'=>"href=#&onclick=showFillers()");
		$a = $nt->_htmlelements('a',$dom,$element);
		$td->appendChild($a);
		$tr->appendChild($td);
		
		$table->appendChild($tr);
		
		$tr = $nt->_htmlelements('tr',$dom);
		
		$element = array('attr'=>'class=spacer&colspan=6');
		$td = $nt->_htmlelements('td',$dom,$element);
		$tr->appendChild($td);
		
		$table->appendChild($tr);
		
		$tr = $nt->_htmlelements('tr',$dom);
		
		$element = array('attr'=>'class=formLeft&colspan=4');
		$td = $nt->_htmlelements('td',$dom,$element);
		$element = array('value'=>'Note to your users that appears on the recipe entry pages: ');
		$label = $nf->_formelements('label',$dom,$element);
		$td->appendChild($label);
		$tr->appendChild($td);
		
		$table->appendChild($tr);
		
		$tr = $nt->_htmlelements('tr',$dom);
		
		$element = array('attr'=>'class=formCenter&colspan=4');
		$td = $nt->_htmlelements('td',$dom,$element);
		$element = array('attr'=>'name=recipe_note&id=recipe_note&rows=8');
		$label = $nf->_formelements('textarea',$dom,$element);
		$td->appendChild($label);
		$tr->appendChild($td);
		
		$table->appendChild($tr);
		
		$tr = $nt->_htmlelements('tr',$dom);
		
		$element = array('attr'=>'class=spacer&colspan=6');
		$td = $nt->_htmlelements('td',$dom,$element);
		$tr->appendChild($td);
		
		$table->appendChild($tr);
	
		$leftdiv->appendChild($table);
		$form->appendChild($leftdiv);
		
		// RIGHT DIV
		$element = array('attr'=>'class=optionsRightDiv');
		$rightdiv = $nt->_htmlelements('div',$dom,$element);
		
		$element = array('title'=>'Recipe Formats','title_class'=>'wizardDGTitle','container_class'=>'wizardDGContainer','name'=>'recipe_format','columns'=>'1');
		$raw_source = $this->getXMLData('recipe_formats');
		$source = array();
		foreach($raw_source['format'] AS $f) {
			$source[] = $f;
		}
		$datagrid = $dg->_datagrid($dom,$source,$element,'recipe_formats');
		
		$rightdiv->appendChild($datagrid);
		$form->appendChild($rightdiv);
		
		// BASE DIV
		$element = array('attr'=>'class=optionsBaseDiv');
		$basediv = $nt->_htmlelements('div',$dom,$element);
		
		$table = $nt->_htmlelements('table',$dom);
		
		$tr = $nt->_htmlelements('tr',$dom);
		
		/*$tr = $nt->_htmlelements('tr',$dom);
		
		$element = array('attr'=>'class=optionsBackSubmit');
		$td = $nt->_htmlelements('div',$dom,$element);
		$element = array('attr'=>'type=submit&name=back&id=back&value=Back...');
		$input = $nf->_formelements('input',$dom,$element);
		$td->appendChild($input);
		$tr->appendChild($td);*/
		
		$element = array('attr'=>'class=optionsNextSubmit');
		$td = $nt->_htmlelements('div',$dom,$element);
		$element = array('attr'=>'type=submit&name=next&id=back&value=Save and Add Recipes...');
		$input = $nf->_formelements('input',$dom,$element);
		$td->appendChild($input);
		$tr->appendChild($td);
		
		$table->appendChild($tr);
		
		$basediv->appendChild($table);
		$form->appendChild($basediv);
								
		$dom->appendChild($form);
		
		return($dom->saveHTML());
		
	}
	
	public function customerSignup() {		
		
		$dom = new DOMDocument('1.0');
		
		$organization_types = $this->getOrganizationTypesList();
		
		$element = array(
								'form'=>array(
									array('name'=>'name','value'=>'set_signup'),
									array('name'=>'id','value'=>'set_signup'),
									array('name'=>'method','value'=>'POST')
								),
								'hidden'=>array(
									array('name'=>'action','value'=>'set_signup'),
									array('name'=>'act_switch','value'=>'warning')
								)
							);
		
		$nf = new FormElements();
		$nt = new HTMLElements();
		
		$form = $nf->_formelements('form',$dom,$element);
		
		$table = $nt->_htmlelements('table',$dom);
		
		$element = array('value'=>'SIGN UPNOW','attr'=>'colspan=3');
		$th = $nt->_htmlelements('th',$dom,$element);
		
		$table->appendChild($th);
		
		$tr = $nt->_htmlelements('tr',$dom);
		
		$element = array('attr'=>'class=formLabel');
		$td = $nt->_htmlelements('td',$dom,$element);
		$element = array('value'=>'Project Name: ','req'=>'true');
		$label = $nf->_formelements('label',$dom,$element);
		$td->appendChild($label);
		$tr->appendChild($td);
		
		$element = array('attr'=>'class=formInput');
		$td = $nt->_htmlelements('td',$dom,$element);
		$element = array('attr'=>'type=text&name=project_name&id=project_name');
		$input = $nf->_formelements('input',$dom,$element);
		$td->appendChild($input);
		$tr->appendChild($td);
		
		$element = array('attr'=>'class=formHelp');
		$td = $nt->_htmlelements('td',$dom,$element);
		$element = array('value'=>'?','a'=>"href=data/help/project_name.html&rel=ibox&title=Project Name");
		$a = $nt->_htmlelements('a',$dom,$element);
		$td->appendChild($a);
		$tr->appendChild($td);
		
		$table->appendChild($tr);
		
		$tr = $nt->_htmlelements('tr',$dom);
		
		$element = array('attr'=>'class=formLabel');
		$td = $nt->_htmlelements('td',$dom,$element);
		$element = array('value'=>'Organization Type: ');
		$label = $nf->_formelements('label',$dom,$element);
		$td->appendChild($label);
		$tr->appendChild($td);
		
		$element = array('attr'=>'class=formInput');
		$td = $nt->_htmlelements('td',$dom,$element);
		$element = array('attr'=>'name=organization_type&id=organization_type','options'=>$organization_types);
		$select = $nf->_formelements('select',$dom,$element);
		$td->appendChild($select);
		$tr->appendChild($td);
		
		$element = array('attr'=>'class=formHelp');
		$td = $nt->_htmlelements('td',$dom,$element);
		$element = array('value'=>'?','a'=>"href=data/help/organization_type.html&rel=ibox&title=Organization Type");
		$a = $nt->_htmlelements('a',$dom,$element);
		$td->appendChild($a);
		$tr->appendChild($td);
		
		$table->appendChild($tr);
		
		$tr = $nt->_htmlelements('tr',$dom);
		
		$element = array('attr'=>'class=formLabel');
		$td = $nt->_htmlelements('td',$dom,$element);
		$element = array('value'=>'&nbsp;');
		$label = $nf->_formelements('label',$dom,$element);
		$td->appendChild($label);
		$tr->appendChild($td);
		
		$element = array('attr'=>'class=formInput&colspan=2');
		$td = $nt->_htmlelements('td',$dom,$element);
		$element = array('attr'=>'type=text&name=other_type&id=other_type&class=disabled&disabled=disabled');
		$input = $nf->_formelements('input',$dom,$element);
		$td->appendChild($input);
		$tr->appendChild($td);
		
		$table->appendChild($tr);
		
		$tr = $nt->_htmlelements('tr',$dom);
		
		$element = array('attr'=>'class=formLabel');
		$td = $nt->_htmlelements('td',$dom,$element);
		$element = array('value'=>'Organization Name: ');
		$label = $nf->_formelements('label',$dom,$element);
		$td->appendChild($label);
		$tr->appendChild($td);
		
		$element = array('attr'=>'class=formInput');
		$td = $nt->_htmlelements('td',$dom,$element);
		$element = array('attr'=>'type=text&name=organization_name&id=organization_name');
		$input = $nf->_formelements('input',$dom,$element);
		$td->appendChild($input);
		$tr->appendChild($td);
		
		$element = array('attr'=>'class=formHelp');
		$td = $nt->_htmlelements('td',$dom,$element);
		$element = array('value'=>'?','a'=>"href=data/help/organization_name.html&rel=ibox&title=Organization Name");
		$a = $nt->_htmlelements('a',$dom,$element);
		$td->appendChild($a);
		$tr->appendChild($td);
		
		$table->appendChild($tr);
		
		$tr = $nt->_htmlelements('tr',$dom);
		
		$element = array('attr'=>'class=formLabel');
		$td = $nt->_htmlelements('td',$dom,$element);
		$element = array('value'=>'Name (first/last): ','req'=>'true');
		$label = $nf->_formelements('label',$dom,$element);
		$td->appendChild($label);
		$tr->appendChild($td);
		
		$element = array('attr'=>'class=formInput&colspan=2');
		$td = $nt->_htmlelements('td',$dom,$element);
		$element = array('attr'=>'type=text&name=first_name&id=first_name&size=12');
		$input = $nf->_formelements('input',$dom,$element);
		$td->appendChild($input);
		
		$element = array('attr'=>'type=text&name=last_name&id=last_name&size=12');
		$input = $nf->_formelements('input',$dom,$element);
		$td->appendChild($input);
		$tr->appendChild($td);
		
		$table->appendChild($tr);
		
		$tr = $nt->_htmlelements('tr',$dom);
		
		$element = array('attr'=>'class=formLabel');
		$td = $nt->_htmlelements('td',$dom,$element);
		$element = array('value'=>'Email: ','req'=>'true');
		$label = $nf->_formelements('label',$dom,$element);
		$td->appendChild($label);
		$tr->appendChild($td);
		
		$element = array('attr'=>'class=formInput&colspan=2');
		$td = $nt->_htmlelements('td',$dom,$element);
		$element = array('attr'=>'type=text&name=email&id=email');
		$input = $nf->_formelements('input',$dom,$element);
		$td->appendChild($input);
		$tr->appendChild($td);
		
		$table->appendChild($tr);
		
		$tr = $nt->_htmlelements('tr',$dom);
		
		$element = array('attr'=>'class=formLabel');
		$td = $nt->_htmlelements('td',$dom,$element);
		$element = array('value'=>'Username: ','req'=>'true');
		$label = $nf->_formelements('label',$dom,$element);
		$td->appendChild($label);
		$tr->appendChild($td);
		
		$element = array('attr'=>'class=formInput&colspan=2');
		$td = $nt->_htmlelements('td',$dom,$element);
		$element = array('attr'=>'type=text&name=username&id=username');
		$input = $nf->_formelements('input',$dom,$element);
		$td->appendChild($input);
		$tr->appendChild($td);
		
		$table->appendChild($tr);
		
		$tr = $nt->_htmlelements('tr',$dom);
		
		$element = array('attr'=>'class=formLabel');
		$td = $nt->_htmlelements('td',$dom,$element);
		$element = array('value'=>'Password: ','req'=>'true');
		$label = $nf->_formelements('label',$dom,$element);
		$td->appendChild($label);
		$tr->appendChild($td);
		
		$element = array('attr'=>'class=formInput&colspan=2');
		$td = $nt->_htmlelements('td',$dom,$element);
		$element = array('attr'=>'type=password&name=password&id=password');
		$input = $nf->_formelements('input',$dom,$element);
		$td->appendChild($input);
		$tr->appendChild($td);
		
		$table->appendChild($tr);
		
		$tr = $nt->_htmlelements('tr',$dom);
		
		$element = array('attr'=>'class=formLabel&colspan=2');
		$td = $nt->_htmlelements('td',$dom,$element);
		$element = array('value'=>'I want to try out UTypeIt first');
		$label = $nf->_formelements('label',$dom,$element);
		$td->appendChild($label);
		$tr->appendChild($td);
		
		$element = array('attr'=>'class=formInput');
		$td = $nt->_htmlelements('td',$dom,$element);
		$element = array('attr'=>'type=radio&name=account_type&id=acount_type&value=demo&onclick=setAct(this)');
		$input = $nf->_formelements('input',$dom,$element);
		$td->appendChild($input);
		$tr->appendChild($td);
		
		$table->appendChild($tr);
		
		$tr = $nt->_htmlelements('tr',$dom);
		
		$element = array('attr'=>'class=formLabel&colspan=2');
		$td = $nt->_htmlelements('td',$dom,$element);
		$element = array('value'=>'I\'m ready to start my cookbook');
		$label = $nf->_formelements('label',$dom,$element);
		$td->appendChild($label);
		$tr->appendChild($td);
		
		$element = array('attr'=>'class=formInput');
		$td = $nt->_htmlelements('td',$dom,$element);
		$element = array('attr'=>'type=radio&name=account_type&id=acount_type&value=live&checked=checked&onclick=setAct(this)');
		$input = $nf->_formelements('input',$dom,$element);
		$td->appendChild($input);
		$tr->appendChild($td);
		
		$table->appendChild($tr);
		
		$tr = $nt->_htmlelements('tr',$dom);
		
		$element = array('attr'=>'class=formRight&colspan=3');
		$td = $nt->_htmlelements('td',$dom,$element);
		$element = array('attr'=>'type=submit&name=signup&id=signup&value=Sign Up');
		$input = $nf->_formelements('input',$dom,$element);
		$td->appendChild($input);
		$tr->appendChild($td);
		
		$table->appendChild($tr);
		
		$form->appendChild($table);
		$dom->appendChild($form);
		return($dom->saveHTML());
		
	}

}

?>