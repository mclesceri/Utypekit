<?php

if ( !defined('SRC') ) require_once('../globals.php');


if(isset($_POST['action'])) {
	switch($_POST['action']) {
		case 'retrieve':
			if(file_exists(SRC.'data/messages/'.$_POST['file'].'.html')) {
				echo file_get_contents(SRC.'data/messages/'.$_POST['file'].'.html');
			}
			break;
		case 'store':
			$new = urldecode($_POST['content']);
			$handle = SRC.'data/messages/'.$_POST['file'].'.html';
			$write = fopen($handle, 'w');
			fwrite($write, $new);
			fclose($write);
			echo file_get_contents(SRC.'data/messages/'.$_POST['file'].'.html');
			break;
	}
}

?>