<?
define('FORCE_HTTPS', true);
define('FORCE_HTTPS_TEMP', true);

session_start();

//setcookie('auto_login','',1);

$action =  null;
$page = 'index';
$login_message = '';
$user_login = '';
$user_password = '';

$_SESSION['login'] = false;

$login = null;
$password = null;
if(isset($_COOKIE['auto_login'])) {
	parse_str($_COOKIE['auto_login']);
	$login = $user_login;
	$password = $user_password;
}
$remember = null;

$err = false;

// login functions here
if(isset($_REQUEST['action'])) {
	$action = $_REQUEST['action'];
}

require_once('src/globals.php');

require_once(SERVICES.'BaseService.php');

switch($action) {
	case 'login':
		if(isset($_POST['login'])) {
			$login = $_POST['login'];
		}
		if(isset($_POST['password'])) {
			$password = $_POST['password'];
		}

		if($login != null && $password != null) {
			if(isset($_POST['cookie'])) {
				if($_POST['cookie'] == 'set_cookie') {
					$remember = true;
				}
			}
			// get this person
			$nb = new BaseService();
			$query = 'SELECT * FROM People WHERE login="'.$login.'" AND password="'.$password.'" AND status > 0';
			$res = $nb->sendAndGetOne($query);
			if($res) {
				// If there's a person, find what level the person is...
				
				if($res->level > 1) {
					$err = true;
					$errStr = "<p>You are attempting to log in with an administrator account. Please log into the CPI-OMS Administration Utility to edit orders.</p>";
				} else {
					$_SESSION['user'] = $res;
					$_SESSION['login'] = true;
					
					if(!isset($_COOKIE['auto_login'])) {
						if($remember == true) {
							setcookie('auto_login','user_login='.$login.'&user_password='.$password.'&user_id='.$res->id,time()+(60*60*24*30));
						}
					} else {
						if($remember == false) {
							if(isset($_COOKIE['auto_login'])) {
								setcookie('auto_login','',1);
							}
						}
						if($remember == true) {
							if(isset($_COOKIE['auto_login'])) {
								setcookie('auto_login','',1);
								setcookie('auto_login','user_login='.$login.'&user_password='.$password.'&user_id='.$res->id,time()+(60*60*24*30));
							}
						}
					}
					
					//echo $res->id;
					$query = "UPDATE People SET last_login='".date('Y-m-d H:i:s')."' WHERE id='".$res->id."'";
					$nb->sendAndGetOne($query);
					header('Location: order_list.php');
				}
			} else {
				$res = $nb->sendAndGetOne('SELECT * FROM People WHERE login="'.urlencode($login).'" AND password="'.urlencode($password).'" AND status > 0');
				if($res) {
					// If there's a person, find what level the person is...
					if($res->level > 1) {
						$err = true;
						$errStr = "<p>You are attempting to log in with an administrator account. Please log into the CPI-OMS Administration Utility to edit orders.</p>";
					} else {
						$_SESSION['user'] = $res;
						$_SESSION['login'] = true;
						if(!isset($_COOKIE['auto_login'])) {
							if($remember == true) {
								setcookie('auto_login','user_login='.$login.'&user_password='.$password.'&user_id='.$res->id,time()+(60*60*24*30));
							}
						} else {
							if($remember == false) {
								if(isset($_COOKIE['auto_login'])) {
									setcookie('auto_login','',1);
								}
							}
							if($remember == true) {
								if(isset($_COOKIE['auto_login'])) {
									setcookie('auto_login','',1);
									setcookie('auto_login','user_login='.$login.'&user_password='.$password.'&user_id='.$res->id,time()+(60*60*24*30));
								}
							}
						}
						$query = "UPDATE People SET last_login='".date('Y-m-d H:i:s')."' WHERE id='".$res->id."'";
						$nb->sendAndGetOne($query);
						header('Location: order_list.php');
					}
				} else {
					$login_message = '<p style="color: black; font-weight: bold;">We\'re sorry, the login information used is incorrect. Please check your username and password, and try again.</p>';
					$filename = DATA.'html/welcome_message.html';
					$message = fopen($filename,'r');
					$welcome_message = fread($message, filesize($filename));
					fclose($message);
					$content = $welcome_message;
					include(TEMPLATES.'login.tpl');
				}
			}
		} else {
			$login_message = '<p style="color: black; font-weight: bold;">Please enter a valid username and password to continue.</p>';
			$filename = DATA.'html/welcome_message.html';
			$message = fopen($filename,'r');
			$welcome_message = fread($message, filesize($filename));
			fclose($message);
			$content = $welcome_message;
			include(TEMPLATES.'login.tpl');
		}
		break;
	case 'logout':
		session_destroy();
		header('Location: index.php');
		break;
	default:
		$filename = DATA.'html/welcome_message.html';
		$message = fopen($filename,'r');
		$welcome_message = fread($message, filesize($filename));
		fclose($message);
		$content = $welcome_message;
		include(TEMPLATES.'login.tpl');
		break;
}
?>