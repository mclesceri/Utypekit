<header id="m_header" class="m-grid__item    mheader @if (Request::path() == 'login') loginheader @endif"  m-minimize-offset="200" m-minimize-mobile-offset="200" >
	<div class="m-container m-container--fluid m-container--full-height">
		<div class="m-stack m-stack--ver m-stack--desktop">
			@if (Request::path() == 'login')
			<div class="m-stack__item m-brand  m-brand--skin-dark ">
			</div>
			<div class="m-stack__item m-stack__item--fluid m-header-head text-md-right" id="m_header_nav">
				<div class="rightheader"><img src="images/utypeit_logo.png" style="max-height: 30px;">
				</div>
			</div>
			@else
			<div class="m-stack__item m-stack__item--fluid m-header-head " id="m_header_nav" style="background: #9db75f;">
				<div class="rightheader container">
					<div class="row justify-content-center">
						<div class="col-sm-6"><img src="/images/cookbook-logo-new2.png"></div><div class="col-sm-6 text-md-right rightlogo"><img src="images/utypeit_logo.png" class="text-md-right"></div>
					</div>
				</div>
			</div>
			@endif
		</div>
	</div>
</header>

<!-- END: Header -->