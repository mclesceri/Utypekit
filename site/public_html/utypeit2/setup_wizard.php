<?php
define('FORCE_HTTPS', true);

session_start();

//ini_set('display_errors',1);
//error_reporting(-1);

require_once('src/globals.php');

require_once(SERVICES.'Orders.php');
require_once(SERVICES.'People.php');

require_once(INCLUDES.'Elements.php');
require_once(INCLUDES.'Order.php');
require_once(INCLUDES.'ColumnList.php');

$ne = new Elements();
$no = new Orders();
$np = new People();

$page = 'setup_wizard';
$action = '';
if(isset($_GET['action'])) {
	$action = $_GET['action'];
}

if($action == 'upgrade') {
    if(isset($_SESSION['order_id'])) {
        unset($_SESSION['order_id']);
        unset($_SESSION['order_number']);
    }
}

$step = 1;
$tabindex = 0;

$fileref = DATA.'xml/recipe_formats.xml';
$data = simplexml_load_file($fileref);
$data_set = json_encode($data);
$format_array = json_decode($data_set);
$format_count = count($format_array->format);
$script = "
    		var format_count = ".$format_count.";
			var wizard;
			document.observe('dom:loaded',function(){
				wizard = new Wizard();
				///var cl = new ColumnList({type: 'div',root: 'slide_5', drag: true});
				cl = new ColumnList({type: 'div',root: 'list_parent', drag: false});
			});
";

$organization_type = '';
$organization_name = '';
$organization_id = '';
$first_name = '';
$last_name = '';
$login = '';
$email = '';
$phone = '';
$address1 = '';
$address2 = '';
$city = '';
$state = '';
$zip = '';

/*
TEST VARIABLES

$first_name = 'asdf';
$last_name = 'asdf';
$login = 'asdf';
$password = 'asdf';
$email = 'asdf@adsf.com';
$title  = 'asdf';
*/
if($action == "upgrade") {
    foreach($_SESSION['user'] AS $key=>$val) {
    	if($val) {
			$$key = urldecode($val);
		}
    }
    if($organization_id != 0) {
        $organization = $np->sendAndGetOne('SELECT name,type FROM Organizations WHERE id="'.$organization_id.'"');
        $organization_name = $organization->name;
        $organization_type = $organization->type;
    }
}

$out = '
			<!-- WIZARD FORM /-->
			<form id="signup_wizard" name="signup_wizard">';
			if($action == 'upgrade') {
			    $out .= '
			    <input type="hidden" name="added_by_id" value="'.$_SESSION['user']->id.'">';
			}
			$out .= '
			<input type="hidden" name="action" value="signup" />
			<input type="hidden" name="added_by_type" value="1" />
			<div id="form_container">
				<div id="form_slider">
					<!-- START : Account Holder /-->
					<div id="slide_1" class="slide">
						<table>
							<tr>
								<td class="label">Organization Type: </td>
								<td class="input">';
								$out .= '
									<select name="organization_type" id="organization_type" tabindex="'.$tabindex++.'">
										<option value="0"> -- </option>';
										$type_res = array(
												'Business',
												'School',
												'Church',
												'Family',
												'Civic',
												'Military',
												'State Agency',
												'Lodge',
												'Pageant',
												'Daycare/Preschool'
											);
										//$type_res = $no->sendAndGetMany('SELECT DISTINCT(type) FROM Organizations WHERE status="1"');
										foreach($type_res AS $t) {
											//if($t != '') {
												//if($t->type != 'other') {
    											$out .= '
                                        <option value="'.$t.'">'.$t.'</option>';
                                                //}
											//}
										}
										$out .= '<option value="other">Other</option>
									</select>
									<a href="'.HELP.'organization_type.html" class="lightwindow help" params="lightwindow_width=500,lightwindow_height=300"title="Organization Type">?</a>
								</td>
								<td class="label">Organization Name: </td>
								<td class="input"><input type="text" name="organization_name" id="organization_name" value="'.$organization_name.'" tabindex="'.$tabindex++.'" /><a href="'.HELP.'organization_name.html" class="lightwindow help" params="lightwindow_width=500,lightwindow_height=300"title="Organization Name">?</a></td>
							</tr>
							<tr>
								<td>&nbsp;</td>
								<td class="input"><input type="text" name="other_type" id="other_type" disabled="disabled" tabindex="'.$tabindex++.'" /></td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
							</tr>';
							if($action == 'upgrade') {
							    $out .= '
							<tr>
								<td class="label"><span style="color: red;">*</span> First Name: </td>
								<td class="input"><input type="hidden" name="first_name" id="first_name" value="'.$first_name.'" />'.$first_name.'</td>
								<td class="label"><span style="color: red;">*</span> Last Name: </td>
								<td class="input"><input type="hidden" name="last_name" id="last_name" value="'.$last_name.'" />'.$last_name.'</td>
							</tr>
							    <input type="hidden" name="login" id="login" value="'.$login.'" />
                                <input type="hidden" name="password" id="password" value="'.$password.'" />';
                            } else {
                                $out .= '
							<tr>
								<td class="label"><span style="color: red;">*</span> Username: </td>
								<td class="input"><input type="text" name="login" id="login" tabindex="'.$tabindex++.'" maxlength="15" /></td>
								<td class="label"><span style="color: red;">*</span> Password: </td>
								<td class="input"><input type="password" name="password" id="password" tabindex="'.$tabindex++.'" maxlength="15" /></td>
							</tr>
							<tr>
								<td colspan="4" style="text-align: center; font-size: .9em; color: #333333">NOTE: Username and password are limited to 15 characters each. </td>
							</tr>
							<tr>
                                <td class="label"><span style="color: red;">*</span> First Name: </td>
                                <td class="input"><input type="text" name="first_name" id="first_name" tabindex="'.$tabindex++.'"/></td>
                                <td class="label"><span style="color: red;">*</span> Last Name: </td>
                                <td class="input"><input type="text" name="last_name" id="last_name"  tabindex="'.$tabindex++.'"/></td>
                            </tr>
							';
							}
							$out .= ' 
							<tr>
								<td class="label"><span style="color: red;">*</span> Email: </td>
								<td class="input"><input type="text" name="email" id="email" value="'.$email.'"  tabindex="'.$tabindex++.'"/></td>
								<td class="label">Phone: </td>
								<td class="input"><input type="text" name="phone" id="phone" value="'.$phone.'"  tabindex="'.$tabindex++.'"/></td>
							</tr>
							<tr>
								<td class="label">Address 1: </td>
								<td class="input"><input type="text" name="address1" id="address1" value="'.$address1.'"  tabindex="'.$tabindex++.'"/></td>
								<td class="label">Address 2: </td>
								<td class="input"><input type="text" name="address2" id="address2" value="'.$address2.'"  tabindex="'.$tabindex++.'"/></td>
							</tr>
							<tr>
								<td class="label">City/State/Zip: </td>
								<td class="input" colspan="3">
									<input type="text" name="city" id="city" value="'.$city.'"  tabindex="'.$tabindex++.'"/>
									<select name="state" id="state" tabindex="'.$tabindex++.'">
									   <option value="">Select One... </option>';
                                        $states = array(
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
										foreach($states AS $key=>$val) {
										    $selected  = '';
										    if($state == $key) {
										        $selected = 'selected="selected"';
										    }
										    $out .= '<option value="'.$key.'"'.$selected.'>'.$val.'</option>';
										}
									$out .= '</select>
									<input type="text" name="zip" id="zip" size="7" value="'.$zip.'" tabindex="'.$tabindex++.'"/>
								</td>
							</tr>
							<tr>
								<td class="label" colspan="2"><span>I want to try out U-Type-It&trade; first</span></td>
								<td class="input" colspan="2"><input name="account_type" id="account_type_demo" value="demo" type="radio"';
                                if($action == 'upgrade') {
                                    $out .= ' style="disabled" disabled="disabled"';
                                }
								 $out .= ' tabindex="'.$tabindex++.'"></td>
							</tr>
							<tr>
								<td class="label" colspan="2"><span>I\'m ready to start my cookbook</span></td>
								<td class="input" colspan="2"><input name="account_type" id="account_type_live" value="live" checked="" type="radio" tabindex="'.$tabindex++.'"></td>
							</tr>
							<tr>
								<td class="label" colspan="2"><span>Sign me up for the Cookbook newsletter</span></td>
								<td class="input" colspan="2"><input name="newsletter" id="newsletter" value="yes" checked="" type="checkbox" tabindex="'.$tabindex++.'"></td>
							</tr>
						</table>
					</div>
					<!-- END /-->
					<!-- START : Signup Options /-->
					<div id="slide_2" class="slide">
						<table>
							<tr>
								<td class="label" style="width: 50%"><span style="color: red;">*</span>Enter an Order Title: </td>
								<td class="input"><input type="text" name="title" id="title" /><a href="'.HELP.'order_title.html" class="lightwindow help" params="lightwindow_width=500,lightwindow_height=300"title="Order Title">?</a></td>
							</tr>
							<tr>
								<td class="label"><span>I want to add more contributors to my order</span></td>
								<td class="input"><input class="screen" type="checkbox" name="add_contributors" rel="3" id="add_contributors"><a href="'.HELP.'order_options.html" class="lightwindow help" params="lightwindow_width=500,lightwindow_height=300"title="Order Title">?</a></td>
							</tr>
							<tr>
								<td class="label"><span>I want to set up my cookbook options now</span><br />
									<span style="font-size: 0.8em">(cookbook title, book style, number of books, etc.)</span></td>
								<td class="input"><input class="screen" type="checkbox" name="cookbook_options" rel="4" id="cookbook_options""></td>
							</tr>
							<tr>
								<td class="label"><span>I want to set up my recipe sections<br />
									<span style="font-size: 0.8em">(categories, subcategories)</span></td>
								<td class="input"><input class="screen" type="checkbox" name="recipe_sections" rel="5" id="recipe_sections"></td>
							</tr>
							<tr>
								<td class="label"><span>I want to set up my recipe format and options now</span></td>
								<td class="input"><input class="screen" type="checkbox" name="recipe_options" rel="6" id="recipe_options"></td>
							</tr>
							<tr>
								<td colspan="2">&nbsp;</td>
							</tr>
							<tr>
								<td class="label"><span style="color: red">*</span><span>I agree to the <a href="'.UTI_URL.'src/data/html/terms_of_service.html" class="lightwindow" params="lightwindow_width=500,lightwindow_height=300" title="Terms of Service">Terms of Service</a></span></td>
								<td class="input"><input class="screen" type="checkbox" name="terms_of_service" rel="terms" id="terms_of_service"></td>
							</tr>
						</table>
					</div>
					<!-- END /-->
					<!-- START : Add Users /-->
					<div id="slide_3" class="slide">
						<table id="contributor">
							<tr>
								<td class="label"><span style="color: red;">*</span> User Level: </td>
								<td class="input">
									<select name="contributor_order_level" id="contributor_order_level">
										<option value="">Select One...</option>
										<option value="2">Contributor</option>
										<!--<option value="3">Committee Member</option>/-->
										<option value="4">Cochairperson</option>
									</select>
								</td>
								<td class="input"><a href="'.HELP.'user_level.html" class="lightwindow help" params="lightwindow_width=500,lightwindow_height=300"title="User Level">?</a></td>
								<td>&nbsp;</td>
								<td>Contributors for this order,,,</td>
							</tr>
							<tr>
								<td class="label"><span style="color: red;">*</span> First Name: </td>
								<td class="input"><input type="text" name="contributor_first_name" id="contributor_first_name"  value=""/></td>
								<td class="label"><span style="color: red;">*</span> Last Name: </td>
								<td class="input"><input type="text" name="contributor_last_name" id="contributor_last_name"  value=""/></td>
								<td rowspan="5" id="contributor_list"></td>
							</tr>
							<tr>
								<td class="label"><span style="color: red;">*</span> Username: </td>
								<td class="input"><input type="text" name="contributor_login" id="contributor_login"  value="" maxlength="15"/></td>
								<td class="label"><span style="color: red;">*</span> Password: </td>
								<td class="input"><input type="text" name="contributor_password" id="contributor_password"  value="" maxlength="15"/></td>
							</tr>
							<tr>
								<td colspan="4" style="text-align: center; font-size: .9em; color: #333333">NOTE: Username and password are limited to 15 characters each. </td>
							</tr>
							<tr>
								<td class="label"><span style="color: red;">*</span> Email: </td>
								<td class="input"><input type="text" name="contributor_email" id="contributor_email" /></td>
								<td class="label">Phone: </td>
								<td class="input"><input type="text" name="contributor_phone" id="contributor_phone" /></td>
							</tr>
							<tr>
								<td class="label">Address 1: </td>
								<td class="input"><input type="text" name="contributor_address1" id="contributor_address1" /></td>
								<td class="label">Address 2: </td>
								<td class="input"><input type="text" name="contributor_address2" id="contributor_address2" /></td>
							</tr>
							<tr>
								<td class="label">City/State/Zip: </td>
								<td class="input" colspan="3">
									<input type="text" name="contributor_city" id="contributor_city" />
									<select name="contributor_state" id="contributor_state">
										<option value="">Select One...</option>
										<option value="AL">Alabama</option>
										<option value="AK">Alaska</option>
										<option value="AZ">Arizona</option>
										<option value="AR">Arkansas</option>  
										<option value="CA">California</option>  
										<option value="CO">Colorado</option>  
										<option value="CT">Connecticut</option>  
										<option value="DE">Delaware</option>  
										<option value="DC">District Of Columbia</option>  
										<option value="FL">Florida</option>  
										<option value="GA">Georgia</option>  
										<option value="HI">Hawaii</option>  
										<option value="ID">Idaho</option>  
										<option value="IL">Illinois</option>  
										<option value="IN">Indiana</option>  
										<option value="IA">Iowa</option>  
										<option value="KS">Kansas</option>  
										<option value="KY">Kentucky</option>  
										<option value="LA">Louisiana</option>  
										<option value="ME">Maine</option>
										<option value="MD">Maryland</option>
										<option value="MA">Massachusetts</option>
										<option value="MI">Michigan</option>
										<option value="MN">Minnesota</option>
										<option value="MS">Mississippi</option>
										<option value="MO">Missouri</option>
										<option value="MT">Montana</option>
										<option value="NE">Nebraska</option>  
										<option value="NV">Nevada</option>  
										<option value="NH">New Hampshire</option>  
										<option value="NJ">New Jersey</option>  
										<option value="NM">New Mexico</option>
										<option value="NY">New York</option>  
										<option value="NC">North Carolina</option>
										<option value="ND">North Dakota</option>
										<option value="OH">Ohio</option>  
										<option value="OK">Oklahoma</option>
										<option value="OR">Oregon</option>
										<option value="PA">Pennsylvania</option>
										<option value="RI">Rhode Island</option>
										<option value="SC">South Carolina</option>
										<option value="SD">South Dakota</option>
										<option value="TN">Tennessee</option>
										<option value="TX">Texas</option>
										<option value="UT">Utah</option>
										<option value="VT">Vermont</option>  
										<option value="VA">Virginia</option>
										<option value="WA">Washington</option>  
										<option value="WV">West Virginia</option>  
										<option value="WI">Wisconsin</option>
										<option value="WY">Wyoming</option>
									</select>
									<input type="text" name="contributor_zip" id="contributor_zip" size="7" />
								</td>
							</tr>
                            <tr>
                                <td colspan="5">&nbsp;</td>
                            </tr>
                            <tr>
                                <td colspan="5"><strong>NOTE:</strong> If you only want to add one contributor, simply enter their information in the fields above and click "Next" above. You DO NOT have to use the buttons below unless you want to add multiple contributors for this order. If you want to add more than one contributor, enter the information in the fields above, then click on the "Add Another Contributor" button on the right, then enter the new information. If you change your mind and do not want to enter contributors at this time, click the "Cancel" button below.</td>
                            </tr>
                            <tr>
								<td colspan="2" class="input"><button type="button" onclick="wizard._cancel_contributor(); return false;">Cancel</button></td>
								<td>&nbsp;</td>
								<td colspan="2" class="submit"><button type="button" onclick="wizard._contributor(); return false;">Add Another Contributor</button></td>
							</tr>
						</table>
					</div>
					<!-- END /-->
					<!-- START : Order Options /-->
					<div id="slide_4" class="slide">
						<table>
							<tr>
								<td class="label">Cookbook Title: </td>
								<td class="input" rowspan="3">
								    <input type="text" name="book_title1" id="book_title1" maxlength="50" /><a href="'.HELP.'cookbook_title.html" class="lightwindow help" params="lightwindow_width=500,lightwindow_height=300"title="Cookbook Title">?</a><br /><br />
								    <input type="text" name="book_title2" id="book_title2" maxlength="50" /><br /><br />
                                    <input type="text" name="book_title3" id="book_title3" maxlength="50" />
                                </td>
								<td class="label">Book Style: </td>
								<td class="input">
									<select name="book_style">
										<option value="">Select one... </option>
										<option value="Soft Cover">Soft Cover</option>
										<option value="Hard Cover">Hard Cover</option>
										<option value="3-Ring Binder">3-Ring Binder</option>
									</select>
									<a href="'.HELP.'book_style.html" class="lightwindow help" params="lightwindow_width=500,lightwindow_height=300"title="Book Style">?</a>
								</td>
								<td rowspan="9">
									<table id="order_form_table" style="width: 300px; display: none;">
										<tr>
											<td colspan="2" style="text-align: center; border-bottom: 1px #333333 solid">Cookbook Order Form Information</td>
										</tr>
										<tr>
											<td class="label">Name</td>
											<td class="input"><input name="order_form_name" id="order_form_name" type="text"></td>
										</tr>
										<tr>
											<td class="label">Address 1:</td>
											<td class="input"><input name="order_form_address1" id="order_form_address1" type="text"></td>
										</tr>
										<tr>
											<td class="label">Address 2:</td>
											<td class="input"><input name="order_form_address2" id="order_form_address2" type="text"></td>
										</tr>
										<tr>
											<td class="label">City:</td>
											<td class="input"><input name="order_form_city" id="order_form_city" type="text"></td>
										</tr>
										<tr>
											<td class="label">State/Zip:</td>
											<td class="input">
												<input name="order_form_state" id="order_form_state" size="15" type="text"> <input name="order_form_zip" id="order_form_zip" size="7" type="text">
											</td>
										</tr>
										<tr>
											<td class="label">Retail Price:</td>
											<td class="input"><input name="order_form_retail" id="order_form_retail" type="text"></td>
										</tr>
										<tr>
											<td class="label">Shipping Fee:</td>
											<td class="input"><input name="order_form_shipping" id="order_form_shipping" value="3.00" type="text">
										</tr>
									</table>
								</td>
							</tr>
							<tr>
								<td class="label">(50 characters each)</td>
								<td class="label"># of Books to Order: </td>
								<td class="input"><input type="text" name="book_count" id="book_count" size="10" /><a href="'.HELP.'book_count.html" class="lightwindow help" params="lightwindow_width=500,lightwindow_height=300"title="Number of Books">?</a></td>
							</tr>
							<tr>
								<td class="label">&nbsp;</td>
								<td class="label"># of Recipes in Cookbook: </td>
								<td class="input"><input type="text" name="recipe_count" id="book_count" size="10" /><a href="'.HELP.'max_recipes.html" class="lightwindow help" params="lightwindow_width=500,lightwindow_height=300"title="Number of Recipes">?</a></td>
							</tr>
							<tr>
								<td class="label" colspan="3">Add <span style="font-weight: bold; color: red;">free</span> nutritional information pages to the cookbook?</td>
								<td class="input"><input type="checkbox" name="nutritionals" id="nutritionals" value="yes" checked="checked" /><a href="'.HELP.'nutritionals.html" class="lightwindow help" params="lightwindow_width=500,lightwindow_height=300"title="Nutritional Information Pages">?</a></td>
							</tr>
							<tr>
								<td class="label" colspan="3">Add a <span style="font-weight: bold; color: red;">free</span> contributor index?</td>
								<td class="input"><input type="checkbox" name="contributors" id="contributors" value="yes" checked="checked" /><a href="'.HELP.'contributor_index.html" class="lightwindow help" params="lightwindow_width=500,lightwindow_height=300"title="Contributor Index ">?</a></td>
							</tr>
							<tr>
								<td class="label" colspan="3">Add a <span style="font-weight: bold; color: red;">free</span> order form to the back of the cookbook?</td>
								<td class="input"><input type="checkbox" name="order_form" id="order_form" value="yes" /><a href="'.HELP.'order_form.html" class="lightwindow help" params="lightwindow_width=500,lightwindow_height=300"title="Cookbook Order Form">?</a></td>
							</tr>
							<tr>
								<td class="label" colspan="3">Add subcategories to each category?</td>
								<td class="input designeroption_off"><input type="checkbox" name="use_subcategories" id="use_subcategories" value="yes" /><a href="'.HELP.'use_subcategories.html" class="lightwindow help" params="lightwindow_width=500,lightwindow_height=300"title="Section Subcategories">?</a></td>
							</tr>
							<tr>
								<td class="label" colspan="3">Recipe index type...</td>
								<td class="input">
									<select name="order_index_by" id="order_index_by">
										<option value="0" selected="selected" selected="selected">Select one... </option>
										<option value="alphabetical">Alphabetical</option>
										<option value="as entered">As Entered</option>
									</select>
									<a href="'.HELP.'order_index_by.html" class="lightwindow help" params="lightwindow_width=500,lightwindow_height=300" title="Recipe Index">?</a>
								</td>
							</tr>
						</table>
					</div>
					<!-- END /-->
					<!-- START : Recipe Sections /-->
					<div id="slide_5" class="slide">
						<table>
					           <tr>
					               <td><button type="button" id="category_button" onclick="cl._add(\'category\',\'list\'); return false;" style="margin-bottom: 5px">Add Category</button></td>
					               <td>Remove Item</td>
					               <td class="disabled">Show Subcategories</td>
					           </tr>
					    </table>
					   <table>
					           <tr>
					               <td><button type="button" id="subcategory_button" disabled="disabled" style="margin-bottom: 5px">Add Subcategory</button></td>
					               <td class="disabled">Remove Item</td>
					               <td></td>
					           </tr>
					    </table>
					   <div id="category" class="orderListColumn" parent="0">
					       <div class="orderListSection" number="1">
					           <input name="category-title_1-0-1" value="Appetizers, Beverages" type="text"><div class="orderListSectionControls"><img src="media/images/remove_button.png" onclick="cl._remove(this,\'list\')"></div>
					       </div>
					       <div class="orderListSection" number="2">
                               <input name="category-title_2-0-2" value="Soups, Salads" type="text"><div class="orderListSectionControls"><img src="media/images/remove_button.png" onclick="cl._remove(this,\'list\')"></div>
                           </div>
                           <div class="orderListSection" number="3">
                               <input name="category-title_3-0-3" value="Vegetables" type="text"><div class="orderListSectionControls"><img src="media/images/remove_button.png" onclick="cl._remove(this,\'list\')"></div>
                           </div>
                           <div class="orderListSection" number="4">
                               <input name="category-title_4-0-4" value="Main Dishes" type="text"><div class="orderListSectionControls"><img src="media/images/remove_button.png" onclick="cl._remove(this,\'list\')"></div>
                           </div>
                           <div class="orderListSection" number="5">
                               <input name="category-title_5-0-5" value="Breads, Rolls" type="text"><div class="orderListSectionControls"><img src="media/images/remove_button.png" onclick="cl._remove(this,\'list\')"></div>
                           </div>
                           <div class="orderListSection" number="6">
                               <input name="category-title_6-0-6" value="Desserts" type="text"><div class="orderListSectionControls"><img src="media/images/remove_button.png" onclick="cl._remove(this,\'list\')"></div>
                           </div>
                           <div class="orderListSection" number="7">
                               <input name="category-title_7-0-7" value="Miscellaneous" type="text"><div class="orderListSectionControls"><img src="media/images/remove_button.png" onclick="cl._remove(this,\'list\')"></div>
                           </div>
					   </div>
					   
					   <div id="subcategory" class="orderListColumn"></div>
					</div>
					<!-- END /-->
					<!-- START : Recipe Format & Design Options /-->
					<div id="slide_6" class="slide">
						<table style="float: left; width: 490px;">
							<tr>
								<td class="label">Order the recipes by...</td>
								<td class="input">
									<select name="order_recipes_by" id="order_recipes_by">
										<option value="0" selected="selected">Select one... </option>
										<option value="alpha">by Alphabet</option>
										<option value="custom">Custom Order</option>
									</select>
								</td>
								<td>&nbsp;</td>
							</tr>
							<tr>
								<td class="label">Recipes Continued<br /><span style="font-size: 0.75em">(Recipes may continue across pages)</span></td>
								<td class="input"><input type="radio" name="recipes_continued" id="recipes_continued_yes" value="yes" checked="checked" /></td>
								<td><a href="'.HELP.'recipes_continued.html" class="lightwindow help" params="lightwindow_width=500,lightwindow_height=300"title="Recipes Continued">?</a></td>
							</tr>
							<tr>
								<td class="label">Recipes Not Continued<br /><span style="font-size: 0.75em">(Recipes will not continue to another page)</span></td>
								<td class="input designeroption_off"><input type="radio" name="recipes_continued" id="recipes_continued_no" value="no" /></td>
								<td class="input designeroption_off">&nbsp;</td>
							</tr>
							<tr>
								<td class="label">Allow recipe notes?</td>
								<td class="input designeroption_off"><input type="radio" name="allow_notes" id="allow_notes_yes" value="yes" /> Yes &nbsp;<input type="radio" name="allow_notes" id="allow_notes_no" value="no" checked="checked" /> No</td>
								<td class="input designeroption_off"><a href="'.HELP.'recipe_notes.html" class="lightwindow help" params="lightwindow_width=500,lightwindow_height=300"title="Recipe Notes">?</a></td>
							</tr>
							<tr>
								<td class="label">Use recipe icons?</td>
								<td class="input designeroption_off"><input type="radio" name="use_icons" id="use_icons_yes" value="yes" /> Yes &nbsp;<input type="radio" name="use_icons" id="use_icons_no" value="no" checked="checked" /> No</td>
								<td class="input designeroption_off"><a href="'.HELP.'recipe_icons.html" class="lightwindow help" params="lightwindow_width=500,lightwindow_height=300"title="Recipe Icons">?</a></td>
							</tr>
							<tr>
								<td class="label disabled" id="page_fillers_label">Use page fillers?</td>
								<td class="input"><input type="radio" name="use_fillers" id="use_fillers_yes" value="yes" disabled="disabled" /> <span class="disabled">Yes</span> &nbsp;<input type="radio" name="use_fillers" id="use_fillers_no" value="yes" disabled="disabled" checked="checked" /> <span class="disabled">No</span></td>
								<td class="input"><a href="'.HELP.'use_fillers.html" class="lightwindow help" params="lightwindow_width=500,lightwindow_height=300"title="Page Fillers">?</a></td>
							</tr>
							<tr>
								<td class="label disabled" id="filler_type_label">Filler Type:</td>
								<td class="input">
									<select name="filler_type" id="filler_type" disabled="disabled">
										<option value="0" selected="selected"> -- </option>
										<option value="text_fillers">Text Fillers</option>
										<option value="image_fillers">Image Fillers</option>
									</select>
								</td>
								<td><a href="'.HELP.'filler_type.html" class="lightwindow help" params="lightwindow_width=500,lightwindow_height=300"title="Recipe Fillers">?</a></td>
							</tr>
							<tr>
                                <td class="label disabled" id="filler_set_label">Filler Set:</td>
                                <td class="input">
                                    <select name="filler_set" id="filler_set" disabled="disabled">
                                        <option value="0" selected="selected"> -- </option>
                                    </select>
                                </td>
                                <td><a href="#" onclick="setContent(\'pop_window\',{mode:\'popup\',action: \'filler_sets\'})" class="help" style="width: 120px;">Show Filler Sets</a></td>
                            </tr>
						</table>
						<div id="recipe_formats">
						  <div id="format_slider">
						      <div id="formats">';
                            foreach($format_array->format AS $a) {
                                $checked = '';
                                if($a->name == 'Traditional') {
                                    $checked = 'checked="checked"';
                                }
                                $out .= "
                                    <div class=\"format\">
                                        <a class='lightwindow' rel=\"Formats[Formats]\" href='".IMAGES.$a->image1."' title='".$a->description."'><img src=\"".IMAGES.$a->thumbnail1."\"></a><a class='lightwindow' rel=\"Formats[Formats]\" href='".IMAGES.$a->image2."' title='".$a->description."'><img src=\"".IMAGES.$a->thumbnail2."\"></a>
                                        <p";
                                        if($a->name == 'CentSaver') {
	                                        $out .= ' class="centsaver_off"';
                                        } else {
	                                        $add_exp = array('Premiere','Fanciful','Casual','Black Tie');
	                                        foreach($add_exp AS $e) {
		                                        if($a->name == $e) {
			                                        $out .= ' class="designeroption_off"';
		                                        }
	                                        }
                                        }
                                        $out .= '>'.$a->description.' <input type="radio" name="recipe_format" class="recipe_format" value="'.$a->name.'"';
                                        $out .= ' flag="'.$a->flag.'"'.$checked.' /></p>
                                    </div>';
                            }
							$out .= '
							 </div>
							</div>
							<div id="format_buttons">
								<button type="button" onclick="wizard._previous_format(); return false;"> < Last Format</button>
								<div id="format_button_spacer"></div>
								<button type="button" onclick="wizard._next_format(); return false;"> Next Format > </button>
							</div>
						</div>
					</div>
					<!-- END /-->
				</div>
			</div>
			</form>
			<!-- END WIZARD FORM /-->';

$header_left = '';
$header_middle = "Cookbook Publisher's UTypeIt Online&trade; Signup Wizard";
$header_right = "";

require_once(TEMPLATES.'wizard_header.tpl');
require_once(TEMPLATES.'wizard_footer.tpl');
$content = $out;

include(TEMPLATES.'wizard.tpl');

?>