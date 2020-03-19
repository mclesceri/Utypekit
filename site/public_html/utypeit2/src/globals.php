<?php
if ( false ) {
	if( $_SERVER["HTTPS"] != "on" ) {
		header("Location: https://" . $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"]);
		exit();
	}
} else {
	if( $_SERVER["HTTPS"] != "on" ) {
		if ( defined('FORCE_HTTPS') && FORCE_HTTPS === true && defined('FORCE_HTTPS_TEMP') ) {
			header("Location: https://" . $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"]);
			exit();
		}
	} elseif ( !defined('FORCE_HTTPS') || (defined('FORCE_HTTPS') && FORCE_HTTPS === false) || !defined('FORCE_HTTPS_TEMP') ) {
		header("Location: http://" . $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"]);
		exit();
	}
}

//ini_set('display_errors',1);
//error_reporting(-1);

define('ROOT_URI',$_SERVER['DOCUMENT_ROOT'].'/');

define('ROOT_URL', (( $_SERVER["HTTPS"] == "on" ) ? 'https' : 'http') . '://'.$_SERVER['HTTP_HOST'].'/');

define('FONTS',ROOT_URL.'webfonts/');

$rootDir = explode( '/', dirname( $_SERVER['PHP_SELF'] ) ); // dbrong 170718 dynamically select
if ( $rootDir[0] == '' && isset($rootDir[1]) ) {
	$rootDir = $rootDir[1];
} else {
	$rootDir = 'utypeit2';
}

define('UTI_URI', ROOT_URI . $rootDir . '/');
define('UTI_URL', ROOT_URL . $rootDir . '/');

define('SRC',UTI_URI.'src/');
define('INCLUDES',SRC.'includes/');
define('SERVICES',SRC.'services/');
define('DATA',SRC.'data/');

/* UNIVERSAL GLOBALS */
define('HELP',UTI_URL.'src/data/help/');

/* ADMIN GLOBALS */
define('ADMIN_URI',UTI_URI.'admin/');
define('ADMIN_URL',UTI_URL.'admin/');
define('A_MEDIA',ADMIN_URL.'media/');
define('A_CSS',A_MEDIA.'css/');
define('A_JS',A_MEDIA.'js/');

/* UTYPEIT GLOBALS */
define('U_MEDIA',UTI_URL.'media/');
define('U_CSS',U_MEDIA.'css/');
define('U_JS',U_MEDIA.'js/');

define('IMAGES',U_MEDIA.'images/');
define('TEMPLATES',UTI_URI.'media/templates/');
define('TEMPLATE',UTI_URL.'media/templates/');

set_include_path('/storage/av03339/pear/php' . PATH_SEPARATOR
                 . get_include_path());
?>
