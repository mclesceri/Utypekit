<!-- BEGIN: Left Aside -->
<button class="m-aside-left-close  m-aside-left-close--skin-dark " id="m_aside_left_close_btn">
	<i class="la la-close"></i>
</button>
<div id="m_aside_left" class="m-grid__item	m-aside-left  m-aside-left--skin-dark " style="margin-top: -5px">
	<!-- BEGIN: Aside Menu -->
	<div class="m-stack m-stack--ver m-stack--general">
		<div class="m-stack__item m-stack__item--middle m-brand__logo" style="">
			<a href="index.html" class="m-brand__logo-wrapper"> <img alt="" src="images/cookbook-logo-new2.png"/> </a>
		</div>
		<div class="m-stack__item m-stack__item--middle m-brand__tools">
			<!-- BEGIN: Left Aside Minimize Toggle -->
			<a href="javascript:;" id="m_aside_left_minimize_toggle" class="m-brand__icon m-brand__toggler m-brand__toggler--left m--visible-desktop-inline-block"> <span></span> </a>
			<!-- END -->
			<!-- BEGIN: Responsive Aside Left Menu Toggler -->
			<a href="javascript:;" id="m_aside_left_offcanvas_toggle" class="m-brand__icon m-brand__toggler m-brand__toggler--left m--visible-tablet-and-mobile-inline-block"> <span></span> </a>
			<!-- END -->
			<!-- BEGIN: Responsive Header Menu Toggler -->
			<a id="m_aside_header_menu_mobile_toggle" href="javascript:;" class="m-brand__icon m-brand__toggler m--visible-tablet-and-mobile-inline-block"> <span></span> </a>
			<!-- END -->
			<!-- BEGIN: Topbar Toggler -->
			<a id="m_aside_header_topbar_mobile_toggle" href="javascript:;" class="m-brand__icon m--visible-tablet-and-mobile-inline-block"> <i class="flaticon-more"></i> </a>
			<!-- BEGIN: Topbar Toggler -->
		</div>

		@if (Auth::guest())
		@else
		<div id="m_ver_menu" class="m-aside-menu  m-aside-menu--skin-dark m-aside-menu--submenu-skin-dark "	m-menu-vertical="1" m-menu-scrollable="0" m-menu-dropdown-timeout="500">
			<ul class="m-menu__nav  m-menu__nav--dropdown-submenu-arrow ">
				<li class="m-menu__item  m-menu__item--active text-md-center" aria-haspopup="true" >
					Welcome Back {{ Auth::user()->name }},
					<br/>
					<a href="{{ route('logout') }}">Logout</a>
				</li>
				<li class="m-menu__item  m-menu__item--active" aria-haspopup="true" >
					<a  href="index.html" class="m-menu__link "> <i class="m-menu__link-icon flaticon-line-graph"></i> <span class="m-menu__link-title"> <span class="m-menu__link-wrap"> <span class="m-menu__link-text"> Dashboard </span> <span class="m-menu__link-badge"> <span class="m-badge m-badge--danger"> 2 </span> </span> </span> </span> </a>
				</li>
				
				<!--
				<li class="m-menu__section ">
					<h4 class="m-menu__section-text"> Components </h4>
					<i class="m-menu__section-icon flaticon-more-v3"></i>
				</li>

				<li class="m-menu__item  m-menu__item--submenu" aria-haspopup="true"  m-menu-submenu-toggle="hover">
					<a  href="javascript:;" class="m-menu__link m-menu__toggle"> <i class="m-menu__link-icon flaticon-layers"></i> <span class="m-menu__link-text"> Base </span> <i class="m-menu__ver-arrow la la-angle-right"></i> </a>

					<div class="m-menu__submenu ">
						<span class="m-menu__arrow"></span>
						<ul class="m-menu__subnav">
							<li class="m-menu__item  m-menu__item--parent" aria-haspopup="true" >
								<span class="m-menu__link"> <span class="m-menu__link-text"> Base </span> </span>
							</li>

							<li class="m-menu__item " aria-haspopup="true" >
								<a  href="components/base/state.html" class="m-menu__link "> <i class="m-menu__link-bullet m-menu__link-bullet--dot"> <span></span> </i> <span class="m-menu__link-text"> State Colors </span> </a>
							</li>

						</ul>
					</div>
				</li>
				-->
			</ul>
		</div>
		@endif
	</div>
	<!-- END: Aside Menu -->
</div>
