<?php

session_start();

if(!$_SESSION['login'] == true) {
    die;
}

require_once('../src/globals.php');
$page = 'message_center';
$title = 'U-Type-It Online Message Center';
$tab = 5;


$script = "
	document.observe('dom:loaded', function(){
		showSet(currentSet);
	});
";

$out = '
<h3>Automated Messages</h3>
<ul id="messages">';

$directory = DATA.'messages/';
if($handle = opendir($directory)) {
	while(false != ($file = readdir($handle))) {
		if($file != '.' && $file != '..') {
			if(!is_dir($file)) {
				$abbr = substr($file,0,strpos($file,'.html'));
				$pretty = ucwords(str_replace('_', ' ', $abbr));
				$out .= '
	<li onclick="_show(\''.$abbr.'\');">'.$pretty.'</li>';
			}
		}
	}
}
$out .= '</ul>
<div id="message_block">
	<textarea id="message_text"></textarea>
</div>';

$out .= '<p>Coming soon to version 2.3: Complete integration with Constant Contact, right here in the CPI OMS Admin.</p>';

$contentHeader = '';
$contentFooter = '';

require_once(TEMPLATES.'contact_header.tpl');
require_once(TEMPLATES.'contact_footer.tpl');
$content = $out;

include(TEMPLATES.'admin.tpl');

?>