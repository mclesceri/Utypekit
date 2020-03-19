<?php

session_start();

if(!$_SESSION['login'] == true) {
    header('Location: index.php');
}

$order_id = $_SESSION['order_id'];

require_once('../src/globals.php');

require_once(SERVICES.'BaseService.php');
$nb = new BaseService();

$current_tab = 'add-user';
$script = '';

// get all possible users...
$query = "SELECT id,first_name,last_name FROM People WHERE level='1' ORDER BY last_name";
$all_users = $nb->sendAndGetMany($query);

// get all possible contractors...
$query = "SELECT id,first_name,last_name FROM People WHERE level='6' ORDER BY last_name";
$all_contractors = $nb->sendAndGetMany($query);

// get users for the current order...
$query = "SELECT People.id,People.first_name,People.last_name,Order_People.level FROM People,Order_People WHERE Order_People.order_id='".$order_id."' AND Order_People.person_id=People.id ORDER BY Order_People.level";
$order_users = $nb->sendAndGetMany($query);

// get contractor(s) for the current order...
$query = "SELECT People.id,People.first_name,People.last_name FROM People,Orders_Contractors WHERE Orders_Contractors.order_id='".$order_id."' AND Orders_Contractors.contractor_id=People.id";
$order_contractors = $nb->sendAndGetMany($query);

if(isset($_POST['action'])) {
    $action = $_POST['action'];
    switch($action) {
        case 'save_users':
            $current_tab = 'add-user';
            // find out who's been added or deleted from the list and change the database;
            $keep = array();
            $delete = array();
            // compare the old user list against the new user values...
            $users = $_POST['order_users'];       
            foreach($order_users AS $old) {
                $uid = $old->id;
                $save = false;
                if($users) {
	                foreach($users AS $u) {
	                    if($uid == $u) {
	                        $save = true;
	                    }
	                }
	            }
                if($save == false) {
                    $delete[]  = $uid;
                }
            }
            // compare the new user values against the old user list...
            foreach($users AS $u) {
                $save = true;
                foreach($order_users AS $old) {
                    $old_id = $old->id;
                    if($u == $old_id) {
                        $save = false;
                    }
                }
                if($save == true) {
                    $keep[] = $u;
                }
            }
            if($keep) {
                foreach($keep AS $k) {
                    $query = "INSERT INTO Order_People (
                        date_modified,
                        order_id,
                        person_id,
                        level,
                        added_by_type,
                        added_by_id
                        ) VALUES (
                        '".date('Y-m-d H:i:s')."',
                        '".$order_id."',
                        '".$k."',
                        '2',
                        '2',
                        '".$_SESSION['user']->id."'
                        )";
                    $res = $nb->insertAndGetOne($query);
                }
            }
            if($delete) {
                foreach($delete AS $d) {
                    $query = "DELETE FROM Order_People WHERE person_id='".$d."' AND order_id='".$order_id."'";
                    $nb->sendAndDelete($query);
                }
            }
            $query = "SELECT People.id,People.first_name,People.last_name,Order_People.level FROM People,Order_People WHERE Order_People.order_id='".$order_id."' AND Order_People.person_id=People.id ORDER BY Order_People.level";
			$order_users = $nb->sendAndGetMany($query);
			$script = '
			url = opener.window.location;
			opener.window.location = url;';
            break;
		case 'save_contractor':
            $current_tab = 'add-contractor';
            // find out who's been added or deleted from the list and change the database;
            $keep = array();
            $delete = array();
            // compare the old user list against the new user values...
            $contractors = $_POST['order_contractors'];
            foreach($order_contractors AS $old) {
                $uid = $old->id;
                $save = false;
                if($contractors) {
	                foreach($contractors AS $u) {
	                    if($uid == $u) {
	                        $save = true;
	                    }
	                }
	            }
                if($save == false) {
                    $delete[]  = $uid;
                }
            }
            // compare the new user values against the old user list...
            if($contractors) {
	            foreach($contractors AS $u) {
	                $save = true;
	                foreach($order_contractors AS $old) {
	                    $old_id = $old->id;
	                    if($u == $old_id) {
	                        $save = false;
	                    }
	                }
	                if($save == true) {
	                    $keep[] = $u;
	                }
	            }
	        }
            if($keep) {
                foreach($keep AS $k) {
                    $query = "INSERT INTO Orders_Contractors (
                        order_number,
                        date_added,
                        date_modified,
                        added_by_id,
                        modified_by_id,
                        order_id,
                        contractor_id,
                        status
                        ) VALUES (
                        '".$_SESSION['order_number']."',
                        '".date('Y-m-d H:i:s')."',
                        '".date('Y-m-d H:i:s')."',
                        '".$_SESSION['user']->id."',
                        '".$_SESSION['user']->id."',
                        '".$order_id."',
                        '".$k."',
                        '1'
                        )";
                    $res = $nb->insertAndGetOne($query);
                }
            }
            if($delete) {
                foreach($delete AS $d) {
                    $query = "DELETE FROM Orders_Contractors WHERE contractor_id='".$d."' AND order_id='".$order_id."'";
                    $nb->sendAndDelete($query);
                }
            }
            // get contractor(s) for the current order...
			$query = "SELECT People.id,People.first_name,People.last_name FROM People,Orders_Contractors WHERE Orders_Contractors.order_id='".$order_id."' AND Orders_Contractors.contractor_id=People.id";
			$order_contractors = $nb->sendAndGetMany($query);
			$script = '
			url = opener.window.location;
			opener.window.location = url;';
            break;
        case 'save_levels':
            $current_tab = 'order-level';
            unset($_POST['action']);
            // put the users with their new levels
            $users = array();
            foreach($_POST AS $key=>$val) {
                $user_arr = explode('-',$key);
                $type_arr = explode('_',$user_arr[0]);
                $users[$user_arr[1]][$type_arr[1]] = $val;
            }
            foreach($users AS $u) {
                $uid = $u['user'];
                $level = $u['level'];

                $query = "UPDATE Order_People SET level='".$level."' WHERE person_id='".$uid."' AND order_id='".$order_id."'";
                $nb->sendAndGetOne($query);
            }
            $script = '
			url = opener.window.location;
			opener.window.location = url;';
            break;
    }
     // get users for the current order...
    $query = "SELECT People.id,People.first_name,People.last_name,Order_People.level FROM People,Order_People WHERE Order_People.order_id='".$order_id."' AND Order_People.person_id=People.id ORDER BY Order_People.level";
    $order_users = $nb->sendAndGetMany($query);
}

?>
<!DOCTYPE html>
<head>
    <title>Add Users to Order</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <style type="text/css">
        body
        {
            font-family: Myriad, Myriad Pro, Arial, Helvetica, sans-serif;
            font-size: 12px;
        }
        table
        {
            width: 100%;
            border-collapse: collapse;
            border: none;
        }
        th,td
        {
            padding: 4px;
            vertical-align: top;
        }
        th
        {
            background: #666666;
            font-weight: normal;
            text-align: center;
            color: #FFFFFF;
        }
        th a
        {
            color: #EFEFEF;
        }
        th a:hover
        {
            color: #FFFFFF;
        }
        select[multiple="multiple"] {
            width: 150px;
            height: 200px;
        }
        #content
        {
            display: block;
            position: relative;
            width: 570px;
            height: 370px;
            margin: 0 auto;    
        }
        #tabs
        {
            position: relative;
            top: 0;
            left: 0;
            width: 530px;
            height: 30px;
            background: #CCCCCC;
        }
        #tabs li
        {
            display: inline-block;
            padding: 4px;
            margin: 4px 5px 0 5px;
            width: 150px;
            height: 18px;
            line-height: 18px;
            border-radius: 4px 4px 0 0;
            background: #EFEFEF;
            cursor: pointer;
        }
        #add-remove,#order-level
        {
            display: block;
            position: absolute;
            top: 35px;
            left: 0;
            width: 570px;
            height: 335px;
            background: #FFFFFF;
        }
        #add-remove
        {
            z-index: 10;
        }
        .tab:hover
        {
            background: #FFFFFF;
        }
        #add,#remove
        {
            font-size: 14px;
        }
        .label
        {
            text-align: right;
            color:  #666666;
        }
        .input
        {
            text-align: left;
        }
        .center
        {
            text-align: center;
        }
        .submit
        {
            text-align: right;
        }
    </style>
    <script type="text/javascript" src="<?=A_JS?>prototype.js"></script>
    <script type="text/javascript" src="<?=A_JS?>scriptaculous.js"></script>
    <script type="text/javascript">
        document.observe("dom:loaded", function()
        {
            var subs = $$('ul.sublist');
			for(var i=0; i<subs.length;i++) {
				if(i == currentSet) {
					subs.show();
				} else{
					subs.hide();
				}
			};
            
            var order_users = $('order_users').select('option');
            for(var i=0;i<order_users.length;i++) {
                var ouval = order_users[i].value;
                var auopt = $('all_users').select('option[value="' + ouval + '"]')[0];
                rid(auopt);
            }
            
            var order_contractors = $('order_contractors').select('option');
            for(var i=0;i<order_contractors.length;i++) {
                var ouval = order_contractors[i].value;
                var auopt = $('all_contractors').select('option[value="' + ouval + '"]')[0];
                rid(auopt);
            }
            
            current_tab = '<?=$current_tab?>';
            setTab(current_tab);
            
        });
        
        function setTab(current) {
            $$('div.tab').each(function(ea){ ea.hide(); });
            $(current).show();
        }
        
        function addUser() {
            window.opener.location = 'people_edit.php?action=users_add';
            window.close();
        }
        
        function addContractor() {
            window.opener.location = 'people_edit.php?action=contractor_add';
            window.close();
        }
        
        function rid(item) {
            item.remove();
        }

        function move(sourceSelect, targetSelect)
        {
            var options = sourceSelect.select("option");
    
            options.each(function(item)
            {
               if(item.selected)
               {
                  item.selected = false;
                  targetSelect.appendChild(item.remove());
               }
            });
        }
        <?=$script?>
    </script>
</head>
<body>
    <div id="content">
        <ul id="tabs">
            <li onclick="setTab('add-user')">Add/Remove Users</li>
            <li onclick="setTab('add-contractor')">Add/Remove Contractor</li>
            <li onclick="setTab('order-level')">Order User Levels</li>
        </ul>
	        <div id="add-user" class="tab">
	            <form name="save_users" id="save_users" method="POST" action="<?=$_SERVER['PHP_SELF']?>">
	            <input type="hidden" name="action" value="save_users" />
	            <table>
	                <tr>
	                    <th colspan="5" id="users_feedback"><p>Now editing users associated with order #<?=$_SESSION['order_number']?></p><p style="font-weight:  bold;">NOTE: Adding user(s) to an order requires the user(s) to first be<br />entered into the system using the "<a href="#" onclick="addUser(); return false;">Add User</a>" feature.</p></th>
	                </tr>
	                <tr>
	                    <td class="label">All Users:</td>
	                    <td class="input">
	                        <select name="all_users[ ]" id="all_users" multiple="multiple">
	                        <?php
	                            for($i=0;$i<count($all_users);$i++) {
	                                if($all_users[$i]->last_name) {
	                                echo '
	                           <option value="'.$all_users[$i]->id.'">'.$all_users[$i]->first_name.' '.$all_users[$i]->last_name.'</option> ';
	                                }
	                            }
	                        ?>
	                        </select>
	                    </td>
	                    <td class="center">
	                        <button id="u-add" type="button" onclick="move($('all_users'), $('order_users')); return false;">&raquo;</button>
	                        <br />
	                        <br />
	                        <br />
	                        <button id="u-remove" type="button" onclick="move($('order_users'), $('all_users')); return false;">&laquo;</button>
	                    </td>
	                    <td class="label">Order Users:</td>
	                    <td>
	                        <select name="order_users[ ]" id="order_users" multiple="multiple">
	                        <?php
	                            for($i=0;$i<count($order_users);$i++) {
	                                echo '
	                           <option value="'.$order_users[$i]->id.'">'.stripslashes(urldecode($order_users[$i]->first_name)).' '.stripslashes(urldecode($order_users[$i]->last_name)).'</option> ';
	                            }
	                        ?>
	                        </select>
	                    </td>
	                </tr>
	                <tr>
	                    <td colspan="5" class="submit"><button type="submit" onclick="$('order_users').select('option').each(function(ea){ea.selected=true})">Save Users</button></td>
	                </tr>
	            </table>
	            </form>
	        </div>
	        <div id="add-contractor" class="tab">
	            <form name="save_contractor" id="save_contractor" method="POST" action="<?=$_SERVER['PHP_SELF']?>">
	            <input type="hidden" name="action" value="save_contractor" />
	            <table>
	                <tr>
	                    <th colspan="5" id="users_feedback"><p>Now editing contractor(s) associated with order #<?=$_SESSION['order_number']?></p><p style="font-weight:  bold;">NOTE: Adding contractor(s) to an order requires the contractor(s) to first be<br />entered into the system using the "<a href="#" onclick="addContractor(); return false;">Add Contractor</a>" feature.</p></th>
	                </tr>
	                <tr>
	                    <td class="label">All Contractors:</td>
	                    <td class="input">
	                        <select name="all_contractors[ ]" id="all_contractors" multiple="multiple">
	                        <?php
	                            for($i=0;$i<count($all_contractors);$i++) {
	                                if($all_contractors[$i]->last_name) {
	                                echo '
	                           <option value="'.$all_contractors[$i]->id.'">'.stripslashes(urldecode($all_contractors[$i]->first_name)).' '.stripslashes(urldecode($all_contractors[$i]->last_name)).'</option> ';
	                                }
	                            }
	                        ?>
	                        </select>
	                    </td>
	                    <td class="center">
	                        <button id="c-add" type="button" onclick="move($('all_contractors'), $('order_contractors')); return false;">&raquo;</button>
	                        <br />
	                        <br />
	                        <br />
	                        <button id="c-remove" type="button" onclick="move($('order_contractors'), $('all_contractors')); return false;">&laquo;</button>
	                    </td>
	                    <td class="label">Order Contractor:</td>
	                    <td>
	                        <select name="order_contractors[ ]" id="order_contractors" multiple="multiple">
	                        <?php
	                            for($i=0;$i<count($order_contractors);$i++) {
	                                echo '
	                           <option value="'.$order_contractors[$i]->id.'">'.stripslashes(urldecode($order_contractors[$i]->first_name)).' '.stripslashes(urldecode($order_contractors[$i]->last_name)).'</option> ';
	                            }
	                        ?>
	                        </select>
	                    </td>
	                </tr>
	                <tr>
	                    <td colspan="5" class="submit"><button type="submit" onclick="$('order_contractors').select('option').each(function(ea){ea.selected=true})">Save Contractors</button></td>
	                </tr>
	            </table>
	            </form>
	        </div>
	        <div id="order-level" class="tab">
	            <form name="save_levels" id="save_levels" method="POST" action="<?=$_SERVER['PHP_SELF']?>">
	            <input type="hidden" name="action" value="save_levels" />
	            <table>
	                <tr>
	                    <th colspan="3" id="levels_feedback"><p>Now editing users associated with order #<?=$_SESSION['order_number']?></p><p style="font-weight: bold">NOTE: New users will not be available unless/until they are saved in the Add/Remove Users tab.</p></th>
	                </tr>
	                    <?php
	                    $i = 1;
	                    foreach($order_users AS $o) {
	                    ?>
	               <tr>
	                   <input type="hidden" name="order_user-<?=$i?>" value="<?=$o->id?>" />
	                    <td class="label"><?=$o->id?></td>
	                    <td class="label"><?=stripslashes(urldecode($o->first_name))?> <?=stripslashes(urldecode($o->last_name))?></td>
	                    <td class="input">
	                        <select name="order_level-<?=$i?>">
	                        <?php
	                        $levels = array('0'=>' -- ','1'=>'Demo Account','2'=>'Contributor','3'=>'Committee Member','4'=>'Cochairperson','5'=>'Chairperson');
	                        foreach($levels AS $key=>$val) {
	                            $selected = '';
	                            if($o->level == $key) {
	                                $selected = ' selected="selected"';
	                            }
	                        ?>
	                        <option value="<?=$key?>"<?=$selected?>><?=$val?></option>
	                        <?php
	                        }
	                        ?>
	                        </select>
	                    </td>
	                </tr>
	                    <?php
	                        $i++;
	                    }
	                    ?>
	                <tr>
	                    <td colspan="3" class="submit"><button type="submit">Save User Levels</button></td>
	                </tr>
	            </table>
	            </form>
	        </div>
    </div>
</body>
