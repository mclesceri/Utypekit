@extends('layout.mainlayout')
@section('content')
<style>
	@charset "UTF-8";
	/* WIZARD STYLES */

	table {
		width: 98%;
		border: none;
		border-collapse: collapse;
	}
	th, td {
		padding: 2px;
	}
	th {
		text-align: center;
	}
	td {
		text-align: left;
	}
	button {
		background-color: #FF9900;
		color: #000000;
		font-family: Museo-700;
		font-size: 12px;
		cursor: pointer;
		text-align: center;
		white-space: nowrap;
		text-overflow: ellipsis;
		overflow: hidden;
		background-color: #FCBA63;
		filter: progid:DXImageTransform.Microsoft.gradient(startColorstr='#FCBA63', endColorstr='#F37043'); /* for IE */
		background: -webkit-gradient(linear, left top, left bottom, from(#FCBA63), to(#F37043)); /* for webkit browsers */
		background: -moz-linear-gradient(top,  #FCBA63,  #F37043); /* for firefox 3.6+ */
		border: 1px #333333 solid;
		border-radius: 4px;
	}
	button:disabled {
		background-color: #EFEFEF;
		color: #CCCCCC;
		cursor: default;
		filter: progid:DXImageTransform.Microsoft.gradient(startColorstr='#CCCCCC', endColorstr='#EFEFEF'); /* for IE */
		background: -webkit-gradient(linear, left top, left bottom, from(#CCCCCC), to(#EFEFEF)); /* for webkit browsers */
		background: -moz-linear-gradient(top,  #CCCCCC,  #EFEFEF); /* for firefox 3.6+ */
		border: 1px #CCCCCC solid;
	}
	a {
		color: #4b63ae;
	}
	a:hover {
		color: #bdd158;
	}
	a.help {
		display: inline-block;
		width: 16px;
		padding: 4px;
		font-weight: bold;
		margin: 0 5px 0 5px;
		background: #577212;
		border: 1px #333333 solid;
		border-radius: 10px;
		text-decoration: none;
		font-weight: bold;
		line-height: 1em;
		text-align: center;
		text-shadow: 0 0 2px #000000;
		box-shadow: 1px 1px 2px #999999;
		color: #ffffff;
	}
	a.help:hover {
		background: #BDD168;
		color: #000000;
	}
	input[type="text"], input[type="password"], select {
		border: 1px #333333 solid;
		border-radius: 4px;
		background: #fff;
		padding: 4px;
	}
	input[type="text"]:disabled, input[type="password"]:disabled, select:disabled {
		border: 1px #CCCCCC solid;
		background: #EFEFEF;
	}
	input[type="text"].error, input[type="password"].error, select.error {
		color: #FF0000;
		border: 1px #FF0000 solid;
	}
	/*
	 *
	 *  Form Elements
	 *
	 */
	.label, .submit, .right {
		text-align: right;
	}
	/*
	 *
	 *  Container Elements
	 *
	 */
	.invalid-feedback{display: block;}
	#headerWrap {
		background-color: #2d8ba0;
	}
	#header {
		display: block;
		position: relative;
		clear: both;
		width: 1000px;
		height: 160px;
		margin: 0 auto;
		margin-bottom: 10px;
	}
	#logo {
		display: inline-block;
		position: absolute;
		left: 30px;
		top: 10px;
		width: 150px;
		height: 150px;
	}
	.headerLeft {
		display: inline-block;
		position: absolute;
		left: 150px;
		bottom: 10px;
	}
	.headerMiddle {
		display: inline-block;
		position: absolute;
		left: 320px;
		bottom: 10px;
		font-size: 16px;
	}
	.headerRight {
		display: inline-block;
		position: absolute;
		left: 150px;
		bottom: 10px;
	}
	#uti_logo {
		display: inline-block;
		position: absolute;
		right: 30px;
		top: 10px;
		width: 263px;
		height: 70px;
	}
	#content {
		display: block;
		position: relative;
		clear: both;
		width: 1000px;
		margin: 0 auto;
	}
	.contentHeaderBlock {
		display: block;
		position: relative;
		max-width: 968px;
		height: 50px;
		background: #008ca2;
		-moz-border-radius: 8px 8px 0px 0px;
		border-radius: 8px 8px 0px 0px;
		padding: 4px 16px 0 16px;
		margin: 0 auto;
		color: #FFFFFF;
		font-weight: bold;
	}
	.contentHeaderBlock ul {
		display: block;
		position: absolute;
		list-style-type: none;
		margin: 0 20px 0 0;
		right: 0;
		bottom: 0;
		font-size: 10px;
	}
	.contentHeaderBlock ul li {
		display: inline-block;
		position: relative;
		background-color: #FCBA63;
		filter: progid:DXImageTransform.Microsoft.gradient(startColorstr='#FCBA63', endColorstr='#F37043'); /* for IE */
		background: -webkit-gradient(linear, left top, left bottom, from(#FCBA63), to(#F37043)); /* for webkit browsers */
		background: -moz-linear-gradient(top,  #FCBA63,  #F37043); /* for firefox 3.6+ */
		border-radius: 8px 8px 0 0;
		width: 100px;
		height: 20px;
		padding: 2px 5px 2px 5px;
		line-height: 20px;
		margin: 0 2px 0 2px;
		bottom: 0;
		font-size: 14px;
		text-align: center;
		cursor: pointer;
		box-shadow: 0 -1px 1px #333;
		color: #FFFFFF;
		text-shadow: 0 1px 2px #000000;
	}
	.contentHeaderBlock ul li:hover {
		background: #FCBA63;
		text-shadow: none;
		color: #000000;
	}
	.contentHeaderBlock .disabled {
		background: #999999;
		color: #CCCCCC;
		display: inline-block;
		position: relative;
		border-radius: 8px 8px 0 0;
		width: 100px;
		height: 20px;
		padding: 2px 5px 2px 5px;
		line-height: 20px;
		margin: 0 2px 0 2px;
		bottom: 0;
		text-align: center;
		cursor: default;
		text-shadow: none;
		box-shadow: 0 -1px 1px #333;
	}
	.contentHeaderBlock .disabled:hover {
		background: #999999;
		color: #CCCCCC;
	}
	.contentHeaderBlock .blank {
		background: none;
		filter: none;
		width: 20px;
		box-shadow: none;
		cursor: default;
	}
	.contentHeaderBlock .blank:hover {
		background: none;
		cursor: default;
	}
	.contentHeaderLeft {
		display: inline-block;
		float: left;
		height: 25px;
		line-height: 40px;
		text-align: left;
	}
	.contentHeaderRight {
		display: inline-block;
		float: right;
		height: 25px;
		text-align: right;
	}
	#signup_wizard {
		width: 100%;
		display: table;
		height: 500px;
	}
	#feedback {
		display: block;
		position: relative;
		margin: 0 auto;
		margin-bottom: 5px;
		max-width: 968px;
		min-height: 24px;
		padding: 10px 100px 10px 100px;
		font-size: 14px;
		line-height: 1.25em;
		background: #CCCCCC;
		border-bottom: 1px #333333 solid;
	}
	.m-page{position: absolute;}
	/* Wizard Elements */
	#form_container {
		display: block;
		position: relative;
		clear: both;
		max-width: 968px;
		height: 100%;
		margin: 0 auto;
		padding: 0;
		/*background: #558184;*/
		overflow: hidden;
	}
	#form_slider {
		display: block;
		margin: 0;
		padding: 0;
	}

	#slide_1 {
		left: 0;
	}
	#slide_2 {
		left: 1010px;
	}
	#slide_3 {
		left: 2020px;
	}
	#slide_4 {
		left: 3030px;
	}
	#slide_5 {
		left: 4040px;
	}
	#slide_6 {
		left: 5050px;
	}
	.centsaver_off {
		background: rgba(208,255,216,.8);
	}
	.centsaver_on {
		background: rgba(208,255,216,.8) url(../images/cent_mark.png) no-repeat right center;
	}
	.designeroption_off {
		background: rgba(154,220,244,.6);
	}
	.designeroption_on {
		background: rgba(154,220,244,.6) url(../images/designer_mark.png) no-repeat right center;
	}
	#contributor_list {
		width: 250px;
		border: 1px #666666 solid;
	}
	#recipe_formats {
		display: block;
		position: absolute;
		right: 0;
		top: 0;
		overflow: hidden;
		width: 500px;
		height: 280px;
		padding: 0;
		margin: 0;
		text-align: center;
	}
	#format_slider {
		display: block;
		position: absolute;
		left: 0;
		top: 0;
		width: 500px;
		height: 230px;
		margin: 0;
		padding: 0;
		overflow: hidden;
	}
	#formats {
		display: block;
		position: absolute;
		left: 0;
		top: 0;
		width: 5000px;
		height: 230px;
	}
	#formats p {
		line-height: 2em;
		margin: 0;
		padding: 5px;
		height: 15px;
	}
	.format {
		display: inline-block;
		width: 500px;
		float: left;
	}
	#format_buttons {
		display: block;
		position: absolute;
		bottom: 0;
		left: 0;
		width: 500px;
		height: 30px;
	}
	#format_buttons button {
		border: 1px #333 solid;
		background: #639499;
		color: #FFFFFF;
	}
	#format_button_spacer {
		display: inline-block;
		width: 250px;
	}
	#base {
		display: block;
		position: relative;
		clear: both;
		width: 100%;
		/*margin: 0 auto;*/
		margin-top: 50px;
		margin-left: auto;
		margin-right: auto;
		text-align: center;
		padding: 8px;
		background-color: #181818;
		font-size: 11px;
		color: #EFEFEF;
	}
	#base p {
		display: block;
		position: relative;
		margin: 0 auto;
		width: 500px;
		text-align: center;
		padding: 4px;
		font-size: 10px;
	}
	#base a {
		color: #EFEFEF;
	}
	#base a:hover {
		color: #FFFFFF;
	}
	#bottom_buttons {
		margin: 5px 0 60px 0;
	}
	/* ORDERED LIST */
	.orderListColumn {
		display: block;
		float: left;
		overflow: auto;
		width: 450px;
		padding: 5px;
		height: 250px;
		border-right: 1px #333333 solid;
	}
	.orderListSection {
		display: block;
		width: 416px;
		padding: 2px;
		border-bottom: 1px #999999 solid;
	}
	.orderListSection input {
		border: 1px #999999 solid;
		background: none;
		width: 250px;
	}
	.orderListSectionControls {
		float: right;
		width: 130px;
	}
	.orderListSectionControls img {
		float: left;
		cursor: pointer;
	}
	img.showSubcats {
		float: right;
		margin-right: 15px;
		cursor: pointer;
	}
	.orderListSubsectionTitle {
		font-weight: bold;
		border-bottom: 1px #333333 solid;
	}
	#slide_5 table {
		display: inline-block;
		position: relative;
		width: 460px;
		height: 30px;
		border-bottom: 1px #333333 solid;
	}
	#slide_5 table td {
		padding: 4px;
		text-align: center;
		font-size: .7em;
		width: 90px;
		line-height: .9em;
		vertical-align: middle;
	}
	.disabled {
		color: #999999;
	}
	#slide_5 table td:first-child {
		width: 250px;
		text-align: left;
		font-size: 1em;
	}

	/* input styles */
	#email, #login, #password, #first_name, #last_name, #address1, #address2 {
		width: 98%;
		border: 1px solid #000;
		color: #4E4E4E;
		background-color: #FFF;
		border-radius: 4px;
		padding: 10px !important;
		font-size: 1em;
	}

	#organization_type {
		width: 82%;
		border: 1px solid #000;
		color: #4E4E4E;
		background-color: #FFF;
		border-radius: 4px;
		padding: 10px !important;
		font-size: 1em;
	}

	#organization_name {
		width: 80%;
		border: 1px solid #000;
		color: #4E4E4E;
		background-color: #FFF;
		border-radius: 4px;
		padding: 10px !important;
		font-size: 1em;
	}

	#city {
		width: 37%;
		border: 1px solid #000;
		color: #4E4E4E;
		background-color: #FFF;
		border-radius: 4px;
		padding: 10px !important;
		font-size: 1em;
	}

	#phone, #zip, #state {
		border: 1px solid #000;
		color: #4E4E4E;
		background-color: #FFF;
		border-radius: 4px;
		padding: 10px !important;
		font-size: 1em;
	}
</style>

<div class="container">
	<div class="contentHeaderBlock" id="contentHeaderBlock">
		<div class="contentHeaderLeft" id="header_left">
			Account Holder Signup
		</div>
	</div>
	<div id="feedback">
		Please fill out  the information  below and then click the sign up button to create  your account.
	</div>
	<!-- END WIZARD HEADER /-->
	<!-- WIZARD FORM /-->
	<form id="signup_wizard"  method="POST" action="{{ route('register') }}">
		@csrf
		<div id="form_container">
			<div id="form_slider">
				<!-- START : Account Holder /-->
				<div id="slide_1" class="slide">
					<table>
						<tr>
							<td class="label">Organization Type: </td>
							<td class="input">
							<select name="org_type" id="org_type" tabindex="0">
								<option value="0"> -- </option>
								<option value="Business">Business</option>
								<option value="School">School</option>
								<option value="Church">Church</option>
								<option value="Family">Family</option>
								<option value="Civic">Civic</option>
								<option value="Military">Military</option>
								<option value="State Agency">State Agency</option>
								<option value="Lodge">Lodge</option>
								<option value="Pageant">Pageant</option>
								<option value="Daycare/Preschool">Daycare/Preschool</option><option value="other">Other</option>
							</select><a href="#" class="lightwindow help" data-toggle="tooltip" title='Please select the type of organization or group from the drop-down list and enter the name below. If your organization's type is not listed, choose "Other" and type your category into the empty field.' params="lightwindow_width=500,lightwindow_height=300"title="Organization Type">?</a></td>
							<td class="label">Organization Name: </td>
							<td class="input">
							<input type="text" name="org_name" id="org_name" value="" tabindex="1" />
							<a href="#" title="For an individual or family enter the individual, or family name." data-toggle="tooltip" class="lightwindow help" params="lightwindow_width=500,lightwindow_height=300"title="Organization Name">?</a></td>
						</tr>
						<tr>
							<td>&nbsp;</td>
							<td class="input">
							<input type="text" name="other_type" id="other_type" disabled="disabled" tabindex="2" />
							</td>
							<td>&nbsp;</td>
							<td>&nbsp;</td>
						</tr>
						<tr>
							<td class="label"><span style="color: red;">*</span> Username: </td>
							<td class="input">
							<input type="text" class="forms-control{{ $errors->has('username') ? ' is-invalid' : '' }}" name="username" value="{{ old('username') }}" required autofocus/>
							@if ($errors->has('username')) <span class="invalid-feedback"> <strong>{{ $errors->first('username') }}</strong> </span> @endif
							</td>
							<td class="label"><span style="color: red;">*</span> Password: </td>
							<td class="input">
							<input type="password" class="forms-control{{ $errors->has('password') ? ' is-invalid' : '' }}" name="password" value="{{ old('password') }}" required autofocus/>
							@if ($errors->has('password')) <span class="invalid-feedback"> <strong>{{ $errors->first('password') }}</strong> </span> @endif
							</td>
						</tr>
						<tr>
							<td colspan="4" style="text-align: center; font-size: .9em; color: #333333">NOTE: Username and password are limited to 15 characters each. </td>
						</tr>
						<tr>
							<td class="label"><span style="color: red;">*</span> First Name: </td>
							<td class="input">
							<input type="text" class="forms-control{{ $errors->has('name') ? ' is-invalid' : '' }}" name="name" value="{{ old('name') }}" required autofocus/>
							@if ($errors->has('name')) <span class="invalid-feedback"> <strong>{{ $errors->first('name') }}</strong> </span> @endif
							</td>
							<td class="label"><span style="color: red;">*</span> Last Name: </td>
							<td class="input">
							<input type="text" class="forms-control{{ $errors->has('lname') ? ' is-invalid' : '' }}" name="lname" value="{{ old('lname') }}" required autofocus/>
							@if ($errors->has('lname')) <span class="invalid-feedback"> <strong>{{ $errors->first('lname') }}</strong> </span> @endif
							</td>
						</tr>

						<tr>
							<td class="label"><span style="color: red;">*</span> Email: </td>
							<td class="input">
							<input id="email" type="email" class="forms-control{{ $errors->has('email') ? ' is-invalid' : '' }}" name="email" value="{{ old('email') }}" required>
							@if ($errors->has('email')) <span class="invalid-feedback"> <strong>{{ $errors->first('email') }}</strong> </span> @endif
							</td>
							<td class="label">Phone: </td>
							<td class="input">
							<input type="text" class="forms-control" name="phone" value="{{ old('phone') }}"  />
							</td>
						</tr>
						<tr>
							<td class="label">Address 1: </td>
							<td class="input">
							<input type="text" class="forms-control" name="address_1" value="{{ old('address_1') }}"/>
							</td>
							<td class="label">Address 2: </td>
							<td class="input">
							<input type="text" class="forms-control" name="address_2" value="{{ old('address_2') }}"/>
							</td>
						</tr>
						<tr>
							<td class="label">City/State/Zip: </td>
							<td class="input" colspan="3">
							<input type="text" class="forms-control" name="city" value="{{ old('city') }}"/>
							<select class="forms-control" name="state" value="{{ old('state') }}">
								<option value="">Select One... </option><option value="AL">Alabama</option><option value="AK">Alaska</option><option value="AZ">Arizona</option><option value="AR">Arkansas</option><option value="CA">California</option><option value="CO">Colorado</option><option value="CT">Connecticut</option><option value="DE">Delaware</option><option value="DC">District Of Columbia</option><option value="FL">Florida</option><option value="GA">Georgia</option><option value="HI">Hawaii</option><option value="ID">Idaho</option><option value="IL">Illinois</option><option value="IN">Indiana</option><option value="IA">Iowa</option><option value="KS">Kansas</option><option value="KY">Kentucky</option><option value="LA">Louisiana</option><option value="ME">Maine</option><option value="MD">Maryland</option><option value="MA">Massachusetts</option><option value="MI">Michigan</option><option value="MN">Minnesota</option><option value="MS">Mississippi</option><option value="MO">Missouri</option><option value="MT">Montana</option><option value="NE">Nebraska</option><option value="NV">Nevada</option><option value="NH">New Hampshire</option><option value="NJ">New Jersey</option><option value="NM">New Mexico</option><option value="NY">New York</option><option value="NC">North Carolina</option><option value="ND">North Dakota</option><option value="OH">Ohio</option><option value="OK">Oklahoma</option><option value="OR">Oregon</option><option value="PA">Pennsylvania</option><option value="RI">Rhode Island</option><option value="SC">South Carolina</option><option value="SD">South Dakota</option><option value="TN">Tennessee</option><option value="TX">Texas</option><option value="UT">Utah</option><option value="VT">Vermont</option><option value="VA">Virginia</option><option value="WA">Washington</option><option value="WV">West Virginia</option><option value="WI">Wisconsin</option><option value="WY">Wyoming</option>
							</select>
							<input type="text" class="forms-control" name="zip" value="{{ old('zip') }}"/>
							</td>
						</tr>
						<tr>
							<td class="label"><span style="color: red;">*</span>Enter an Order Title: </td>
							<td class="input">
							<input type="text" class="forms-control{{ $errors->has('order_title') ? ' is-invalid' : '' }}" name="order_title" value="{{ old('order_title') }}" required autofocus />
							@if ($errors->has('order_title')) <span class="invalid-feedback"> <strong>{{ $errors->first('order_title') }}</strong> </span> @endif
							<a href="#" title="Each order requires a name to identify it. This is not the title of your cookbook; that will be entered later." data-toggle="tooltip" class="lightwindow help" params="lightwindow_width=500,lightwindow_height=300"title="Order Title">?</a></td>
							<td colspan="2"></td>
						</tr>
						<tr>
							<td class="label" colspan="2" align="left" style="text-align: left"><span style="color: red">*</span><span>I agree to the <a href="http://cookbookpublishers.com/utypeit2/src/data/html/terms_of_service.html" class="lightwindow" params="lightwindow_width=500,lightwindow_height=300" title="Terms of Service">Terms of Service</a></span>
							<input class="screen" type="checkbox" name="terms_of_service" rel="terms" id="terms_of_service" required>
							</td>
						</tr>
						<tr>
							<td class="submit" colspan="4">
							<button name="next_bottom"  style="width: 100px; padding: 8px">
								Sign Up
							</button></td>
						</tr>
					</table>
				</div>
				<!-- END /-->
			</div>
		</div>

	</form>
	<!-- END WIZARD FORM /-->
	<!-- WIZARD BASE /-->

	<p class="museo_slab_500_italic t_ds99-1" style="font-size: 12pt; text-align: right;">
		Please be patient while your recipe account is set up. Do not click the "Sign Up" button more than once.
	</p>
	<!-- END WIZARD BASE /-->

	

</div>

@endsection

