<?php

session_start();

if(!$_SESSION['login'] == true) {
	die;
}

$demo  = false;
if($_SESSION['order_id'] == 1) {
	$demo = true;
}

$response = '';
if(isset($_GET['res'])) {
	$response = $_GET['res'];
}

require_once('../src/globals.php');

if(isset($_GET['res'])) {
	$location = $_GET['res'];
} 

$recipe_id = '';
if(isset($_GET['id'])) {
	$recipe_id = $_GET['id'];
}

?>
<!DOCTYPE html>
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
		<title>Recipe Preview</title>
		<link href="<?=A_CSS?>reset.css" rel="stylesheet" type="text/css" />
		<link href="../webfonts/css/webfonts.css" rel="stylesheet" type="text/css" />
		<link href='http://fonts.googleapis.com/css?family=Alegreya:400,700,400italic' rel='stylesheet' type='text/css'>
		<link href="<?=A_CSS?>preview_style.css" rel="stylesheet" type="text/css" />
		<link href="<?=A_CSS?>opentip.css" rel="stylesheet" type="text/css" />
		<script type="text/javascript" src="<?=A_JS?>prototype.js"></script>
		<script type="text/javascript" src="<?=A_JS?>scriptaculous.js"></script>
		<script type="text/javascript" src="<?=A_JS?>php.js"></script>
		<script type="text/javascript" src="<?=A_JS?>preview.js"></script>
		<script type="text/javascript">
			var images = '<?=IMAGES?>';
			var includes = '<?=UTI_URL?>src/includes/';
			var services = '<?=UTI_URL?>src/services/';
			var _recipesections = '';
			var _contributors = '';
			var _ins_object = '';
			
			var preview = null;
			document.observe('dom:loaded', function(){
				preview = new Preview();
				preview._draw();
				if(res != '') {
					preview._setAction(res);
				}
			});
		</script>
	</head>
	
	<body>
		<div id="header">
			<table>
			   <tr>
		            <td class="label">Recipe ID: </td>
		            <td class="input" id="recipe_id"></td>
		            <td class="label">Name: </td>
		            <td class="input" id="recipe_name"></td>
		            <td class="label">&nbsp; </td>
		            <td class="input">&nbsp;</td>
		        </tr>
				<tr>
					<td class="label">Date Added: </td>
					<td class="input" id="date_added"></td>
					<td class="label">Last Modified: </td>
					<td class="input" id="last_modified"></td>
					<td class="label">Added By: </td>
					<td class="input" id="added_by"></td>
				</tr>
				<tr>
					<td class="label">Category: </td>
					<td class="input" id="category_name"></td>
					<td class="label">Subcategory: </td>
					<td class="input" id="subcategory_name"></td>
					<td class="label">Status: </td>
					<td class="input" id="status"></td>
				</tr>
			</table>
		</div>
		<div id="content"></div>
		<?php
			if($demo) {
		?>
		<div id="base"><button name="close" id="close" onclick="window.close();">Close Preview</button></div>
		<?php
			} else {
				if($response != '') {
		?>
				<div id="base">
					<button name="close" class="cancelButton" onclick="window.close();">Cancel Changes</button>
					<button name="close" class="hotButton" id="close" onclick="preview._send('<?=$response?>');",'<?=$recipe_id?>'>Accept Recipe Changes</button>
				</div>
		<?php
				} else {
		?>
		<div id="base"><button name="close" id="close" onclick="window.close();">Close Preview</button></div>
		<?php
				}
			}
		?>
	</body>
</html>