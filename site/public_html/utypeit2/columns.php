<?php

session_start();

require_once('src/globals.php');

$_SESSION['order_id'] = 50;

?>
<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<link rel="stylesheet" type="text/css" href="media/css/reset.css" />
	<link rel="stylesheet" type="text/css" href="media/css/style.css" />
	<link rel="stylesheet" type="text/css" href="media/css/columns.css" />
	<script src="media/js/prototype.js"></script>
	<script src="media/js/scriptaculous.js"></script>
	<script src="media/js/php.js" type="text/javascript"></script>
	<script src="media/js/columns.js"></script>
	<script type="text/javascript">
		function editRecipe(id) {
			window.location = 'recipe_edit.php?id='+id+'&action=recipe_edit';
		}
		document.observe('dom:loaded', function(){
			var columns = new ColumnList('columnList','<?=UTI_URL?>src/includes/Columns.php',3,'categories,subcategories,recipes');
			columns._create();
		});

	</script>
</head>

<body>
	<div id="columnList"></div>
</body>
</html>