<?php

session_start();

if(!$_SESSION['login'] == true) {
	header("Location: index.php");
}

require_once('src/globals.php');

if($_POST['action']) {
	$action = $_POST['action'];
}

$page = 'order_form';
$title = 'Submit My Order';

$order_id = $_GET['id'];

require_once(SERVICES.'Orders.php');
require_once(SERVICES.'People.php');
require_once(SERVICES.'Email.php');
require_once(SERVICES.'Transmit.php');

require_once(INCLUDES."Warnings.php");

$err = false;
$page_note = $_SESSION['utypeit_info']->welcome_note;

class makeOrderParts {

    var $order = null;
    var $general_info;
    var $utypeit_info;
    var $categories;
    var $subcategories;
    var $chairperson;
    var $cochairperson;
    var $contractor;
	
	function __construct($order) {
	   $this->order = $order;
       foreach($this->order AS $key=>$val) {
            switch($key) {
                case 'general_info':
                    $array_a = explode('|',$val);
                    $array_b = array();
                    foreach($array_a AS $b) {
                        $array_b[substr($b,0,strpos($b,':'))] = substr($b,strpos($b,':')+1);
                    }
                    $this->general_info = $array_b;
                    break;
                case 'utypeit_info':
                    $uti_arr = explode('|',$val);
                    $uti_data = array();
                    foreach($uti_arr AS $u) {
                        $data = explode(':',$u);
                        $uti_data[$data[0]] = $data[1];
                    }
                    $this->utypeit_info = $uti_data;
                    break;
                case 'categories':
                    // creates array( order:title, order:title, order:title);
                    $categories = json_decode($val);
                    $this->categories = $categories;
                    break;
                case 'subcategories':
                    $subcategories = json_decode($val);
                    $this->subcategories = $subcategories;
                    break;
            }
        }
        
        if($_SESSION['order_id'] == 1) {
            $chairperson = new stdClass();
                $chairperson->id = '3';
                $chairperson->organization_id = '0';
                $chairperson->first_name = 'Suzie'; 
                $chairperson->last_name = 'Homemaker';
                $chairperson->email = 'suzie@homemaker.com'; 
                $chairperson->phone = '123-123-1234';
                $chairperson->cell_phone = '123-123-1234';
                $chairperson->address1 = '123 My Street';
                $chairperson->address2 = '';
                $chairperson->city = 'Anytown';
                $chairperson->state = 'KS';
                $chairperson->zip = '66212';
                $chairperson->login = 'suzy';
                $chairperson->password = 'homemaker'; 
                $chairperson->level = '1';
                $chairperson->type = '1';
                $chairperson->meta = 'newsletter:no';
                $chairperson->date_added = '2011-11-28 17:05:05';
                $chairperson->added_by_type = '1';
                $chairperson->added_by_id = '3';
                $chairperson->date_modified = '2013-05-30 17:16:14';
                $chairperson->status = '1';
                $chairperson->order_level = '5'; 
            $this->chairperson = $chairperson;
            $this->cochairperson = $chairperson;
        } else {
            $this->chairperson = $order->chairperson;
            $this->cochairperson = $order->cochairperson;
            $this->contractor = $order->contractor;
        }
	}
    
    function _make() {
        
        $out = "<table id='order_form'>";    
        
        // Book block...
        $out .= $this->book_block($this->order->order,$this->general_info);
       
        // People block...
        $chairperson = null;
        if(!isset($this->chairperson)) {
            $out = 'Order submission requires one user to be designated as the chairperson. If you have any questions, please refer to the <a href="faq.php">FAQ</a>, or contact <a href="message_center.php?action=contact_cpi">Cookbook Publishers Inc.</a> for more information.';
            return($out);
        } else {
            $chairperson = $this->chairperson;
        }
        $cochairperson = null;
        if(isset($this->cochairperson)) {
            $cochairperson = $this->cochairperson;
        }
        $contractor = null;
        if(isset($this->contractor)) {
            $contractor = $this->contractor;
        }
        $out .= $this->people_block($chairperson,$cochairperson,$contractor);
        
        // Sections block...
        $categories = null;
        $categories = $this->categories;
        $subcategories = null;
        if($this->order->subcategories) {
            $subcategories = $this->subcategories;
        }
        $out .= $this->sections_block($categories,$subcategories);
        
        // Options block...
        $out .= $this->options_block($this->general_info);
        $demo = false;
        if($_SESSION['order_id'] == 1) {
            $demo = true;
        }
        $out .= "
            <tr>";
        if(!$demo) {
            $out .= "<td colspan=\"4\" class=\"formSubmit\"><form action=\"".$_SERVER['PHP_SELF']."\" method=\"POST\"><input type=\"hidden\" name=\"action\" value=\"submit\"><button id=\"order_submit\">Send to Publish</button></form></td>";
        } else {
            $out .= "<td colspan=\"4\">&nbsp;</td>";
        }
        $out .= "
            </tr>
        </table>";
        return($out);
    }
    
    function book_block($order,$info) {
        
        foreach($order AS $key=>$val) {
            $$key = stripslashes(urldecode($val));
        }
        foreach($info AS $key=>$val) {
            $$key = stripslashes(urldecode($val));
        }
        $out = '
            <tr>
                <td colspan="4"><h4>Order Information</h4></td>
            </tr>
            <tr>
                <td class="formLabel">Order Number: </td>
                <td class="formInput">'.$order_number.'</td>
                <td class="formLabel">Order ID: </td>
                <td class="formInput">'.$id.'</td>
            </tr>
            <tr>
                <td class="formLabel">Date Added: </td>
                <td class="formInput">'.$date_added.'</td>
                <td class="formLabel">Date Modified: </td>
                <td class="formInput">'.$date_modified.'</td>
            </tr>
            <tr>
                <td class="formLabel">Added by: </td>
                <td class="formInput"><a href="people_edit.php?action=user_edit&id='.$added_by_id.'">'.$added_by_id.'</a></td>
                <td class="formLabel">Added by Type: </td>';
                if($added_by_type == 1) {
                    $added_by_type = 'UTypeIt Online';   
                } else {
                    $added_by_type = 'CPI Administrator';
                }
                $out .= '<td class="formInput">'.$added_by_type.'</td>
            </tr>
            <tr>
                <td colspan="4"><h4>Cookbook Information</h4></td>
            </tr>
            <tr>
                <td class="formLabel">Book Title: </td>
                <td class="formInput">';
        if(!$book_title1) {
            $err = true;
            $out .= '<span class="required">BOOK TITLE REQUIRED</span>';
        } else {
            $out .= $book_title1;
        }
        $out .= '</td>
                <td class="formLabel">Book Style: </td>
                <td class="formInput">';
        if(!$book_style) {
            $err = true;
            $out .= '<span class="required">BOOK STYLE REQUIRED</span>';
        } else {
            $out .= $book_style;
        }
        $out .= '
                </td>
            </tr>
            <tr>
                <td>&nbsp;</td>
                <td class="formInput">'.$book_title2.'</td>
                <td class="formLabel"># Books:</td>
                <td>';
        if(!$book_count) {
            $err = true;
            $out .= '<span class="required">BOOK COUNT REQUIRED</span>';
        } else {
            $out .= $book_count;
        }
        $out .= '
                </td>
            </tr>';
        if($book_title3) {
            $out .= '<tr>
                <td>&nbsp;</td>
                <td class="formInput">'.$book_title3.'</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
            </tr>';
        }
        return($out);
    }
    
    function people_block($chairperson,$cochairperson,$contractor) {
        $out = '
            <tr>
                <td colspan="4"><h4>Contact Information</h4>
            </tr>
            <tr>
                <td class="formLabel">Chairperson: </td>
                <td class="formInput">'.stripslashes(urldecode($chairperson->first_name)).' '.stripslashes(urldecode($chairperson->last_name)).'</td>
                <td class="formLabel">Cochairperson: </td>
                <td class="formInput">'.stripslashes(urldecode($cochairperson->first_name)).' '.stripslashes(urldecode($cochairperson->last_name)).'</td>
            </tr>
            <tr>
                <td class="formLabel">Address1: </td>
                <td class="formInput">'.stripslashes(urldecode($chairperson->address1)).'</td>
                <td class="formLabel">Address1: </td>
                <td class="formInput">'.stripslashes(urldecode($cochairperson->address1)).'</td>
            </tr>
            <tr>
                <td class="formLabel">Address2: </td>
                <td class="formInput">'.stripslashes(urldecode($chairperson->address2)).'</td>
                <td class="formLabel">Address2: </td>
                <td class="formInput">'.stripslashes(urldecode($cochairperson->address2)).'</td>
            </tr>
            <tr>
                <td class="formLabel">City/State/Zip: </td>
                <td class="formInput">'.stripslashes(urldecode($chairperson->city)).', '.$chairperson->state.' '.$chairperson->zip.'</td>
                <td class="formLabel">City/State/Zip: </td>
                <td class="formInput">'.stripslashes(urldecode($cochairperson->city)).', '.$cochairperson->state.' '.$cochairperson->zip.'</td>
            </tr>
            <tr>
                <td class="formLabel">Email: </td>
                <td class="formInput">'.stripslashes(urldecode($chairperson->email)).'</td>
                <td class="formLabel">Email: </td>
                <td class="formInput">'.stripslashes(urldecode($cochairperson->email)).'</td>
            </tr>
        ';
        
        return($out);
    }
    
    function sections_block($categories,$subcategories) {
        $out = '
            <tr>
                <td colspan="4"><h4>Recipe Sections</h4>
            </tr>
        ';
        $out .= '
        <tr>';
        $i=0;
        foreach($categories->categories AS $c) {
            if($i%4==0) {
                $out .= '
                </tr>
                <tr>';
            }
            $out .= '
                    <td style="border: 1px #999999 solid; padding: 4px">'.$c->order.'. '.stripslashes(urldecode($c->name));
            if($subcategories) {
                $out .= '
                        <ul>';
                foreach($subcategories->subcategories AS $s) {
                    if($s->parent == $c->number) {
                        $out .= '
                                <li>'.stripslashes(urldecode($s->name)).'</li>';
                    }
                }
                $out .= '
                        </ul>';
            }
            $out .= '
                    </td>';
            $i++;
        }
        return($out);
    }
    
    function options_block($info) {
        
        foreach($info AS $key=>$val) {
            $$key = $val;
        }
        
        $out = '
            <tr>
                <td colspan="4"><h4>Cookbook Options</h4>
            </tr>
        ';
        $out .= '
            <tr>
                <td class="formLabel">Free nutritional information? </td>
                <td class="formInput">'.$nutritionals.'</td>
                <td class="formLabel">Recipe contributors index? </td>
                <td class="formInput">'.$contributors.'</td>
            </tr>
            <tr>
                <td class="formLabel">Order index page by </td>
                <td class="formInput">'.$order_index_by.'</td>
                <td class="formLabel">Order form in the book? </td>
                <td class="formInput">'.$order_form.'</td>
            </tr>
            <tr>
                <td class="formLabel">Use subcategories? </td>
                <td class="formInput">'.$use_subcategories.'</td>
                <td class="formLabel">Recipes continued page to page?</td>
                <td class="formInput">'.$recipes_continued.'</td>
            </tr>
            <tr>
                <td class="formLabel">Allow notes?</td>
                <td class="formInput">'.$allow_notes.'</td>
                <td class="formLabel">Use recipe icons?</td>
                <td class="formInput">'.$use_icons.'</td>
            </tr>
            <tr>
                <td class="formLabel">Use page fillers?</td>
                <td class="formInput">'.$use_fillers.'</td>
                <td class="formLabel">Filler type</td>';
                if($use_fillers == 'no') {
                    $filler_type = 'none';   
                }
                $out .= '<td class="formInput">'.$filler_type.': '.$filler_set.'</td>
            </tr>
            <tr>
                <td class="formLabel">Recipe format</td>
                <td class="formInput">'.$recipe_format.'</td>
                <td class="formLabel">Design type </td>
                <td class="formInput">'.$design_type.'</td>
            </tr>
            <tr>
                <td class="formLabel">Order recipes by </td>
                <td class="formInput">'.$order_recipes_by.'</td>
                <td class="formLabel" colspan="2"></td>
            </tr>';
            if($order_form == 'yes') {
                $out .= 
            '<tr>
                <td class="formLabel">Order form details: </td>
                <td class="formInput" colspan="3">';
                $out .= 'Name: '.$order_form_name.'<br /> 
                Address 1: '.$order_form_address1.'<br />
                Address 2: '.$order_form_address2.'<br />
                City / State / Zip: '.$order_form_city.', '.$order_form_state.' '.$order_form_zip.'<br /> 
                Retail Price: '.$order_form_retail.'<br />
                Shipping Fee: '.$order_form_shipping.'<br />
                </td>
            </tr>
        ';
            }
        return($out);
    }
    
}

$status_list = 'order_status';
$mod_date = date('Y-m-d H:i:s');

$script = "document.observe('dom:loaded',function(){
    fancyNav();
});";

switch($action) {
	case 'submit':
		$first_name = stripslashes(urldecode($_SESSION['user']->first_name));
		$last_name = stripslashes(urldecode($_SESSION['user']->last_name));
		$address1 = '';
		if(isset($_SESSION['user']->address1)) {
			$address1 = stripslashes(urldecode($_SESSION['user']->address1));
		}
		$address2 = '';
		if(isset($_SESSION['user']->address2)) {
			$address1 = stripslashes(urldecode($_SESSION['user']->address2));
		}
		$city = '';
		if(isset($_SESSION['user']->city)) {
			$city = stripslashes(urldecode($_SESSION['user']->city));
		}
		$email = '';
		if(isset($_SESSION['user']->email)) {
			$email = stripslashes(urldecode($_SESSION['user']->email));
		}
		
		$ne = new Email();
		
		$message = "<p>The following project has been submitted for Editorial Review, pending order submission:</p>";
		$message .= "<p>Order Id:".$_SESSION['order_id']."<br />";
		$message .= "<p>Order Title:".$_SESSION['order_title']."<br />";
		$message .= "<p>Order Number:".$_SESSION['order_number']."<br />";	
		$message .= "Chairperson: ".$_SESSION['user']->id." : ".$first_name.' '.$last_name."<br />";
		$message .= "Address: ".$address1."<br />";
		$message .= $address2."<br />";
		$message .= $city.', '.$_SESSION['user']->state." ".$_SESSION['user']->zip."<br />";
		$message .= "Email: ".$email."<br />";
		
		// vars = array(recipient_email(s),reply_to,sender_email,sender_name,subject,message)
		$vars = array(
			'recipient_email'=>'Valerie VanHoecke <valerie@dev.cbp.ctcsdev.com>, Stephanie Jones <sjones@dev.cbp.ctcsdev.com>',
			'reply_to'=>'no-reply@dev.cbp.ctcsdev.com',
			'sender_email'=>'no-reply@dev.cbp.ctcsdev.com',
			'sender_name'=>'U-Type-It Customer Support',
			'subject'=>'U-Type-It Order Submission '.$_SESSION['order_number'],
			'message'=>$message
			);
		$res = $ne->_mail($vars);
		
		$message = "<p>Congratulations! Your U-Type-It Online&trade; recipe file has been received by Cookbook Publishers, Inc. Once we receive your completed order form with signatures and the other materials for your book, we will begin production of your order.</p>";
		$message .= "<p>Thank you for using our U-Type-It Online&trade; recipe software. We are certain your cookbooks will prove to be a success.</p>";
		$message .= "<p>Order Number:".$_SESSION['order_number']."<br />";
		$message .= "<p>Order Title:".$_SESSION['order_title']."<br />";
		$message .= "Chairperson: ".$_SESSION['user']->id." : ".$first_name.' '.$last_name."<br />";
		$message .= "Address: ".$address1."<br />";
		$message .= $address2."<br />";
		$message .= $city.', '.$_SESSION['user']->state." ".$_SESSION['user']->zip."<br />";
		$message .= "Email: ".$email."<br /></p>";
		$message .= "<p>Thank you and best wishes,</p>";
		$message .= "<p>Cookbook Publishers, Inc.</p>";
		$message .= "<p>1-800-227-7282</p>";
		$vars = array(
			'recipient_email'=>$first_name.' '.$last_name.' <'.$email.'>',
			'reply_to'=>'no-reply@dev.cbp.ctcsdev.com',
			'sender_email'=>'no-reply@dev.cbp.ctcsdev.com',
			'sender_name'=>'UTypeIt Customer Support',
			'subject'=>'UTypeIt Order Submission '.$_SESSION['order_number'],
			'message'=>$message
			);
		$res = $ne->_mail($vars);
		// Remove the current order from the session...
		$no = new Orders();
		$no->sendAndGetOne('UPDATE Orders SET status="2" WHERE id="'.$_SESSION['order_id'].'"');
			unset($_SESSION['order_id']);
			unset($_SESSION['order_number']);
			unset($_SESSION['general_info']);
			unset($_SESSION['utypeit_info']);
		$out = '
		Thank you! Please check your email for verification that Cookbook Publishers has received your recipes. <a href="order_list.php">Click Here</a> to return to the Order List page.';
		break;
	case 'confirm':
		$newOrder = new Orders();
		$this_order = $newOrder->getComposedOrder($_SESSION['order_id']);
   		$newParts = new makeOrderParts($this_order);
        
        foreach($this_order->order AS $key=>$val) {
            $$key = $val;
        }

		$out .= '<form id="order_form" name="order_form" action="'.$_SERVER['PHP_SELF'].'" method="POST">
			<input type="hidden" name="action" value="submit">
			<input type="hidden" name="id" value="'.$id.'">
			<input type="hidden" name="order_number" value="'.$order_number.'">';
            $out .= $newParts->_make();
            $out .= "</form>";
		break;
	default:
		$demo = false;
		if($_SESSION['order_id'] == 1) {
			$demo = true;
		}
		$no = new Orders();
		$query = "SELECT COUNT(id) FROM Order_Content WHERE order_id='".$_SESSION['order_id']."' AND status>2";
		if(!$demo) {
			$result = $no->sendAndGetOne($query);
			$recipe_count = $result->{'COUNT(id)'};
			if(!$recipe_count) {
				$submit_message = "<p style=\"font-weight: bold; margin-bottom: 10px\">Attention: Only recipes marked as \"APPROVED\" will be printed in your cookbook.</p><p>Before you submit your order for processing please make sure that you review your recipes and set their status as Approved. To mark a recipe as Approved, go to the individual recipe and select \"Approved\" from the \"Recipe Status\" drop-down menu at the top, right side of the page.</p>";
			}
		}
		$filename = DATA.'html/submit_message.html';
		$message = fopen($filename,'r');
		$submit_message = fread($message, filesize($filename));
		fclose($message);
        if(!$demo) {
        	$script .= "
            function makeAgreement() {
                if(!$('agree').checked) {
                    alert('You must agree to the terms before continuing.');
                } else {
                    $('terms_confirm').submit();
                }
            }";
			$submit_message .= "<form name='terms_confirm' id='terms_confirm' method='POST' action='".$_SERVER['PHP_SELF']."'>
			<input type='hidden' name='action' value='confirm'>
			<table width='100%' border='0' cellpadding='0' cellspacing='0' style='margin: 10px 0 0 0'>
				<tr>
					<td colspan='2'><strong>I acknowledge the above and am ready to submit the cookbook for printing. I understand I cannot make changes after I do this.</strong></td>
				</tr>
				<tr>
                    <td colspan='2'>&nbsp;</td>
                </tr>
				<tr>
					<td class='formLabel'><input type='checkbox' name='agree' id='agree' value='yes'> I agree</td>
					<td class='formRight'><button name='submit' id='submit' onclick='makeAgreement(); return false;'>Next...</button></td>
				</tr>
			</table>
			</form>
			";
		}
		
		$out = $submit_message;
		
		break;
}

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
