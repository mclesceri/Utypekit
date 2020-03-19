<?php
/*
 * 
 * process_report.php
 * by William Logan, OCT 2012
 * 
 * Processes GET and AJAX calls (via POST) to create a report
 * 
 * Receives data from either a GET string sent by the breadcrumb,
 * or a form POST from the AccountReportFilter utility and processes
 * that data to be sent to the AccountReport class which constructs
 * the report.
 * 
 */

 session_start();

if ( !defined('SRC') ) require_once('../globals.php');

require_once(SERVICES.'BaseService.php');
require_once(INCLUDES.'AccountReport.php');

$action = '';
if(isset($_REQUEST['action'])){ // receive the action via GET
	$action = $_REQUEST['action'];
}

// Get the session query object

switch($action) {
	case 'AccountReportCSV':
		// Request a CSV output from AccountReport with all information
		$set = '';
		if(isset($_POST['set'])) {
		    $set = $_POST['set'];
		}
        if($set == 'filter') {
    		if(isset($_SESSION['report_filter'])) {
                $filter = json_decode($_SESSION['report_filter']);
            } else {
                $filter = new stdClass();
                $filter->fields = 'all';
            }
        } else {
            $filter = new stdClass();
            $filter->fields = 'all';
        }
        $filter->start = '';
		$filter->limit = '';
		$filter->lpage = '';
		$filter->orderby = 'id';
		$nar = new AccountReport($filter); // instantiate AccountReport with this query
		$res = $nar->_report('csv',$filter,$set); // construct the report
		echo $res;
		break;
	case 'AccountReportFilter':
		// Create a filtered result based on user criteria
		//print_r( $_POST );
		
		// The post variables always override the existing variables.
        $filter = new stdClass();
        $json = json_decode($_SESSION['report_filter']);
        // The get variables always override the existing variables.
        // If there's no get, and no existing variables, the default is set.
        $filter->orderby = 'id';
        if($json->orderby) {
            $filter->orderby = $json->orderby;
        } elseif(isset($_POST['orderby'])) {
            $filter->orderby = $_POST['orderby'];
        }
        
        $filter->searchmod = null;
        if($json->searchby) {
            $filter->searchmod = $json->searchmod;
        } elseif(isset($_POST['searchmod'])) {
            $filter->searchmod = $_POST['searchmod'];
        }
        
        $filter->searchby = null;
        if($json->searchby) {
            $filter->searchby = $json->searchby;
        } elseif(isset($_POST['searchby'])) {
            $filter->searchby = $_POST['searchby'];
        }
        
        $filter->searchfor = null;
        if($json->searchfor) {
            $filter->searchfor = $json->searchfor;
        } elseif(isset($_POST['searchfor'])) {
            $filter->searchmod = $_POST['searchfor'];
        }
        
        // all possible fields...
        $all = array('Order'=>array('id','date_added','added_by_type','order_number','title','status'),
                                                'People'=>array('first_name','last_name','phone','email','login','password','address1','address2','city','state','zip','meta'),
                                                'Recipe'=>array('recipe_count','last_recipe'),
                                                'Organization'=>array('organization_id','organization_name','organization_type')
                                            );
        // find out if all the fields are checked by setting up a toggle...
        $allfields = true;
        // arrange the post values for all the checkboxes into a new object...
        $newfields = new stdClass();
        foreach($all AS $key=>$val) {
            $tmp = array();
            if(count($val) != count($_POST[$key])) {
                $allfields = false;
            }
            foreach($val AS $v) {
                if($_POST[$v]) {
                    $tmp[$v] = $_POST[$v];
                }
            }
            if(count($tmp) > 0) {
                $subfields = new stdClass();
                foreach($tmp AS $k=>$v) {
                    $subfields->{$k} = $v;
                }
                $newfields->{$key} = $subfields;
            }
        }
        if($allfields == true) {
            $filter->fields = 'all';
        } else {
            $filter->fields = $json->fields;
            if(count((array)$newfields) > 0) {
                $filter->fields = $newfields;
            }
        }
        
       if(isset($_POST['lpage'])) {
            $filter->lpage = $_POST['lpage'];
        } else {
            $filter->lpage = 1;
        }
        
        if(isset($_POST['total'])) {
            $filter->total = $_POST['total'];
        }
        
        if(isset($_POST['start'])) {
            $filter->start = $_POST['start'];
        } else {
            $filter->start = 0;
        }
        
        $_SESSION['report_filter'] = json_encode($filter);
		
		$nar = new AccountReport($filter); // instantiate AccountReport with this query
		$res = $nar->_report('html',$filter);// construct the report
		if(!isset($_POST['case'])) {
    		echo $res; // AJAX response
        }
		break;
}

?>