<?php

        $head = '<div class="contentHeaderBlock" id="contentHeaderBlock">
        <div class="contentHeaderLeft">';
        if($title) {
        $head .= $title;
        } else {
        	$head .= ucwords(str_replace('_',' ',$page)).' for Order #'.$_SESSION['order_number'].' ';
        }
        
        if($page == 'recipe_list') {
        $bc = new BreadCrumb($page);
        $ll = new ListLimit();
        $head .= $ll->_draw();
            $head .= $bc-> _paginate($pagenum,$total_people,$limit,'subheaderLink',$orderby);
        } 
        
        $head .= '</div>
        <div class="contentHeaderRight">
            <ul>
                <li';
                if(!$demo) {
                    $head .= ' onclick="printWin(\'order_people_list\',\'\')"';
                } else {
                    $head .= ' class="disabled"';
                }
                $head .= '>Print People List</li>
                <li';
                 if($_SESSION['user']->order_level > 3) {
                 	if($page != 'message_center') {
	                    $head .= ' onclick="window.location=\'message_center.php?action=user_messages\'"';
	                } else {
						$head .=  ' class="disabled"';
					}
                } else {
                    $head .= ' class="disabled"';
                }
                $head .= '>User Messages</li>
                <li class="blank">&nbsp;</li>
                <li';
                if(!$demo){
                    if($page != 'people_list') {
                    	if($page != 'message_center') {
                        	if($_SESSION['user']->order_level > $level) {
                        	    $head .=  ' onclick="$(\'people_edit\').submit();"';
							}
						} else {
							$head .=  ' class="disabled"';
						}
                    } else {
                        $head .=  ' class="disabled"';
                    }
                } else {
                    if($page != 'people_list' && !$demo) {
                        if($action != 'save') {
                            $head .=  ' onclick="$(\'people_edit\').submit();"';
                        } else {
                            $head .=  ' class="disabled"';
                        }
                    } else {
                        $head .=  ' class="disabled"';
                    }
                }
                $head .= '>Save Person</li>
                <li';
                if($page != 'people_list' && $action != 'user_add') {
                	if($page != 'message_center') {
	                    $head .= ' onclick="printWin(\'person\',\''.$person_id.'\')"';
	                } else {
	                	$head .=  ' class="disabled"';
	                }
                } else {
                    $head .= ' class="disabled"';
                }
                $head .= '>Print This Person</a></li>
                <li';
				if($action == 'user_add') {
					$head .= ' class="disabled"'; 
				} else {
					if($page == 'message_center') {
						$head .=  ' class="disabled"';
					} elseif($page == 'people_list') {
						$head .=  ' class="disabled"';
					} else {
						$head .= ' onclick ="window.location = \'message_center.php?action=message_compose&recipient='.$person_id.'\'"';
					}
                }
                 $head .= '>Contact Person</li>
				 <li class="blank">&nbsp;</li>
				<li';
				if($page == 'people_list') {
					if($_SESSION['user']->order_level >= 4) {
						//echo $_SESSION['user']->order_level;
						$head .= ' onclick ="window.location = \'people_edit.php?action=user_add\'"';
					} else {
						$head .=  ' class="disabled"';
					}
				} else {
					if($action != 'user_add') {
						if($page != 'message_center') {
							$head .= ' class="disabled"'; 
						} else {
							$head .= ' onclick ="window.location = \'people_edit.php?action=user_add\'"';
						}
					} else {
						$head .= ' onclick ="window.location = \'people_edit.php?action=user_add\'"';
					}
                }
				$head .= '">Add New Person</li>
            </ul>';
        $head .= '
            </div>
        </div>
        <div id="feedback">'.$feedback.'</div>';

$contentHeader = $head;
?>