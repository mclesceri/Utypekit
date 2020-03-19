<?

$action= $_REQUEST['action'];

require_once('src/globals.php');
require_once('src/includes/MakeXML.php');

if($action == 'make') {
    $make = new MakeXML();
    $xml = $make->makeMilesXML($_POST['order_id'],'CBSOAP');
    //$xml = htmlentities($xml,ENT_QUOTES,'UTF-8',true);
    $res = $xml;
} else {
    $res = '';
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>TEMPORARY MAKE XML FILE</title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
</head>

<body>
<form action="<?=$_SERVER['PHP_SELF']?>" method="POST">
	<input type="hidden" name="action" value="make">
	<table cellpadding="4" cellspacing="0" border="0">
		<tr>
			<td>Order Id: </td>
			<td><input type="text" name="order_id"></td>
		</tr>
		<tr>
			<td>&nbsp;</td>
			<td><input type="submit" value="Get XML"></td>
		</tr>
	</table>
</form>
<div><?=$res?></div>
</body>
</html>