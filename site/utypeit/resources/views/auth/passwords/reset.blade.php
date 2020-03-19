<!DOCTYPE html>

<html lang="en" >

	<!-- begin::Head -->

	<head>

		<meta charset="utf-8" />



		<title>Metronic | Reset Password </title>

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

										<h3 class="m-login__title">{{ __('Reset Password') }}</h3>



									</div>



									<form class="m-login__form m-form" method="POST" action="{{ route('password.request') }}">
									@csrf		

									
										<input type="hidden" name="token" value="{{ $token }}">
										<div class="form-group m-form__group">

											<input id="email" type="text" placeholder="Email" class="form-control{{ $errors->has('email') ? ' is-invalid' : '' }}" name="email" value="{{ $email ?? old('email') }}" autocomplete="off">

											@if ($errors->has('email'))

											<span class="invalid-feedback"> <strong>{{ $errors->first('email') }}</strong> </span>

											@endif

										</div>

										<div class="form-group m-form__group">

											 <input id="password" type="password" class="form-control{{ $errors->has('password') ? ' is-invalid' : '' }}" placeholder="Password" name="password" required>

											@if ($errors->has('password'))
												<span class="invalid-feedback">
													<strong>{{ $errors->first('password') }}</strong>
												</span>
											@endif

										</div>

										<div class="form-group m-form__group">

											 <input id="password-confirm" type="password" class="form-control" placeholder="Confirm Password" name="password_confirmation" required>

											

										</div>

										

										<div class="m-login__form-action">

											<button type="submit" class="btn btn-accent">

												{{ __('Reset Password') }}

											</button>

										</div>

									</form>

								</div>



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