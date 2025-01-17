<!DOCTYPE html>

<!-- 
Template Name: Metronic - Responsive Admin Dashboard Template build with Twitter Bootstrap 4
Author: KeenThemes
Website: http://www.keenthemes.com/
Contact: support@keenthemes.com
Follow: www.twitter.com/keenthemes
Dribbble: www.dribbble.com/keenthemes
Like: www.facebook.com/keenthemes
Purchase: http://themeforest.net/item/metronic-responsive-admin-dashboard-template/4021469?ref=keenthemes
Renew Support: http://themeforest.net/item/metronic-responsive-admin-dashboard-template/4021469?ref=keenthemes
License: You must have a valid license purchased only from themeforest(the above link) in order to legally use the theme for your project.
-->
<html lang="en">

	<!-- begin::Head -->
	<head>
		<meta charset="utf-8" />
		<meta name="csrf-token" content="{{ csrf_token() }}">
		<title>Metronic | Dashboard</title>
		<meta name="description" content="Latest updates and statistic charts">
		<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no">

		<!--begin::Web font -->
		<script src="https://ajax.googleapis.com/ajax/libs/webfont/1.6.16/webfont.js"></script>
		<script type="text/javascript">
			WebFont.load({
				google: {
					"families": ["Poppins:300,400,500,600,700", "Roboto:300,400,500,600,700"]
				},
				active: function() {
					sessionStorage.fonts = true;
				}
			});
		</script>
		<script type="text/javascript">
			var site_url = "<?php echo URL::to('/'); ?>";	
			recipesRows 		= 	1;
			defaultRecipeImg 	= 	"{{ asset('images/default.png') }}";
		</script>
		
		<style>
			.loading_image {
				background-color: #fff;
				border-radius: 11px;
				box-shadow: 2px 2px 10px 2px #acacac;
				display: inline-block;
				margin: 17% auto;
				padding: 35px 20px;
				width: 115px;
				z-index: 5000999;
			}
			.valid {
				display: inline-block;
				position: relative;
				//text-align: center;
				width: 100%;
			}
			.ld {
				background: rgba(255, 255, 255, 0.5) none repeat scroll 0 0;
				float: left;
				min-height: 100%;
				position: fixed;
				text-align: center;
				width: 100%;
				z-index: 9999;
			}
			
			.table th a {
				color: #596d83 !important;
			}
		</style>

		<!--end::Web font -->

		<!--begin::Base Styles -->

		<link href="{{ URL::asset('assets/vendors/custom/jquery-ui/jquery-ui.bundle.css') }}" rel="stylesheet" type="text/css" />
		
		
		<!--begin::Page Vendors -->
		<link href="{{ URL::asset('assets/vendors/custom/fullcalendar/fullcalendar.bundle.css') }}" rel="stylesheet" type="text/css" />

		<!--RTL version:<link href="assets/vendors/custom/fullcalendar/fullcalendar.bundle.rtl.css" rel="stylesheet" type="text/css" />-->
		
		

		<!--end::Page Vendors -->
		<link href="{{ URL::asset('assets/vendors/base/vendors.bundle.css') }}" rel="stylesheet" type="text/css" />

		<!--RTL version:<link href="assets/vendors/base/vendors.bundle.rtl.css" rel="stylesheet" type="text/css" />-->
		<link href="{{ URL::asset('assets/demo/default/base/style.bundle.css') }}" rel="stylesheet" type="text/css" />

		<!--RTL version:<link href="assets/demo/default/base/style.bundle.rtl.css" rel="stylesheet" type="text/css" />-->
		
		<link rel="stylesheet" type="text/css" href="{{URL::asset('css/dev_style.css')}}" />
		<link rel="stylesheet" type="text/css" href="{{URL::asset('css/sidebox.css')}}" />
		<!--end::Base Styles -->
		<link rel="shortcut icon" href="{{ URL::asset('assets/demo/default/media/img/logo/favicon.ico') }}" />
		
		<script src="{{ URL::asset('assets//vendors/base/vendors.bundle.js') }}" type="text/javascript"></script>
		
		<script src="{{ URL::asset('assets/demo/default/base/scripts.bundle.js') }}" type="text/javascript"></script>
		
		
		<script src="{{ URL::asset('assets/vendors/custom/jquery-ui/jquery-ui.bundle.js') }}" type="text/javascript"></script>
		
		 <script src="{{ URL::asset('assets/demo/default/custom/components/portlets/draggable.js') }}" type="text/javascript"></script>
	</head>

	<!-- end::Head -->
	
	
	<!-- begin::Body -->
	<body class="m-page--fluid m--skin- m-content--skin-light2 m-header--fixed m-header--fixed-mobile m-aside-left--enabled m-aside-left--skin-dark m-aside-left--fixed m-aside-left--offcanvas m-footer--push m-aside--offcanvas-default">
	
		<div style="display:none" class="ld" id="loader_div">
			<div class="loading_image">
				<div class="valid"><img src="{!! asset('images/loader.gif') !!}" alt=""></div>
			</div>
		</div>
		
		<!-- begin:: Page -->
		<div class="m-grid m-grid--hor m-grid--root m-page">

			@include('../Elements/metronic_header')
        
			<!-- begin::Body -->
			<div class="m-grid__item m-grid__item--fluid m-grid m-grid--ver-desktop m-grid--desktop m-body">
				@include('../Elements/metronic_sidebar')
				
				
				
				@yield('content')
			</div>

			<!-- end:: Body -->
		
		
		@include('../Elements/metronic_footer')
		</div>
		@include('../Elements/metronic_quick_sidebar')
		<!-- end::Quick Sidebar -->

		<!-- begin::Scroll Top -->
		<div id="m_scroll_top" class="m-scroll-top">
			<i class="la la-arrow-up"></i>
		</div>

		<!-- end::Scroll Top -->
		
		<!-- begin::Quick Nav -->
		<ul class="m-nav-sticky" style="margin-top: 30px;">
			<li class="m-nav-sticky__item" data-toggle="m-tooltip" title="Purchase" data-placement="left">
				<a href="https://themeforest.net/item/metronic-responsive-admin-dashboard-template/4021469?ref=keenthemes" target="_blank">
					<i class="la la-cart-arrow-down"></i>
				</a>
			</li>
			<li class="m-nav-sticky__item" data-toggle="m-tooltip" title="Documentation" data-placement="left">
				<a href="https://keenthemes.com/metronic/documentation.html" target="_blank">
					<i class="la la-code-fork"></i>
				</a>
			</li>
			<li class="m-nav-sticky__item" data-toggle="m-tooltip" title="Support" data-placement="left">
				<a href="https://keenthemes.com/forums/forum/support/metronic5/" target="_blank">
					<i class="la la-life-ring"></i>
				</a>
			</li>
		</ul>

		<!-- begin::Quick Nav -->
		
		<!--begin::Base Scripts -->
		

		<!--end::Base Scripts -->

		<!--begin::Page Vendors -->
		<script src="{{ URL::asset('assets/vendors/custom/fullcalendar/fullcalendar.bundle.js') }}" type="text/javascript"></script>

		<!--end::Page Vendors -->

		<!--begin::Page Snippets -->
		<script src="{{ URL::asset('assets/app/js/dashboard.js') }}" type="text/javascript"></script>
		<script src="{{URL::asset('js/custom-validation.js')}}"></script>
		<!-- <script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.10.3/jquery-ui.min.js"></script> -->
		<script  src="{!! asset('js/custom-validation.js') !!}" type="text/javascript"></script>
		<script src="{{URL::asset('js/jquery.cookie.js')}}"></script>
		<script  src="{!! asset('js/custom.js') !!}" type="text/javascript"></script>
		<script  src="{!! asset('js/side_boxes.js') !!}" type="text/javascript"></script>

		<!--end::Page Snippets -->
		
		@if(isset($fetchedData->category) && isset($fetchedData->order_id))
			<script>
				$(window).on('load', function(){
					var category_id = 	"<?php echo $fetchedData->category ?>";
					var order_id 	= 	"<?php echo $fetchedData->order_id ?>";
					var subcategory	= 	"<?php echo $fetchedData->subcategory ?>";
					getRecipeSubcategory(category_id, order_id, subcategory);
				});
			</script>
		@endif

    </body>
	
</html>
