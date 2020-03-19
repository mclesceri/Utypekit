<!DOCTYPE html>
<html lang="en" >
	<!-- begin::Head -->
	<head>
		<meta charset="utf-8" />
		<title>Metronic | Login Page - 1</title>
		<meta name="description" content="Latest updates and statistic charts">
		<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no">
		<!--begin::Web font -->
		<script src="https://ajax.googleapis.com/ajax/libs/webfont/1.6.16/webfont.js"></script>
		<script>
			WebFont.load({
				google : {
					"families" : ["Poppins:300,400,500,600,700", "Roboto:300,400,500,600,700"]
				},
				active : function() {
					sessionStorage.fonts = true;
				}
			});
		</script>
		<!--end::Web font -->
		<!--begin::Base Styles -->
		<link href="{{ URL::asset('assets/vendors/base/vendors.bundle.css') }}" rel="stylesheet" type="text/css" />
		<link href="{{ URL::asset('assets/demo/default/base/style.bundle.css') }}" rel="stylesheet" type="text/css" />
		<!--end::Base Styles -->
		<link rel="shortcut icon" href="assets/demo/default/media/img/logo/favicon.ico" />
		<style>
			select.form-control.m-select{border:0px;padding: 10px 0px !important;    border-bottom: 1px solid #ebedf2;}
		</style>
	</head>
	<!-- end::Head -->
	<!-- end::Body -->
	<body  class="m--skin- m-header--fixed m-header--fixed-mobile m-aside-left--enabled m-aside-left--skin-dark m-aside-left--fixed m-aside-left--offcanvas m-footer--push m-aside--offcanvas-default"  >
		<!-- begin:: Page -->
		<div class="m-grid m-grid--hor m-grid--root m-page">
			<div class="m-grid__item m-grid__item--fluid m-grid m-grid--ver-desktop m-grid--desktop m-grid--tablet-and-mobile m-grid--hor-tablet-and-mobile m-login m-login--1 m-login--signin" id="m_login">
				<div class="m-grid__item m-grid__item--order-tablet-and-mobile-2 m-login__aside">
					<div class="m-stack m-stack--hor m-stack--desktop">
						<div class="m-stack__item m-stack__item--fluid">
							<div class="m-login__wrapper">
								<div class="m-login__logo">
									<a href="#"> <img src="{{ asset('images/logo.png') }}"> </a>
								</div>
								<div class="m-login__logo">
									   @if (session('status'))
										<div class="alert alert-success">
											{{ session('status') }}
										</div>
										@endif
										@if (session('warning'))
											<div class="alert alert-warning">
												{{ session('warning') }}
											</div>
										@endif
								</div>
								<div class="m-login__signin">
									<div class="m-login__head">
										<h3 class="m-login__title">Sign In To Admin</h3>
									</div>
									<form class="m-login__form m-form" id="mloginForm"  method="POST" action="{{ route('login') }}">
										@csrf
										<div class="form-group m-form__group">
											<input type="text" placeholder="Email" class="form-control m-input {{ $errors->has('email') ? ' is-invalid' : '' }}" name="email" value="{{ old('email') }}" autocomplete="off">
											@if ($errors->has('email'))
											<span class="invalid-feedback"> <strong>{{ $errors->first('email') }}</strong> </span>
											@endif
										</div>
										<div class="form-group m-form__group">
											<input class="form-control m-input m-login__form-input--last {{ $errors->has('password') ? ' is-invalid' : '' }}" name="password"  type="password" placeholder="Password">
											@if ($errors->has('password'))
											<span class="invalid-feedback"> <strong>{{ $errors->first('password') }}</strong> </span>
											@endif
										</div>
										<div class="row m-login__form-sub">
											<div class="col m--align-left">
												<label class="m-checkbox m-checkbox--focus">
													<input type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }}>
													Remember me <span></span> </label>
											</div>
											<div class="col m--align-right">
												<a href="javascript:;" id="m_login_forget_password" class="m-link">Forget Password ?</a>
											</div>
										</div>
										<div class="m-login__form-action">
											<button id="m_login_signin_submits" class="btn btn-accent">
												{{ __('Login') }}
											</button>
										</div>
									</form>
								</div>
								<div class="m-login__signup">
									<div class="m-login__head">
										<h3 class="m-login__title">Sign Up</h3>
										<div class="m-login__desc">
											Enter your details to create your account:
										</div>
									</div>
									<form class="m-login__form m-form"  method="POST" action="{{ route('register') }}">
										@csrf
										<div class="form-group m-form__group">
											<select name="org_type" id="org_type" class="form-control m-select" tabindex="0">
												<option value="0">Organization Type</option>
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
											</select>
										</div>
										<div class="form-group m-form__group">
											<input type="text" class="form-control m-input" name="org_name" value="{{ old('org_name') }}" placeholder="Organization Name" autofocus/>
											@if ($errors->has('username')) <span class="invalid-feedback"> <strong>{{ $errors->first('username') }}</strong> </span> @endif
										</div>
										<div class="form-group m-form__group">
											<input type="text" class="form-control m-input {{ $errors->has('username') ? ' is-invalid' : '' }}" name="username" value="{{ old('username') }}" placeholder="Username" required autofocus/>
											@if ($errors->has('username')) <span class="invalid-feedback"> <strong>{{ $errors->first('username') }}</strong> </span> @endif
										</div>
										<div class="form-group m-form__group">
											<input type="password" class="form-control m-input{{ $errors->has('password') ? ' is-invalid' : '' }}" name="password" placeholder="Password" value="{{ old('password') }}" required autofocus/>
											@if ($errors->has('password')) <span class="invalid-feedback"> <strong>{{ $errors->first('password') }}</strong> </span> @endif
										</div>
										<div class="form-group m-form__group">
											<input type="text" class="form-control m-input {{ $errors->has('name') ? ' is-invalid' : '' }}" name="name" placeholder="First Name" value="{{ old('name') }}" required autofocus/>
											@if ($errors->has('name')) <span class="invalid-feedback"> <strong>{{ $errors->first('name') }}</strong> </span> @endif
										</div>
										<div class="form-group m-form__group">
											<input type="text" class="form-control m-input{{ $errors->has('lname') ? ' is-invalid' : '' }}" name="lname" placeholder="Last Name" value="{{ old('lname') }}" required autofocus/>
											@if ($errors->has('lname')) <span class="invalid-feedback"> <strong>{{ $errors->first('lname') }}</strong> </span> @endif
										</div>
										<div class="form-group m-form__group">
											<input id="email" type="email" class="form-control m-input {{ $errors->has('email') ? ' is-invalid' : '' }}" name="email" placeholder="Email Address" value="{{ old('email') }}" required>
											@if ($errors->has('email')) <span class="invalid-feedback"> <strong>{{ $errors->first('email') }}</strong> </span> @endif
										</div>
										<div class="form-group m-form__group">
											<input type="text" class="form-control m-input" name="phone" placeholder="Phone" value="{{ old('phone') }}"  />
										</div>
										<div class="form-group m-form__group">
											<input type="text" class="form-control m-input" name="address_1" placeholder="Address 1" value="{{ old('address_1') }}"  />
										</div>
										<div class="form-group m-form__group">
											<input type="text" class="form-control m-input" name="address_2" placeholder="Address 2" value="{{ old('address_2') }}"  />
										</div>
										<div class="form-group m-form__group">
											<input type="text" class="form-control m-input" name="city" placeholder="City" value="{{ old('phone') }}"  />
										</div>
										<div class="form-group m-form__group">
											<select class="form-control m-select" name="state">
												<option value="">State</option><option value="AL">Alabama</option><option value="AK">Alaska</option><option value="AZ">Arizona</option><option value="AR">Arkansas</option><option value="CA">California</option><option value="CO">Colorado</option><option value="CT">Connecticut</option><option value="DE">Delaware</option><option value="DC">District Of Columbia</option><option value="FL">Florida</option><option value="GA">Georgia</option><option value="HI">Hawaii</option><option value="ID">Idaho</option><option value="IL">Illinois</option><option value="IN">Indiana</option><option value="IA">Iowa</option><option value="KS">Kansas</option><option value="KY">Kentucky</option><option value="LA">Louisiana</option><option value="ME">Maine</option><option value="MD">Maryland</option><option value="MA">Massachusetts</option><option value="MI">Michigan</option><option value="MN">Minnesota</option><option value="MS">Mississippi</option><option value="MO">Missouri</option><option value="MT">Montana</option><option value="NE">Nebraska</option><option value="NV">Nevada</option><option value="NH">New Hampshire</option><option value="NJ">New Jersey</option><option value="NM">New Mexico</option><option value="NY">New York</option><option value="NC">North Carolina</option><option value="ND">North Dakota</option><option value="OH">Ohio</option><option value="OK">Oklahoma</option><option value="OR">Oregon</option><option value="PA">Pennsylvania</option><option value="RI">Rhode Island</option><option value="SC">South Carolina</option><option value="SD">South Dakota</option><option value="TN">Tennessee</option><option value="TX">Texas</option><option value="UT">Utah</option><option value="VT">Vermont</option><option value="VA">Virginia</option><option value="WA">Washington</option><option value="WV">West Virginia</option><option value="WI">Wisconsin</option><option value="WY">Wyoming</option>
											</select>
										</div>
										<div class="form-group m-form__group">
											<input type="text" class="form-control m-input" name="zip" placeholder="Zip" value="{{ old('zip') }}"  />
										</div>
										<div class="form-group m-form__group">
											<input type="text" class="form-control m-input {{ $errors->has('order_title') ? ' is-invalid' : '' }}" name="order_title" value="{{ old('order_title') }}" placeholder="Enter an Order Title" required autofocus />
											@if ($errors->has('order_title')) <span class="invalid-feedback"> <strong>{{ $errors->first('order_title') }}</strong> </span> @endif
										</div>
										<div class="row form-group m-form__group m-login__form-sub">
											<div class="col m--align-left">
												<label class="m-checkbox m-checkbox--focus">
													<input type="checkbox" name="agree">
													I Agree the <a href="#" class="m-link m-link--focus">terms and conditions</a>. <span></span> </label>
												<span class="m-form__help"></span>
											</div>
										</div>
										<div class="m-login__form-action">
											<button id="m_login_signup_submits" class="btn btn-accent">
												Sign Up
											</button>
											<button id="m_login_signup_cancel" class="btn btn-outline-focus  m-btn m-btn--accent m-btn--custom">
												Cancel
											</button>
										</div>
									</form>
								</div>
								<div class="m-login__forget-password">
									<div class="m-login__head">
										<h3 class="m-login__title">Forgotten Password ?</h3>
										<div class="m-login__desc">
											Enter your email to reset your password:
										</div>
									</div>
									<form method="POST" class="m-login__form m-form" action="{{ route('password.email') }}">									@csrf
										<div class="form-group m-form__group">
											<input class="form-control m-input" type="text" placeholder="Email" name="email" id="m_email" autocomplete="off">
										</div>
										<div class="m-login__form-action">
											<button type="submit" id="m_login_forget_password_submits" class="btn btn-focus m-btn m-btn--pill m-btn--custom m-btn--air">
												Request
											</button>
											<button id="m_login_forget_password_cancel" class="btn btn-outline-focus m-btn m-btn--pill m-btn--custom">
												Cancel
											</button>
										</div>
									</form>
								</div>
							</div>
						</div>
						<div class="m-stack__item m-stack__item--center">
							<div class="m-login__account">
								<span class="m-login__account-msg"> Don't have an account yet ? </span>&nbsp;&nbsp; <a href="javascript:;" id="m_login_signup" class="m-link m-link--focus m-login__account-link">Sign Up</a>
							</div>
						</div>
					</div>
				</div>
				<div class="m-grid__item m-grid__item--fluid m-grid m-grid--center m-grid--hor m-grid__item--order-tablet-and-mobile-1	m-login__content m-grid-item--center" style="background-image: url({{ asset('assets/app/media/img//bg/bg-4.jpg') }})">
					<div class="m-grid__item">
						<h3 class="m-login__welcome">Join Our Community</h3>
						<p class="m-login__msg">
							Lorem ipsum dolor sit amet, coectetuer adipiscing
							<br>
							elit sed diam nonummy et nibh euismod
						</p>
					</div>
				</div>
			</div>
		</div>
		<!-- end:: Page -->
		<!--begin::Base Scripts -->
		<script src="{{ URL::asset('assets/vendors/base/vendors.bundle.js') }}" type="text/javascript"></script>
		<script src="{{ URL::asset('assets/demo/default/base/scripts.bundle.js') }}" type="text/javascript"></script>
		<!--end::Base Scripts -->
		<!--begin::Page Snippets -->
		<script src="{{ URL::asset('assets/snippets/custom/pages/user/login.js') }}" type="text/javascript"></script>
		<!--end::Page Snippets -->
	</body>
	<!-- end::Body -->
</html>