<?php
$page_note = '';
if(isset($_SESSION['utypeit_info'])) {  
	$recipe_note = '';
	$welcome_note = '';
	if(isset($_SESSION['utypeit_info']->recipe_note)) {
		$recipe_note = stripslashes(urldecode($_SESSION['utypeit_info']->recipe_note));
	}
	if(isset($_SESSION['utypeit_info']->welcome_note)) {
		$welcome_note = stripslashes(urldecode($_SESSION['utypeit_info']->welcome_note));
	}
	
	if($page == 'recipe_edit') {
		if(!$recipe_note) {
			if($_SESSION['user']->order_level > 3) {
					$page_note = "The options for this order have not been set. Click the \"Members\" button, then choose the \"User Messages\" tab to enter this information.";
			}
		} else  {
			$page_note = $recipe_note;
		}
	} else {
		if($page == 'order_list') {
			$page_note = 'Choose an order on the right to begin.';
		} else {
			if(!$welcome_note) {
				if($_SESSION['user']->order_level > 3) {
					$page_note = "The options for this order have not been set. Click the \"Members\" button, then choose the \"User Messages\" tab to enter this information.";
				}
			} else {
				$page_note = $welcome_note;
			}
		}	
	}
} else {
	if($page == 'order_list') {
		$page_note = 'Choose an order on the right to begin.';
	}
}

?>
<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title><?=$title?></title>
<link href="<?=U_CSS?>reset.css" rel="stylesheet" type="text/css" />
<link href="<?=FONTS?>css/webfonts.css" rel="stylesheet" type="text/css" />
<link href="<?=U_CSS?>colors.css" rel="stylesheet" type="text/css" />
<link href="<?=U_CSS?>lightwindow.css" rel="stylesheet" type="text/css" />
<link href="<?=U_CSS?>style.css" rel="stylesheet" type="text/css" />
<link href="<?=U_CSS?>opentip.css" rel="stylesheet" type="text/css" />
<?php
switch($page) {
    case 'order_options':
?>
<link href="<?=U_CSS?>order_options.css" rel="stylesheet" type="text/css" />
<?php
        break;
    case 'order_edit':
?>
<link href="<?=U_CSS?>ordered_list.css" rel="stylesheet" type="text/css" />
<link href="<?=U_CSS?>order_edit.css" rel="stylesheet" type="text/css" />
<?php
        break;
    case 'order_list':
?>
<link href="<?=U_CSS?>list.css" rel="stylesheet" type="text/css" />
<?php
        break;
    case 'recipe_edit':
?>
<link href="<?=U_CSS?>sidebox.css" rel="stylesheet" type="text/css" />
<!--[if IE]>
    <link rel="stylesheet" type="text/css" href="<?=U_CSS?>sidebox_ie.css" />
<![endif]-->
<link href="<?=U_CSS?>recipe.css" rel="stylesheet" type="text/css" />
<?php
        break;
    case 'recipe_organize':
?>
<link href="<?=U_CSS?>recipe_organize.css" rel="stylesheet" type="text/css" />
<?
        break;
    case 'recipe_list':
?>
<link href="<?=U_CSS?>list.css" rel="stylesheet" type="text/css" />
<?php
        break;
    case 'people_list':
?>
<link href="<?=U_CSS?>list.css" rel="stylesheet" type="text/css" />
<?php
        break;
    case 'people_edit':
?>

<?
        break;
    case 'message_center':
?>
<link href="<?=U_CSS?>calendarview.css" rel="stylesheet" type="text/css" />
<?php
        break;
    case 'send_proof':
?>
<link href="<?=U_CSS?>send_proof.css" rel="stylesheet" type="text/css" />
<?php
    	break;
    case 'order_form':
?>
<link href="<?=U_CSS?>order_form.css" rel="stylesheet" type="text/css" />
<?php
        break;
}
?>

<script src="<?=U_JS?>prototype.js" type="text/javascript"></script>
<script src="<?=U_JS?>scriptaculous.js" type="text/javascript"></script>
<script src="<?=U_JS?>php.js?ver=<?=date('dmY')?>" type="text/javascript"></script>
<script src="<?=U_JS?>opentip.js" type="text/javascript"></script>
<script src="<?=U_JS?>opentip-prototype-excanvas.js" type="text/javascript"></script>
<?
switch($page) {
    case 'order_options':
?>
<script src="<?=U_JS?>order_options.js?ver=<?=date('dmY')?>" type="text/javascript"></script>
<?php
        break;
    case 'order_edit':
?>
<script src="<?=U_JS?>ordered_list.js?ver=<?=date('dmY')?>" type="text/javascript"></script>
<script src="<?=U_JS?>orders.js?ver=<?=date('dmY')?>" type="text/javascript"></script>
<?php
        break;
    case 'order_list':
?>
<script src="<?=U_JS?>list.js?ver=<?=date('dmY')?>" type="text/javascript"></script>
<?php
        break;
    case 'recipe_edit':
?>
<script src="<?=U_JS?>recipesections_list.js?ver=<?=date('dmY')?>" type="text/javascript"></script>
<script src="<?=U_JS?>side_boxes.js?ver=<?=date('dmY')?>" type="text/javascript"></script>
<script src="<?=U_JS?>contributors_list.js?ver=<?=date('dmY')?>" type="text/javascript"></script>
<script src="<?=U_JS?>recipe.js?ver=<?=date('dmY')?>" type="text/javascript"></script>
<?php
        break;
    case 'recipe_organize':
?>
<script src="<?=U_JS?>recipe_organize.js?ver=<?=date('dmY')?>" type="text/javascript"></script>
<?
        break;
    case 'recipe_list':
?>
<script src="<?=U_JS?>list.js?ver=<?=date('dmY')?>" type="text/javascript"></script>
<?php
        break;
    case 'message_center':
?>
<script src="<?=U_JS?>calendarview.js?ver=<?=date('dmY')?>" type="text/javascript"></script>
<?php
        break;
}
?>
<?php
if(!$demo) {
?>
<script src="<?=U_JS?>hotkeys.js?ver=<?=date('dmY')?>" type="text/javascript"></script>
<?php
}
?>
<script src="<?=U_JS?>functions.js?ver=<?=date('dmY')?>" type="text/javascript"></script>
<script src="<?=U_JS?>lightwindow.js" type='text/javascript'></script>
<script type="text/javascript">
    var images = '<?=IMAGES?>';
    var includes = '<?=UTI_URL?>src/includes/';
    var services = '<?=UTI_URL?>src/services/';
<?=$script?>
</script>
</head>

<body>
<div id="headerWrap">
<div id="header">
    <div id="logo"><a href="http://dev.cbp.ctcsdev.com"><img src="<?=IMAGES?>cookbook-logo-new2.png" /></a></div>
    <div class="headerLeft"><?=$header_left?></div>
    <div class="headerMiddle museo_slab_500 t_ds99-1">
    <?php
    if(!$demo) {
        echo $header_middle;
    } else {
        echo '<a href="setup_wizard.php?action=upgrade" class="upgrade">UPGRADE TO A LIVE ACCOUNT NOW!</a>';
    }
    ?>
    </div>
    <div class="headerRight"><?=$header_right?></div>
    <div id="uti_logo"><img src="<?=IMAGES?>utypeit_logo.png" style="margin: 30px 50px 0 0; float: right;" /></div>
</div>
</div>
<div id="content">
    <div id="left" class="b_pms2726">
		<?=$warning?>
		<div class="header museo_slab_500">Manage Cookbook</div>
		<div class="content b_pms659">
			<ul id="left_nav">
				<li onclick="window.location = 'order_list.php'" data-ot="The list of your orders." data-ot-delay="1">All Orders</li>
				<?php
				if(isset($_SESSION['order_id'])) {
				    echo '<p>Order #'.$_SESSION['order_number'].'</p>';
				?>
    			<li onclick="window.location = 'order_options.php?id=<?=$_SESSION['order_id']?>'" data-ot="Options for order #<?=$_SESSION['order_number']?>." data-ot-delay="1">Order #<?=$_SESSION['order_number']?></li>
    			<li onclick="window.location = 'recipe_list.php'" data-ot="List the recipes for order #<?=$_SESSION['order_number']?>." data-ot-delay="1">Recipes</li>
    			<?php
    			if($_SESSION['user']->order_level > 3) {
    			?>
	   		    <li onclick="window.location = 'people_list.php'" data-ot="List the people associated with order #<?=$_SESSION['order_number']?>." data-ot-delay="1">Members</li>
	   		    <?
	   		    } else {
	   		    ?>
	   		    <li class="inactive" data-ot="Chairperson, or Cochairperson Only." data-ot-delay="1">Members</li>
	   		    <?
	   		    }
	   		    ?>
		  		<li onclick="window.location = 'message_center.php'" data-ot="Edit user messages or send messages to users for  order #<?=$_SESSION['order_number']?>." data-ot-delay="1">Message Center</li>
		  		<?php
		  			if($demo) {
		  		?>
		  		<li onclick="window.location = 'send_proof.php'" data-ot="Send for a proof of order #<?=$_SESSION['order_number']?>." data-ot-delay="1">Proof This Cookbook</li>
		  		<?php
		  			} else if($_SESSION['user']->order_level > 3) {
                ?>
                <li onclick="window.location = 'send_proof.php'" data-ot="Send for a proof of order #<?=$_SESSION['order_number']?>." data-ot-delay="1">Proof This Cookbook</li>
                <?
                    } else {
                        ?>
                  <li class="inactive" data-ot="Chairperson, or Cochairperson Only." data-ot-delay="1">Proof This Cookbook</li>      
                        <?
                    }
                        if($_SESSION['user']->order_level > 4) {
                ?>
                <li onclick="window.location = 'order_form.php'" data-ot="Submit order #<?=$_SESSION['order_number']?> to print." data-ot-delay="1">Submit This Order</li>
                <?php
                        } else {
                ?>
                <li class="inactive" data-ot="Chairperson Only" data-ot-delay="1">Submit This Order</li>
                <?            
                        }
                } else {
                    echo '<p>No order selected</p>';
                ?>
                <li class='inactive' data-ot="You must select an order first." data-ot-delay="1">Recipes</li>
                <li class='inactive' data-ot="You must select an order first." data-ot-delay="1">Members</li>
                <li class='inactive' data-ot="You must select an order first." data-ot-delay="1">Message Center</li>
                <?php
                }
		  		?>
			</ul>
		</div>
		<div class="header museo_slab_500">My Account</div>
		<div class="content b_pms659">
			<ul>
			    <li onclick="window.location = 'people_edit.php?action=user_edit&id=<?=$_SESSION['user']->id?>'" data-ot="Edit your account information." data-ot-delay="1">Edit My Info</li>
            </ul>
        </div>
        <div class="header museo_slab_500">Support</div>
        <div class="content b_pms659">
            <ul>
				<li onclick="window.open('http://dev.cbp.ctcsdev.com/order-a-free-kit/','_blank')" data-ot="Request a free cookbook ordering kit." data-ot-delay="1">Request a Kit</li>
				<li onclick="window.location = 'message_center.php?action=contact_cpi'" data-ot="Contact customer service at Cookbook Publishers Inc.." data-ot-delay="1">Contact Support</li>
				<li onclick="window.location = 'faq.php'" data-ot="A list of common questions and answers." data-ot-delay="1">Help/FAQ</li>
	       </ul>
		</div>
		<button onclick="window.location='index.php?action=logout'">Log Out</button>
		<div class="note"><?=$page_note?></div>
	</div>
	<div id="right">
	    <?php
	    if($page != 'order_list') {
	        if($page != 'order_options') {
	            if(isset($_SESSION['order_id'])) {
	    ?>
        <div id="right_nav">
            <div class="smallTextIcon" onclick="window.location = 'order_list.php'" data-ot="Select Another Order" data-ot-delay="1">All</div>
            <?
            if($_SESSION['user']->order_level > 3) {
            ?>
            <div class="smallIcon" onclick="window.location = 'order_edit.php?id=<?=$_SESSION['order_id']?>&action=order_edit'" data-ot="Edit Order Settings" data-ot-delay="1"><img src="<?=IMAGES?>si_order_on.png"></div>
            <?
            } else {
            ?>
            <div class="smallIcon"><img src="<?=IMAGES?>si_order_off.png" data-ot="Chairpersons Only" data-ot-delay="1"></div>
            <?
            }
            ?>
            <div class="smallIcon" onclick="window.location = 'recipe_list.php'"><img src="<?=IMAGES?>si_recipes_on.png" data-ot="List/Edit Recipes" data-ot-delay="1"></div>
            <?
            if($_SESSION['user']->order_level > 3) {
            ?>
            <div class="smallIcon" onclick="window.location = 'people_list.php'"><img src="<?=IMAGES?>si_member_on.png" data-ot="Member Options" data-ot-delay="1"></div>
            <?
            } else {
            ?>
            <div class="smallIcon"><img src="<?=IMAGES?>si_member_off.png" data-ot="Chairpersons Only" data-ot-delay="1"></div>
            <?
            }
            ?>
            <div class="smallIcon" onclick="window.location = 'message_center.php'"><img src="<?=IMAGES?>si_message_on.png" data-ot="Message Center" data-ot-delay="1"></div>
            <?
            if($_SESSION['user']->order_level > 3) {
            ?>
            <div class="smallIcon" onclick="window.location = 'send_proof.php'"><img src="<?=IMAGES?>si_proof_on.png" data-ot="Proof This Order" data-ot-delay="1"></div>
            <?
            } else {
            ?>
            <div class="smallIcon"><img src="<?=IMAGES?>si_proof_off.png" data-ot="Chairpersons Only" data-ot-delay="1"></div>
            <?
            }
            if($_SESSION['user']->order_level > 4) {
            ?>
            <div class="smallIcon" onclick="window.location = 'order_form.php'"><img src="<?=IMAGES?>si_submit_on.png" data-ot="Submit This Order" data-ot-delay="1"></div>
            <?
            } else {
            ?>
            <div class="smallIcon"><img src="<?=IMAGES?>si_submit_off.png" data-ot="Chairpersons Only" data-ot-delay="1"></div>
        	<?
            }
            ?>
        </div>	    
	    <?
	           }
	       }
	    }
        ?>
	    <?=$contentHeader?>
	    <?php if(isset($search)) { ?><div class="search"><?=$search?></div><? } ?>
		<div id="dynamic"><?=$content?></div>
		<?=$contentFooter?>
	</div>
</div>
<div id="base"><p><a href="http://dev.cbp.ctcsdev.com">Cookbook Publishers</a> | <a href="faq.php">Help/FAQ</a> | <a href="mailto: info@dev.cbp.ctcsdev.com">Email Support</a></p><p>U-Type-It&trade; Online v 2.0 Brought to you by Cookbook Publishers, Inc. 1-800-227-7282</p></div>
</body>
<script type="text/javascript">
 
  var _gaq = _gaq || [];
  _gaq.push(['_setAccount', 'UA-17597813-1']);
  _gaq.push(['_setDomainName', 'dev.cbp.ctcsdev.com']);
  _gaq.push(['_trackPageview']);
 
  (function() {
    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
  })();
 
</script>
</html>
