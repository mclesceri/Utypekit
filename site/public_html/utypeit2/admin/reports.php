<?php

session_start();

if(!$_SESSION['login'] == true) {
	header('Location: index.php');
}

$page = 'reports';
$tab = 4;

require_once('../src/globals.php');

$script = "
	function sendReport(form,report) {
		$('report_results').update('<img src=\"".IMAGES."loading.gif\">');
		var url = '".UTI_URL."src/includes/'+report+'.php';
		new Ajax.Request(url,{
			method: 'post',
			parameters: Form.serialize(form),
			onFailure: function(transport) {
				alert(transport.responseStatus);
			},
			onSuccess: function(transport) {
				$('report_results').update(transport.responseText);
			}
		});
	}
";
$out = '
<section class="report_select">
	<form action="'.$_SERVER['PHP_SELF'].'" method="POST" onsubmit="sendReport(this,\'DeadlineReport\'); return false;">
		<input type="hidden" name="action" value="deadline_report">
		<button type="submit">Get Deadline Report</button>
	</form>
	<form action="'.$_SERVER['PHP_SELF'].'" method="POST" onsubmit="sendReport(this,\'MarketingReport\'); return false;">
		<input type="hidden" name="action" value="marketing_report">
		<button type="submit">Get Marketing Report</button>
	</form>
</section>
<section id="report_results"></section>
';

$content = $out;
$content .= $list;

require_once (TEMPLATES . 'reports_header.tpl');
require_once (TEMPLATES . 'reports_footer.tpl');

require_once(TEMPLATES.'admin.tpl');
?>