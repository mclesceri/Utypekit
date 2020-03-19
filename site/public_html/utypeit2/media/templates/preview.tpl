<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title><?=$title?></title>
<link href="<?=U_CSS?>reset.css" rel="stylesheet" type="text/css" />
<link href="../webfonts/css/webfonts.css" rel="stylesheet" type="text/css" />
<link href='http://fonts.googleapis.com/css?family=Alegreya:400,700,400italic' rel='stylesheet' type='text/css'>
<link href="<?=U_CSS?>preview_style.css" rel="stylesheet" type="text/css" />
<link href="<?=U_CSS?>opentip.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="<?=U_JS?>prototype.js"></script>
<script type="text/javascript" src="<?=U_JS?>scriptaculous.js"></script>
<script type="text/javascript" src="<?=U_JS?>functions.js"></script>
<script type="text/javascript">
	var images = '<?=IMAGES?>';
	var includes = '<?=UTI_URL?>/src/includes/';
	var services = '<?=UTI_URL?>/src/services/';
	var _recipesections = '';
	var _contributors = '';
	var _ins_object = '';
	var res = '<?=$location?>';
	function closeMe() {
		var title = 'Edit Recipe';
		var form = 'recipe_edit';
		if(res == 'recipe_add') {
			var url = 'recipe_edit.php?action=recipe_add';
			window.opener.location = url;
		}
		if(res == 'recipe_edit') {
			var url = 'recipe_edit.php?action=recipe_edit&id=<?=$recipe_id?>';
			window.opener.location = url;
		}
		window.close();
	}
	document.observe('dom:loaded', function(){
		if($('subcategory')) {
		$('category').observe('change', function(){
			var select = $( 'category' );
			var val = select.selectedIndex >=0 && select.selectedIndex ? escape(select.options[select.selectedIndex].value): undefined;
			_setSubCatList(val);
			});
		}
	});
</script>
</head>

<body>
    <div id="header"><?=$header?></div>
    <div id="content"><?=$content?></div>
    <div id="base"><button name="close" id="close" onclick="closeMe();">Close Preview</button></div>
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
