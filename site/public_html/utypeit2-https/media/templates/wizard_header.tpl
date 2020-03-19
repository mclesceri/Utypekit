<?php

$head = '
        <!-- WIZARD HEADER /-->
			<div class="contentHeaderBlock" id="contentHeaderBlock">
            <div class="contentHeaderLeft" id="header_left">Step 1 : Account Holder Signup</div>
				<div class="contentHeaderRight">
					<ul>
						<li onclick="wizard._previous(\'back\')" class="disabled" id="back_button"><span style="font-size: 16px">&laquo;</span>- Back</li>
						<li class="blank">&nbsp;</li>
						<li class="blank">&nbsp;</li>
						<li class="blank">&nbsp;</li>
						<li class="blank">&nbsp;</li>
						<li class="blank">&nbsp;</li>
						<li class="blank">&nbsp;</li>
						<li onclick="wizard._next(\'forward\')" id="next_button">Next -<span style="font-size: 16px">&raquo;</span></li>
						<li class="blank">&nbsp;</li>
						<li class="blank">&nbsp;</li>
						<li class="blank">&nbsp;</li>
						<li onclick="window.location = \'http://dev.cbp.ctcsdev.com/utypeit2/\'">Cancel</li>
					</ul>
				</div>
			</div>
			<div id="feedback">Use the "Next" and "Back" buttons above to set up your cookbook with U-Type-It&trade; <em>Online</em>. Items marked with a red asterisk (<span style="color: red">*</span>) are required. For help, click on the <a class="help">?</a> next to the item, or <a href="http://dev.cbp.ctcsdev.com/contact/">contact us</a>. After your choices have been made, click the "Sign Up" button at the bottom right.</div>
			<!-- END WIZARD HEADER /-->';
        
 $contentHeader = $head;
 ?>
