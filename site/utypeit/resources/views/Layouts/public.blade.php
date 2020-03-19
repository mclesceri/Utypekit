<!DOCTYPE html>
<html>
    <head>
		<title>@yield('title')</title>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
        
		<meta name="description" content="Latest updates and statistic charts">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
		<script src="https://ajax.googleapis.com/ajax/libs/webfont/1.6.16/webfont.js"></script>
		<script>
		  WebFont.load({
			google: {"families":["Poppins:300,400,500,600,700","Roboto:300,400,500,600,700"]},
			active: function() {
				sessionStorage.fonts = true;
			}
		  });
		</script>
		
		<link href="{{ URL::asset('assets/vendors/custom/fullcalendar/fullcalendar.bundle.css') }}" rel="stylesheet" type="text/css" />
		<link href="{{ URL::asset('assets/vendors/base/vendors.bundle.css') }}" rel="stylesheet" type="text/css" />
		<link href="{{ URL::asset('assets/demo/default/base/style.bundle.css') }}" rel="stylesheet" type="text/css" />
		<link href="{{ URL::asset('public/css/theme.css') }}" rel="stylesheet" type="text/css" />
		<link rel="shortcut icon" href="{{ URL::asset('assets/demo/default/media/img/logo/favicon.ico') }}" />

		<link rel="stylesheet" type="text/css" href="{{URL::asset('css/style.css')}}" />
		<link rel="stylesheet" type="text/css" href="{{URL::asset('css/bootstrap.min.css')}}" />
		
		<link href="http://cookbookpublishers.com/utypeit2/media/css/reset.css" rel="stylesheet" type="text/css" />
		<link href="http://cookbookpublishers.com/utypeit2/webfonts/css/webfonts.css" rel="stylesheet" type="text/css" />
		<link href="http://cookbookpublishers.com/utypeit2/media/css/colors.css" rel="stylesheet" type="text/css" />
		<link href="http://cookbookpublishers.com/utypeit2/media/css/calendarview.css" rel="stylesheet" type="text/css" />
		<link href="http://cookbookpublishers.com/utypeit2/media/css/lightwindow.css" rel="stylesheet" type="text/css" />
		<link href="http://cookbookpublishers.com/utypeit2/media/css/wizard.css" rel="stylesheet" type="text/css" />
		
		<!-- All JS Start -->
			<script src="{{URL::asset('js/jquery.min.js')}}"></script> 
			<script src="{{URL::asset('js/popper.min.js')}}"></script> 
			<script src="{{URL::asset('js/bootstrap.min.js')}}"></script> 
			<script src="{{URL::asset('js/custom-validation.js')}}"></script>
			<script src="{{URL::asset('js/jquery.cookie.js')}}"></script>
			<script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.10.3/jquery-ui.min.js"></script>
			<script type="text/javascript" src="{!! asset('js/ckeditor/ckeditor.js') !!}"></script>
			<script type="text/javascript" src="{!! asset('js/custom.js') !!}"></script>
			
			
		<!-- All JS End -->
		<script>
			recipesRows 		= 	1;
			defaultRecipeImg 	= 	"{{ asset('images/default.png') }}";
		</script>
	</head>
    <body>
		<div id="headerWrap">
			@include('../Elements/header')
        </div>
		@include('../Elements/flash-message')
		
		@yield('content')
		<div id="base">
			@include('../Elements/footer')
		</div>
    </body>
</html>
