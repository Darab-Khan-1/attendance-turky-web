<!--end::Aside-->
<!--begin::Wrapper-->
<div class="d-flex flex-column flex-row-fluid wrapper" id="kt_wrapper">
    <!--begin::Header-->
    <div id="kt_header" class="header header-fixed">
        <!--begin::Container-->
        <div class="container-fluid d-flex align-items-stretch justify-content-between">
            <!--begin::Header Menu Wrapper-->
            <div class="header-menu-wrapper header-menu-wrapper-left" id="kt_header_menu_wrapper">
                <!--begin::Header Menu-->
                <div id="kt_header_menu" class="header-menu header-menu-mobile header-menu-layout-default">
                    <!--begin::Header Nav-->
                    {{-- <ul class="menu-nav">
                        <li class="menu-item menu-item-submenu menu-item-rel "  aria-haspopup="true">
                            <a href="{{ url('/dashboard') }}" class="menu-link menu-toggle">
                                <span class="menu-text">{{__('messages.home')}}</span>
                                <i class="menu-arrow"></i>
                            </a>
                        </li>
                        </ul> --}}
                    <!--end::Header Nav-->
                </div>
                <!--end::Header Menu-->
            </div>
            <!--end::Header Menu Wrapper-->
            <!--begin::Topbar-->
            <div class="topbar">
                <!--begin::User-->
                <a class="topbar-item" href="{{ url('/profile/personal') }}">
                    <div class="btn btn-icon w-auto btn-clean d-flex align-items-center btn-lg px-2">
                        <span
                            class="text-muted font-weight-bold font-size-base d-none d-md-inline mr-1">Hi,</span>
                        <span
                            class="text-dark-50 font-weight-bolder font-size-base d-none d-md-inline mr-3">Admin</span>
                        <span class="symbol symbol-35 ">
                            <span class="symbol-label font-size-h5 font-weight-bold text-white"
                                style="background:#6b869b"><i class="fa fa-user"></i></span>
                        </span>
                    </div>
                </a>
                <a href="{{ url('/logout') }}" class="btn  m-auto font-weight-bold text-white"
                    style="background:#bea19c">Sign Out</a>
                <!--end::User-->
            </div>
            <!--end::Topbar-->
        </div>
        <!--end::Container-->
    </div>
