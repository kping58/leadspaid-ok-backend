@php
  $testing = auth()->guard('advertiser')->user()->id === 11?true:false;
@endphp
<style>
 .sidebar__menu .menu-title{font-size: 20px ; color: #fff}
.custom_side_bar {
    display: flex;
    flex-wrap: wrap;
    justify-content: space-between;
    align-items: center;
    align-content: space-between;
}
.sidebar__menu.footer-fix {
    margin-bottom: 60px;
}
.custom_side_bar ul li a {
    padding: 5px 13px !important;
}



    .sidebar__menu .menu-title{font-size: 20px ; color: #fff}
.custom_side_bar {
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    align-content: space-between;
    transition: none;
    height: 100% !important;
    transition:unset;
    position: relative;
}
.sidebar, .slimScrollDiv {
    transition:unset;
}
.sidebar__menu {
    margin-bottom: 0;
    position: absolute;
    left: 0;
    top: 0;

}
.sidebar__menu.footer-fix {
    margin-bottom: 60px;
    position: absolute;
    left: 0;
    top: auto;
    bottom: 0;
}
.custom_side_bar ul li a {
    padding: 5px 13px !important;
}



   
</style>
<div class="sidebar bg--" style="background-image: url('{{asset('assets/userpanel/images/sidebar/1.jpg')}}')">
    <button class="res-sidebar-close-btn"><i class="las la-times"></i></button>
    <button class="sidebar__expand"><i class="las la-plus"></i></button>
    <div class="sidebar__inner">
        <div class="sidebar__logo">
            <a href="{{route('advertiser.dashboard')}}" class="sidebar__main-logo"><img
                    src="{{getImage(imagePath()['logoIcon']['path'] .'/logo.png')}}" alt="image"></a>
            <button type="button" class="navbar__expand"></button>
        </div>

        <div class="sidebar__menu-wrapper custom_side_bar" id="sidebar__menuWrapper">
            <ul class="sidebar__menu">
            @if( $testing  )
                <li class="sidebar-menu-item {{menuActive('advertiser.dashboard')}}">
                    <a href="{{route('advertiser.dashboard')}}" class="nav-link ">
                        <i class="menu-icon las la-home text-white"></i>
                        <span class="menu-title text-white">@lang('Dashboard')</span>
                    </a>
                </li>
                @endif
                {{-- <li class="sidebar-menu-item sidebar-dropdown">
                    <a href="javascript:void(0)" class="{{menuActive('advertiser.ad*',3)}}">
                        <i class="menu-icon la las la-ad text-white"></i>
                        <span class="menu-title text-white">@lang('Advertisements')</span>
                    </a>
                    <div class="sidebar-submenu {{menuActive('advertiser.ad*',2)}} ">
                        <ul>

                            <li class="sidebar-menu-item {{menuActive('advertiser.ad.all')}}">
                                <a href="{{route('advertiser.ad.all')}}" class="nav-link">
                                    <i class="menu-icon las la-dot-circle text-white"></i>
                                    <span class="menu-title text-white">@lang('All Ads')</span>
                                </a>
                            </li>

                            <li class="sidebar-menu-item {{menuActive(['advertiser.ad.create','advertiser.ad.store'])}}">
                                <a href="{{route('advertiser.ad.create')}}" class="nav-link">
                                    <i class="menu-icon las la-dot-circle text-white"></i>
                                    <span class="menu-title text-white">@lang('Create Ad')</span>
                                </a>
                            </li>
                            <li class="sidebar-menu-item {{menuActive('advertiser.ad.report')}}">
                                <a href="{{route('advertiser.ad.report')}}" class="nav-link">
                                    <i class="menu-icon las la-dot-circle text-white"></i>
                                    <span class="menu-title text-white">@lang('Ad Reports')</span>
                                </a>
                            </li>

                        </ul>
                    </div>
                </li> --}}
                <li class="sidebar-menu-item  {{menuActive('advertiser.campaigns.index')}}">
                    <a href="{{route('advertiser.campaigns.index')}}" class="nav-link" >
                        <i class="menu-icon las la-money-bill text-white"></i>
                        <span class="menu-title text-white">@lang('Campaigns') </span>
                    </a>
                </li>

				@if( $testing  )
                    <li class="sidebar-menu-item  {{menuActive('advertiser.campaigns.style', 2)}}">
                        <a href="{{route('advertiser.campaigns.style', 2)}}" class="nav-link" >
                            <i class="menu-icon las la-money-bill text-white"></i>
                            <span class="menu-title text-white">@lang('Campaigns v2') </span>
                        </a>
                    </li>

                    <li class="sidebar-menu-item  {{menuActive('advertiser.campaigns.index_old')}}">
                        <a href="{{route('advertiser.campaigns.index_old')}}" class="nav-link" >
                            <i class="menu-icon las la-money-bill text-white"></i>
                            <span class="menu-title text-white">@lang('Campaigns Old') </span>
                        </a>
                    </li>
                @endif

                <li class="sidebar-menu-item  {{menuActive('advertiser.forms.index')}}">
                    <a href="{{route('advertiser.forms.index')}}" class="nav-link" >
                        <i class="menu-icon las la-money-bill text-white"></i>
                        <span class="menu-title text-white">@lang('Forms') </span>
                    </a>
                </li>

                {{-- <li class="sidebar-menu-item  {{menuActive('advertiser.price.plan')}}">
                    <a href="{{route('advertiser.price.plan')}}" class="nav-link"
                      >
                        <i class="menu-icon las la-money-bill text-white"></i>
                        <span class="menu-title text-white">@lang('Price Plans') </span>
                    </a>
                </li> --}}
                @if( $testing  )
                <li class="sidebar-menu-item sidebar-dropdown">
                    <a href="javascript:void(0)" class="{{menuActive('user.deposit*',3)}}">
                        <i class="menu-icon la la-list text-white"></i>
                        <span class="menu-title text-white">@lang('Deposit') </span>
                    </a>
                    <div class="sidebar-submenu {{menuActive('user.deposit*',2)}} ">
                        <ul>

                            <li class="sidebar-menu-item {{menuActive('user.deposit')}}">
                                <a href="{{route('user.deposit')}}" class="nav-link">
                                    <i class="menu-icon las la-dot-circle text-white"></i>
                                    <span class="menu-title text-white">@lang('Deposit Money')</span>
                                </a>
                            </li>
                            <li class="sidebar-menu-item {{menuActive('user.deposit.history')}}">
                                <a href="{{route('user.deposit.history')}}" class="nav-link">
                                    <i class="menu-icon las la-dot-circle text-white"></i>
                                    <span class="menu-title text-white">@lang('Deposit Log')</span>
                                </a>
                            </li>

                        </ul>
                    </div>
                </li>

                <li class="sidebar-menu-item {{menuActive('advertiser.trx.logs')}}">
                    <a href="{{route('advertiser.trx.logs')}}" class="nav-link">
                        <i class="menu-icon fas fa-exchange-alt text-white"></i>
                            <span class="menu-title text-white">@lang('Transaction Log')</span>
                        </a>
                    </li>
                <li class="sidebar-menu-item {{menuActive(['advertiser.day.logs','advertiser.day.search','advertiser.date.search'])}}">
                    <a href="{{route('advertiser.day.logs')}}" class="nav-link">
                        <i class="menu-icon las la-calendar-week text-white"></i>
                            <span class="menu-title text-white">@lang('Per Day Logs')</span>
                        </a>
                    </li>

                    <li class="sidebar-menu-item {{menuActive(['ticket','ticket.open','ticket.view'])}}">
                    <a href="{{route('ticket')}}" class="nav-link">
                        <i class="menu-icon las la-ticket-alt text-white"></i>
                            <span class="menu-title text-white">@lang('Support Ticket')</span>
                        </a>
                    </li>
                    <li class="sidebar-menu-item {{menuActive('advertiser.profile')}}">
                    <a href="{{route('advertiser.profile')}}" class="nav-link">
                        <i class="menu-icon las la-user-cog text-white"></i>
                            <span class="menu-title text-white">@lang('Profile Setting')</span>
                        </a>
                    </li>
                    <li class="sidebar-menu-item {{menuActive('advertiser.twofactor')}}">
                    <a href="{{route('advertiser.twofactor')}}" class="nav-link">
                        <i class="menu-icon las la-key text-white"></i>
                            <span class="menu-title text-white">@lang('2FA Security')</span>
                        </a>
                    </li>
                @endif
                    <li class="sidebar-menu-item {{menuActive('advertiser.payments')}}">
                    <a href="{{route('advertiser.payments')}}" class="nav-link">
                        <i class="menu-icon las la-credit-card text-white"></i>
                            <span class="menu-title text-white">@lang('Payments')</span>
                        </a>
                    </li>
                   

            </ul>

            <ul class="sidebar__menu footer-fix">
               <li class="sidebar-menu-item {{menuActive('advertiser.profile')}}">
                    <a href="{{route('advertiser.profile')}}" class="nav-link">
                        <i class="menu-icon dropdown-menu__icon las la-cog"></i>
                            <span class="menu-title text-white">@lang('Edit Profile')</span>
                        </a>
                    </li>
                     <li class="sidebar-menu-item {{menuActive('advertiser.password')}}">
                    <a href="{{route('advertiser.password')}}" class="nav-link">
                        <i class="menu-icon dropdown-menu__icon las la-key"></i>
                            <span class="menu-title text-white">@lang('Change Password')</span>
                        </a>
                    </li>

                    <li class="sidebar-menu-item {{menuActive('advertiser.logout')}}">
                    <a href="{{route('advertiser.logout')}}" class="nav-link">
                        <i class="menu-icon las la-sign-out-alt text-white"></i>
                            <span class="menu-title text-white">@lang('logout')</span>
                        </a>
                    </li>
            </ul>
        </div>
    </div>
</div>


<!-- sidebar end -->

<!-- Button trigger modal -->
<!-- Button trigger modal -->
