<!DOCTYPE html>
<html lang="en">
	<head>
		   @include('layout.partials.head')
	</head>
	<body  class="m-page--fluid m--skin- m-content--skin-light2 m-header--fixed m-header--fixed-mobile m-aside-left--enabled m-aside-left--skin-dark m-aside-left--offcanvas m-footer--push m-aside--offcanvas-default">
		<div class="m-grid m-grid--hor m-grid--root m-page">
			@include('layout.partials.header')
			<div class="m-grid__item m-grid__item--fluid m-grid m-grid--ver-desktop m-grid--desktop m-body">
				@if (Request::path() == 'login')
				@include('layout.partials.nav')
				@endif
				@yield('content')
			</div>
			@include('layout.partials.footer')
		</div>
		@include('layout.partials.footer-extra')
		@include('layout.partials.footer-scripts')
	</body>
</html>
