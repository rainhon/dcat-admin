
{!! admin_section(Dcat\Admin\Admin::SECTION['NAVBAR_BEFORE']) !!}

<nav class="header-navbar navbar-expand-lg navbar
    navbar-with-menu {{ $configData['navbar_class'] }}
    {{ $configData['navbar_color'] }}
        navbar-light navbar-shadow " style="top: 0;">

    <div class="navbar-wrapper">
        <div class="navbar-container content">

            <div class="navbar-collapse d-flex justify-content-between">
                <div class="navbar-left d-flex align-items-center">
                    {!! Dcat\Admin\Admin::navbar()->render('left') !!}
                </div>

                @if($configData['horizontal_menu'])
                <div class="d-md-block horizontal-navbar-brand justify-content-center text-center">
                    <ul class="nav navbar-nav flex-row">
                        <li class="nav-item mr-auto">
                            <a href="{{ admin_url('/') }}" class="waves-effect waves-light" data-no-pjax="1">
                                <span class="logo-lg">{!! config('admin.logo') !!}</span>
                            </a>
                        </li>
                    </ul>
                </div>
                @endif

                <div class="d-md-block horizontal-navbar-brand justify-content-center text-center flex-grow-1">
                    <ul class="nav navbar-nav flex-row justify-content-around">
                        {!! admin_section(Dcat\Admin\Admin::SECTION['NAVBAR_MENU']) !!}
                    </ul>
                </div>

                <div class="navbar-right d-flex align-items-center">
                    {!! Dcat\Admin\Admin::navbar()->render() !!}

                    <ul class="nav navbar-nav">
                        {{--User Account Menu--}}
                        {!! admin_section(Dcat\Admin\Admin::SECTION['NAVBAR_USER_PANEL']) !!}

                        {!! admin_section(Dcat\Admin\Admin::SECTION['NAVBAR_AFTER_USER_PANEL']) !!}
                    </ul>
                </div>
            </div>
        </div>
    </div>
</nav>

{!! admin_section(Dcat\Admin\Admin::SECTION['NAVBAR_AFTER']) !!}