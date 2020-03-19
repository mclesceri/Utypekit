<?php
$disabled = ' disabled="disabled"';
$base = '
			<!-- WIZARD BASE /-->
			<table id="bottom_buttons" width="100%" border="0" cellspacing="0" cellpadding="0">

				<tr>
					<td class="submit"><button name="next_bottom" id="next_bottom" onclick="wizard._send(); return false;"'.$disabled.' style="width: 100px; padding: 8px">Sign Up</button></td>
				</tr>

			</table>
<p class="museo_slab_500_italic t_ds99-1" style="font-size: 12pt; text-align: right;">Please be patient while your recipe account is set up. Do not click the "Sign Up" button more than once.</p>
			<!-- END WIZARD BASE /-->';

$contentFooter = $base;
?>