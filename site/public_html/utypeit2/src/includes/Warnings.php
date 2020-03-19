<?php

if ( !defined('SRC') ) require_once('../globals.php');

require_once(SERVICES.'BaseService.php');

class Warnings
{
    
    var $recipe_deadline = null;
    var $max_recipes = null;
    var $max_recipes_ea = null;
    
    function __construct($info) {
        if(isset($info)) {
            foreach($info AS $key=>$val) {
                $$key = $val;
            }
            if($recipe_deadline) {
                $this->recipe_deadline = $recipe_deadline;
            }
            if($max_recipes) {
                $this->max_recipes = $max_recipes;
            }
            if($max_recipes_ea) {
                $this->max_recipes_ea = $max_recipes_ea;
            }
        }
    }
    
    function _warnings($order_id,$user_id) {
        
        $warnings = new stdClass();
        $warnings->error = false;
        
        $warn = false;
        
        $nb = new BaseService();
        $query = 'SELECT Order_People.level AS order_level FROM Order_People WHERE person_id="'.$user_id.'"';
        $order_level = $nb->sendAndGetOne($query);
		$order_level = $order_level->order_level;
        
        // Recipe deadline
        // Warns only chair and cochair...
		if($order_level > 3) {
			$rd_warn_msg = '';
			if($this->recipe_deadline) {
				$warnings->recipe_deadline = new stdClass();
				$warnings->recipe_deadline->status = false;
	            
	            $today = strtotime(date('Y-m-d H:i:s'));
	            $deadline = strtotime($this->recipe_deadline." 01:00:00").'<br />';
	            
	            if($today >= $deadline) {
	                $warn = true;
	                $warnings->recipe_deadline->status = true;
	                $rd_warn_msg = 'The recipe entry deadline has passed.<br />';
	            }
	        }
	        // max recipes
	        $mx_warn_msg = '';
	        if($this->max_recipes) {
	        	if($this->max_recipes > 0) {
		            $warnings->max_recipes = new stdClass();
		            $warnings->max_recipes->status = false;
		            $query = "SELECT COUNT(id) FROM Order_Content WHERE order_id='".$order_id."'";
		            $recipe_count = $nb->sendAndGetOne($query);
		            $total = $recipe_count->{'COUNT(id)'};
		            if($total >= $this->max_recipes) {
		                $warn = true;
		                $warnings->max_recipes->status = true;
		                $mx_warn_msg = 'The maximum number of recipes for this order has been reached<br />';
		            }
				}
	        }
		}
        
        // max recipes per user
        // warns only this user...
		$mxe_warn_msg = '';
		if($this->max_recipes_ea) {
        	if($this->max_recipes_ea) {
	            $warnings->max_recipes_ea = new stdClass();
	            $warnings->max_recipes_ea->status = false;
	            $query = 'SELECT COUNT(id) FROM Order_Content WHERE order_id="'.$order_id.'" AND added_by_id="'.$user_id.'"';
	            $res = $nb->sendAndGetOne($query);
	            $total = $res->{'COUNT(id)'};
	           
	            if($total >= $this->max_recipes_ea) {
	                $warn = true;
	                $warnings->max_recipes->status = true;
	                $mxe_warn_msg = 'The maximum number of recipes for this user has been reached<br />';
	            }
			}
        }

		$warnings->display = '
        <div style="display: none">&nbsp;</div>';
        if($warn == true) {
            $warnings->error = true;
            // Gives chair or cochair the opportunity to change the settings...
			if($order_level > 3) {
				$chair_msg = '<br /><a href="'.UTI_URL.'message_center.php?action=user_messages" style="color: black;">Click Here</a> to change these settings.';
			}
			$warnings->display = '
		<div class="warning">'.$rd_warn_msg.$mx_warn_msg.$mxe_warn_msg.$chair_msg.'</div>';
        }
        return($warnings);
    }
    
}

?>