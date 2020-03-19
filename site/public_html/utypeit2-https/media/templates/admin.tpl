<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title><?=$title?></title>
<link href="<?=A_CSS?>reset.css" rel="stylesheet" type="text/css" />
<link href="<?=A_CSS?>style.css" rel="stylesheet" type="text/css" />
<link href="<?=ROOT_URL?>webfonts/css/webfonts.css" rel="stylesheet" type="text/css" />
<link href="<?=A_CSS?>darkbox.css" rel="stylesheet" type="text/css" />
<?php
switch($page) {
	case 'order_edit':
?>
<link href="<?=A_CSS?>lightwindow.css" rel="stylesheet" type="text/css" />
<link href="<?=A_CSS?>ordered_list.css" rel="stylesheet" type="text/css" />
<link href="<?=A_CSS?>order_edit.css" rel="stylesheet" type="text/css" />
<?php
		break;
	case 'order_list':
?>
<link href="<?=A_CSS?>list.css" rel="stylesheet" type="text/css" />
<?php
		break;
	case 'recipe_edit':
?>
<link href="<?=A_CSS?>sidebox.css" rel="stylesheet" type="text/css" />
<!--[if IE]>
    <link rel="stylesheet" type="text/css" href="<?=A_CSS?>sidebox_ie.css" />
<![endif]-->
<link href="<?=A_CSS?>recipe.css" rel="stylesheet" type="text/css" />
<?php
        break;
    case 'recipe_organize':
?>
<link href="<?=A_CSS?>recipe_organize.css" rel="stylesheet" type="text/css" />
<?
        break;
    case 'recipe_list':
?>
<link href="<?=A_CSS?>list.css" rel="stylesheet" type="text/css" />
<?php
        break;
    case 'people_list':
?>
<link href="<?=A_CSS?>list.css" rel="stylesheet" type="text/css" />
<?php
        break;
    case 'people_edit':
?>

<?
        break;
    case 'message_center':
?>
<link href="<?=A_CSS?>message_center.css" rel="stylesheet" type="text/css" />
<link href="<?=A_CSS?>calendarview.css" rel="stylesheet" type="text/css" />
<?php
        break;
    case 'order_form':
?>
<link href="<?=A_CSS?>order_form.css" rel="stylesheet" type="text/css" />
<?php
        break;
	case 'reports':
?>
<link href="<?=A_CSS?>reports.css" rel="stylesheet" type="text/css" />
<link href="<?=A_CSS?>account_report_filter.css" rel="stylesheet" type="text/css" />
<link href="<?=A_CSS?>calendarview.css" rel="stylesheet" type="text/css" />
<?php
		break;
}
?>
<script src="<?=A_JS?>prototype.js" type="text/javascript"></script>
<script src="<?=A_JS?>scriptaculous.js" type="text/javascript"></script>
<?
switch($page) {
	case 'order_edit':
?>
<script src='<?=A_JS?>lightwindow.js' type='text/javascript'></script>
<script src='<?=A_JS?>ordered_list.js?ver=<?=date('dmY')?>' type='text/javascript'></script>
<script src='<?=A_JS?>orders.js?ver=<?=date('dmY')?>' type='text/javascript'></script>
<?php
		break;
	case 'order_list':
?>
<script src="<?=A_JS?>list.js?ver=<?=date('dmY')?>" type="text/javascript"></script>
<?php
		break;
	case 'recipe_edit':
?>
<script src="<?=A_JS?>recipesections_list.js?ver=<?=date('dmY')?>" type="text/javascript"></script>
<script src="<?=A_JS?>side_boxes.js?ver=<?=date('dmY')?>" type="text/javascript"></script>
<script src="<?=A_JS?>contributors_list.js?ver=<?=date('dmY')?>" type="text/javascript"></script>
<script src="<?=A_JS?>recipe.js?ver=<?=date('dmY')?>" type="text/javascript"></script>
<?php
		break;
	case 'recipe_organize':
?>
<script src="<?=A_JS?>recipe_organize.js?ver=<?=date('dmY')?>" type="text/javascript"></script>
<?
		break;
	case 'recipe_list':
?>
<script src="<?=A_JS?>list.js?ver=<?=date('dmY')?>" type="text/javascript"></script>
<?php
		break;
	case 'message_center':
?>
<script src="<?=A_JS?>tinymce/tinymce.min.js" type="text/javascript"></script>
<script src="<?=A_JS?>message_center.js?ver=<?=date('dmY')?>" type="text/javascript"></script>
<?php
		break;
	case 'reports':
?>
<script src="<?=A_JS?>reports.js?ver=<?=date('dmY')?>" type="text/javascript"></script>
<script src="<?=A_JS?>calendarview.js" type="text/javascript"></script>
<?
		break;
}
?>
<script src="<?=A_JS?>hotkeys.js" type="text/javascript"></script>
<script src="<?=A_JS?>functions.js?ver=<?=date('dmY')?>" type="text/javascript"></script>
<script type="text/javascript">
	var images = '<?=IMAGES?>';
	var includes = '<?=UTI_URL?>src/includes/';
	var services = '<?=UTI_URL?>src/services/';
	var currentSet = <?=$tab?>;
<?=$script?>
</script>
</head>

<body>
<div class="header">CPI OMS Administration Utility</div>
<div id="navigation">
		<ul>
			<li onclick="setContent('order_list',{mode:'redirect'<? if($_SESSION['user']->level == 6){ echo ", action: 'contractor', id: '".$_SESSION['user']->id."'"; }; ?>});">Orders</li>
			<ul class="sublist" style="left: -140px">
				<li id="orders" onclick="expandSublist(this); return false;" class="sublistButton" style="right: -125px">></li>
			<?php
			if($_SESSION['user']->level > 6) {
			?>
				<li onclick="setContent('order_edit',{mode: 'redirect',action: 'order_add'});">Add an Order<img src="<?=IMAGES?>icon_add_order.png"></li>
				<li onclick="setContent('print_win',{mode: 'popup',action: 'order_list'});">Print Orders List<img src="<?=IMAGES?>icon_print_list.png"></li>
				<li class="blank">&nbsp;</li>
			<?php
			}
			?>
				<li onclick="if(!this.hasClassName('inactive')) { setContent('order_edit',{mode: 'redirect',action: 'order_edit',id: '<?=$_SESSION['order_id']?>'}); } else { return false }" class="inactive">Edit Settings<img src="<?=IMAGES?>icon_order_settings.png"></li>
				<li onclick="if(!this.hasClassName('inactive')) { setContent('recipe_list',{mode: 'redirect'}); } else { return false }" class="inactive">List All Recipes<img src="<?=IMAGES?>icon_show_list.png"></li>
				<li onclick="if(!this.hasClassName('inactive')) { setContent('recipe_organize',{mode: 'redirect'}); } else { return false }" class="inactive">Organize Recipes<img src="<?=IMAGES?>icon_organize_recipes.png"></li>
				<li onclick="if(!this.hasClassName('inactive')) { setContent('recipe_edit',{mode: 'redirect',action: 'recipe_add'}); } else { return false }" class="inactive">Add a Recipe<img src="<?=IMAGES?>icon_new_recipe.png"></li>
			<?php
			if($_SESSION['user']->level > 7) {
			?>
				<li class="blank">&nbsp;</li>
				<li class="inactive">Delete This Order<img src="<?=IMAGES?>icon_delete.png"></li>
			<?
			}
			?>
			</ul>
			<?php
			if($_SESSION['user']->level > 6) {
			?>
			<li onclick="setContent('people_list',{mode:'redirect',action:'customers'})">Customers</li>
			<ul class="sublist" style="left: -140px; display: none">
				<li id="customers" onclick="expandSublist(this); return false;" class="sublistButton" style="right: -125px">></li>
				<li onclick="setContent('people_edit',{mode: 'redirect',action: 'customers_add'})">Add a Customer<img src="<?=IMAGES?>icon_add_order.png"></li>
				<li onclick="setContent('print_win',{mode: 'popup',action: 'customers_list'})">Print Customer List<img src="<?=IMAGES?>icon_print_list.png"></li>
				<li class="blank">&nbsp;</li>
				<li onclick="setContent('print_win',{mode: 'popup',action: 'customers_list'" class="inactive">Print Customer<img src="<?=IMAGES?>icon_print_one.png"></li>
				<li onclick="if(!this.hasClassName('inactive')) { setContent('contact_one',{mode: 'redirect', action: 'contact_compose', id: '<?=$person_id?>'}); } else { return false; }" class="inactive">Contact Customer<img src="<?=IMAGES?>icon_send_message.png"></li>
				<?php
				if($_SESSION['user']->level > 7) {
				?>
				<li class="blank">&nbsp;</li>
				<li onclick="if(!this.hasClassName('inactive')) { setContent('recipe_list',{mode: 'redirect'}); } else { return false }" class="inactive">Delete Customer<img src="<?=IMAGES?>icon_delete.png"></li>
				<?
				}
				?>
			</ul>
			<li onclick="setContent('people_list',{mode:'redirect',action:'users'})">Users</li>
			<ul class="sublist" style="left: -140px; display: none">
				<li id="users" onclick="expandSublist(this); return false;" class="sublistButton" style="right: -125px">></li>
				<li onclick="setContent('people_edit',{mode: 'redirect',action: 'users_add'})">Add a User<img src="<?=IMAGES?>icon_add_order.png"></li>
				<li onclick="setContent('print_win',{mode: 'popup',action: 'users_list'})">Print Users List<img src="<?=IMAGES?>icon_print_list.png"></li>
				<li class="blank">&nbsp;</li>
				<li onclick="if(!this.hasClassName('inactive')) { setContent('print_win',{mode: 'popup',action: 'person',id: '<?=$person_id?>'}); } else { return false; }" class="inactive">Print User<img src="<?=IMAGES?>icon_print_one.png"></li>
				<li onclick="if(!this.hasClassName('inactive')) { setContent('contact_one',{mode: 'redirect', action: 'contact_compose', id: '<?=$person_id?>'}); } else { return false; }" class="inactive">Contact User<img src="<?=IMAGES?>icon_send_message.png"></li>
				<?php
				if($_SESSION['user']->level > 7) {
				?>
				<li class="blank">&nbsp;</li>
				<li onclick="if(!this.hasClassName('inactive')) { setContent('recipe_list',{mode: 'redirect'}); } else { return false }" class="inactive">Delete User<img src="<?=IMAGES?>icon_delete.png"></li>
				<?
				}
				?>
			</ul>
			<?php
			}
			if($_SESSION['user']->level >= 8) {
			?>
			<li onclick="setContent('people_list',{mode:'redirect',action:'contractors'})">Contractors</li>
			<ul class="sublist" style="left: -140px; display: none">
				<li id="contractors" onclick="expandSublist(this); return false;" class="sublistButton" style="right: -125px">></li>
				<li onclick="setContent('people_edit',{mode: 'redirect',action: 'contractors_add'})">Add a Contractor<img src="<?=IMAGES?>icon_add_order.png"></li>
				<li onclick="setContent('print_win',{mode: 'popup',action: 'contractors_list'})">Print Contractor List<img src="<?=IMAGES?>icon_print_list.png"></li>
				<li class="blank">&nbsp;</li>
				<li onclick="if(!this.hasClassName('inactive')) { setContent('print_win',{mode: 'popup',action: 'person',id: '<?=$person_id?>'}); } else { return false; }" class="inactive">Print Contractor<img src="<?=IMAGES?>icon_print_one.png"></li>
				<li onclick="if(!this.hasClassName('inactive')) { setContent('contact_one',{mode: 'redirect', action: 'contact_compose', id: '<?=$person_id?>'}); } else { return false; }" class="inactive">Contact Contractor<img src="<?=IMAGES?>icon_send_message.png"></li>
				<?php
				if($_SESSION['user']->level > 7) {
				?>
				<li class="blank">&nbsp;</li>
				<li onclick="if(!this.hasClassName('inactive')) { setContent('recipe_list',{mode: 'redirect'}); } else { return false }" class="inactive">Delete Contractor<img src="<?=IMAGES?>icon_delete.png"></li>
				<?
				}
				?>
			</ul>
			<li onclick="setContent('reports',{mode:'redirect',action:'account'})">Reports</li>
			<ul class="sublist" style="left: -140px; display: none">
				<li id="reports" onclick="expandSublist(this); return false;" class="sublistButton" style="right: -125px">></li>
				<li onclick="setContent('report_win',{mode: 'popup',width: 500,height: 350})">Output Report<img src="<?=IMAGES?>icon_print_list.png"></li>
				<li onclick="window.location='<?=ADMIN_URL?>reports.php'">Reset Report<img src="<?=IMAGES?>icon_show_list.png"></li>
			</ul>
			<?php
			}
			?>
			<li onclick="setContent('message_center',{mode:'redirect'})">Messages</li>
			<ul class="sublist" style="left: -140px; display: none"></ul>
			<li class="blank"></li>
			<li onclick="setContent('index',{mode:'redirect',action:'logout'})">Log Out</li>
		</ul>
</div>
<?=$contentHeader?>
<div id="content" class="content">
<?php if(isset($search)) { ?><div class="search"><?=$search?></div><? } ?>
<div id="dynamic"><?=$content?></div>
<?=$contentFooter?>
</div>
<div class="baseRow">The CPI OMS Administration Utility is brought to you by <a href="mailto:info@phantasea.net">Phantasea Media Group Inc.</a></div>
</body>
</html>