<?php
define('FORCE_HTTPS', true);
session_start();

// login functions here
$action = $_REQUEST['action'];
if(!isset($_SESSION['login'])) {
	$_SESSION['login'] = false;
	$login = false;
}

require_once('../src/globals.php');

require_once(SERVICES.'People.php');

$login_form = '<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>CPI OMS Admin</title>
<link href="'.A_CSS.'reset.css" rel="stylesheet" type="text/css" />
<link href="'.A_CSS.'style.css" rel="stylesheet" type="text/css" />

</head>

<body>
<center>
<form action="'.$_SERVER['PHP_SELF'].'" method="POST">
<input type="hidden" name="action" value="login">
	<table border="0" cellpadding="4" cellspacing="0" style="margin-top: 50px; background-color: #EFEFEF; border: 1px #333333 solid">
		<tr>
			<td colspan="2" bgcolor="#6699CC"><p style="font-weight: bold; text-align: center">Welcome to the CPI Administration Utility</p><p style="text-align: center">Please log in below to continue</p></td>
		</tr>
		<tr>
			<td colspan="2">&nbsp;</td>
		</tr>
		<tr>
			<td class="formLabel">Login: </td>
			<td class="formInput"><input type="text" name="login"></td>
		</tr>
		<tr>
			<td class="formLabel">Password: </td>
			<td class="formInput"><input type="password" name="password"></td>
		</tr>
		<tr>
			<td>&nbsp;</td>
			<td  class="formRight"><input type="submit" value="Log In"></td>
		</tr>
	</table>
</form>
<center>
</body>
</html>';

switch($action) {
	case 'login':
		$username = $_POST['login'];
		$password = $_POST['password'];
		$np = new People();
		$result = $np->getPersonByUsernamePassword($username,$password);
		if($result) {
			if($result->level >= 6) {
				$_SESSION['user'] = $result;
				$_SESSION['login'] = true;
				$login = true;
			}
		}
		break;
	case 'logout':
		$_SESSION['user'] = null;
		$_SESSION['login'] = false;
		session_destroy();
		$login = false;
		break;
}

if($login == false) {
	echo $login_form;
} else {
	header('location:order_list.php');
}
?>