<?

session_start();

require_once('src/globals.php');

require_once(INCLUDES."Warnings.php");

$title = "Frequently Asked Questions";
$page_note = $_SESSION['utypeit_info']->welcome_note;

if(file_exists(DATA.'html/user_faq.html')) {		
	$filename = DATA.'html/user_faq.html';
	$faq = fopen($filename,'r');
	$faq_content = fread($faq, filesize($filename));
	fclose($faq);
	$content = $faq_content;
} else {
	$content = null;
}
$content .= '
<p>&nbsp;</p>
<p style="margin-top: 10px"><a href="src/data/docs/UTI2_User_Guide.pdf" target="_blank">U-Type-It&trade; User Guide (PDF)</a></p>
<p>&nbsp;</p>';

$header_left = "";
$header_middle = $title;
$header_right = "";

$script = "
document.observe('dom:loaded', function() {
            fancyNav();
 });";
/*
 * 
 *  Set up the warnings to be displayed for:
 *  recipe count, individual recipe count, entry deadline
 * 
 */
 $warn = '';
 $display = ' style="display: none"';
 
 $x = new Warnings($_SESSION['utypeit_info']);
 $warn = $x->_warnings($_SESSION['order_id'],$_SESSION['user']->id);
 if($warn->error) {
     $_SESSION['warning'] = $warn;
 }
 
include(TEMPLATES.'main.tpl');

?>