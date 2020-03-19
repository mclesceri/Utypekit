<!DOCTYPE html>
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
		<title>UTypeIt Online Signup Wizard</title>
		<link href="media/css/reset.css" rel="stylesheet" type="text/css" />
		<link href="../webfonts/css/webfonts.css" rel="stylesheet" type="text/css" />
		<link href="media/css/colors.css" rel="stylesheet" type="text/css" />
		<link href="media/css/calendarview.css" rel="stylesheet" type="text/css" />
		<link href="media/css/lightwindow.css" rel="stylesheet" type="text/css" />
		<link href="media/css/wizard.css" rel="stylesheet" type="text/css" />
		
		<script src="media/js/prototype.js" type="text/javascript"></script>
		<script src="media/js/scriptaculous.js" type="text/javascript"></script>
		<script src="media/js/php.js" type='text/javascript'></script>
		<script src="media/js/wizard.js" type='text/javascript'></script>
		<script src="media/js/column_list.js" type='text/javascript'></script>
		<script src="media/js/calendarview.js" type="text/javascript"></script>
		<script src="media/js/lightwindow.js" type="text/javascript"></script>
		
		<script type="text/javascript">
		    var baseurl = '<?=UTI_URL?>';
    		var images = '<?=IMAGES?>';
			var includes = '<?=UTI_URL?>src/includes/';
			var services = '<?=UTI_URL?>src/services/';
			<?=$script?>
		</script>
	</head>

	<body>
		<!-- HEADER BLOCK  /-->
		<div id="headerWrap">
		<div id="header">
			<div id="logo"><a href="http://dev.cbp.ctcsdev.com"><img src="media/images/cookbook-logo-new2.png" /></a></div>
			<div class="headerLeft">&nbsp;</div>
			<div class="headerMiddle museo_slab_500" style="color:#ffffff;">Welcome to the UTypeIt&trade; Online Signup Wizard</div>
			<div class="headerRight">&nbsp;</div>
			<div id="uti_logo"><img src="media/images/utypeit_logo.png" style="margin: 30px 50px 0 0; float: right;" /></div>
		</div>
		</div>
			<!-- END HEADER BLOCK t_ds99-1 /-->
			<div id="content">
			<?=$contentHeader?>
			<?=$content?>
			<?=$contentFooter?>
			</div>
		</div>
		<div id="base"><p><a href="http://dev.cbp.ctcsdev.com">Cookbook Publishers</a> | <a href="faq.php" target="_blank">Help/FAQ</a> | <a href="mailto: support@dev.cbp.ctcsdev.com">Email Support</a></p><p>U-Type-It&trade; Online v 2.0 Brought to you by Cookbook Publishers, 1.800.227.7282</p></div>
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
