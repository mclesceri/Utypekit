<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>Welcome to UTypeIt Online</title>
<link href="<?=U_CSS?>reset.css" rel="stylesheet" type="text/css" />
<link href="<?=FONTS?>css/webfonts.css" rel="stylesheet" type="text/css" />
<link href="<?=U_CSS?>colors.css" rel="stylesheet" type="text/css" />
<link href="<?=U_CSS?>style.css" rel="stylesheet" type="text/css" />
<link href="<?=U_CSS?>login.css" rel="stylesheet" type="text/css" />
<link href="<?=U_CSS?>lightwindow.css" rel="stylesheet" type="text/css" />
<script src="<?=U_JS?>prototype.js" type="text/javascript"></script>
<script src="<?=U_JS?>scriptaculous.js" type="text/javascript"></script>
<script src="<?=U_JS?>lightwindow.js" type='text/javascript'></script>
</head>

<body>
<div id="headerWrap">
<div id="header">
    <div id="logo"><a href="http://dev.cbp.ctcsdev.com"><img src="<?=IMAGES?>cookbook-logo-new2.png" /></a></div>
	<!--<div class="kitOrder" onclick="window.location='http://dev.cbp.ctcsdev.com/order-a-free-kit'"/></div>-->
	<div id="uti_logo"><img src="<?=IMAGES?>utypeit_logo.png" style="margin: 30px 0px 0 0; float: right;" /></div>
</div>
</div>

<div id="content">
	<div id="left" class="b_ds300-3">
		<?php
		if($page == 'index') {
		?>
		<div class="header museo_slab_500">MEMBER LOGIN</div>
		<div class="content b_ds303-4">
			<div id="loginMessage" class="loginMessage"><?=$login_message?></div>
			<form action="<?=$_SERVER['PHP_SELF']?>" method="POST">
			<input type="hidden" name="action" value="login">
			<table border="0" cellpadding="4" cellspacing="0" style="margin-bottom: 10px">
				<tr>
					<td>Login: </td>
				</tr>
				<tr>
					<td><input type="text" class="login" name="login" value="<?=$login?>" maxlength="15"></td>
				</tr>
				<tr>
					<td>Password: </td>
				</tr>
				<tr>
					<td><input type="password" class="login" name="password" value="<?=$password?>" maxlength="15"></td>
				</tr>
				<tr>
					
				</tr>
				<tr>
					<td class="submit"><button type="submit">Log In</button></td>
				</tr>
				<?php
				$selected = '';
				if(isset($_COOKIE['auto_login'])) {
					$selected = ' checked';
				}
				?>
				<tr>
					<td class="formRight">Remember me <input type="checkbox" name="cookie" value="set_cookie"<?=$selected?>></td>
				</tr>
                <td class="right"><a href="lost_password.php">Lost your login info?</a></td>
			</table>
			</form>
		</div>
		<?php
		} else {
		?>
		<div class="header museo_slab_500">MEMBERS LOGIN</div>
		<div class="content b_ds97-3">
			<div style="display: block; margin: 5px; padding: 10px"><a href="index.php">Back to Login</a></div>
		</div>
		<?php
		}
		?>
		<div class="header museo_slab_500">NOT A MEMBER?</div>
		<div class="content b_ds22-3 t_black" style="padding: 0px;">
			<!--<p>Not a U-Type-It<span style="font-size: 6px; vertical-align: super">TM</span> member yet? To start making your cookbook, click on the "Start Now" button below and begin the short steps to self-publishing!</p> 
			<p>We even have a demo version available that allows you to try out the software first.<br />Come on in!</p>-->
			<div class="right" style="margin-top: 10px"><button onclick="window.location='setup_wizard.php'">Start Now</button></div>
		</div>
	   <p style="margin-top: 10px"><a href="<?=UTI_URL?>src/data/docs/UTI2_User_Guide.pdf" target="_blank">U-Type-It&trade; User Guide (PDF)</a></p>			
	</div>
	<div id="right"><?=$content?></div>
</div>
<div id="base"><a href="http://dev.cbp.ctcsdev.com">Cookbook Publishers</a> | <a href="mailto: info@dev.cbp.ctcsdev.com">Email Support</a><p>© Copyright Cookbook Publishers, Inc. 1.800.227.7282</p></div>
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
