<!DOCTYPE html>
<html lang="zxx" class="js">

<head>
    <base href="../">
    <meta charset="utf-8">
    <meta name="author" content="Softnio">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="Dashboard System">
    <!-- Fav Icon  -->
    <link rel="shortcut icon" href="./images/favicon.png">
    <!-- Page Title  -->
    <title>Dashboard System</title>
    <!-- StyleSheets  -->

    <link rel="stylesheet" href="{{ url('assets/css/dashlite.css') }}?ver=3.0.6">
    <link id="skin-default" rel="stylesheet" href="{{ url('assets/css/theme.css') }}?ver=3.0.8">
    <link rel="stylesheet" href="{{ url('assets/css/editors/summernote.css') }}?ver=3.0.0">

    <!-- Resources -->
    <script src="https://cdn.amcharts.com/lib/5/index.js"></script>
    <script src="https://cdn.amcharts.com/lib/5/percent.js"></script>
    <script src="https://cdn.amcharts.com/lib/5/themes/Animated.js"></script>
    <script src="https://cdn.amcharts.com/lib/5/xy.js"></script>

    {{-- JQUERY --}}
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"
        integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>

</head>

<body class="nk-body bg-lighter ">
    <div class="nk-app-root">
        <!-- wrap @s -->
        <div class="nk-wrap ">
            <!-- main header @s -->
            <div class="nk-header is-light">
                <div class="container-fluid">
                    <div class="nk-header-wrap">
                        <div class="nk-menu-trigger me-sm-2 d-lg-none">
                            <a href="#" class="nk-nav-toggle nk-quick-nav-icon" data-target="headerNav"><em
                                    class="icon ni ni-menu"></em></a>
                        </div>
                        <div class="nk-header-brand">
                            <a href="/" class="logo-link">
                                <img class="logo-light logo-img"
                                    src="https://demo.satpamku.co.id/assets/images/logo.png"
                                    srcset="https://demo.satpamku.co.id/assets/images/logo.png 2x" alt="logo">
                                <img class="logo-dark logo-img" src="https://demo.satpamku.co.id/assets/images/logo.png"
                                    srcset="https://demo.satpamku.co.id/assets/images/logo.png 2x" alt="logo-dark">
                            </a>
                        </div><!-- .nk-header-brand -->
                        <div class="nk-header-menu ms-auto" data-content="headerNav">
                            <div class="nk-header-mobile">
                                <div class="nk-header-brand">
                                    <a href="/" class="logo-link">
                                        <img class="logo-light logo-img"
                                            src="https://demo.satpamku.co.id/assets/images/logo.png"
                                            srcset="https://demo.satpamku.co.id/assets/images/logo.png 2x"
                                            alt="logo">
                                        <img class="logo-dark logo-img"
                                            src="https://demo.satpamku.co.id/assets/images/logo.png"
                                            srcset="https://demo.satpamku.co.id/assets/images/logo.png 2x"
                                            alt="logo-dark">
                                    </a>
                                </div>
                                <div class="nk-menu-trigger me-n2">
                                    <a href="#" class="nk-nav-toggle nk-quick-nav-icon"
                                        data-target="headerNav"><em class="icon ni ni-arrow-left"></em></a>
                                </div>
                            </div>
                            <ul class="nk-menu nk-menu-main ui-s2">
                                <li class="nk-menu-item active">
                                    <a href="/" class="nk-menu-link">
                                        <span class="nk-menu-text">Dashboard</span>
                                    </a>
                                </li>
                                <li class="nk-menu-item">
                                    <a href="{{ url('check/1') }}" class="nk-menu-link">
                                        <span class="nk-menu-text">Outlet</span>
                                    </a>
                                </li>
                            </ul><!-- .nk-menu -->
                        </div><!-- .nk-header-menu -->
                        <div class="nk-header-tools">
                            <ul class="nk-quick-nav">

                                <li class="dropdown user-dropdown">
                                    <a href="#" class="dropdown-toggle" data-bs-toggle="dropdown">
                                        <div class="user-toggle">
                                            <div class="user-avatar sm">
                                                <em class="icon ni ni-user-alt"></em>
                                            </div>
                                        </div>
                                    </a>
                                    <div
                                        class="dropdown-menu dropdown-menu-md dropdown-menu-end dropdown-menu-s1 is-light">
                                        <div class="dropdown-inner user-card-wrap bg-lighter d-none d-md-block">
                                            <div class="user-card">
                                                <div class="user-avatar">
                                                    <span>AB</span>
                                                </div>
                                                <div class="user-info">
                                                    <span class="lead-text">Admin</span>
                                                    <span class="sub-text">admin@gmail.com</span>
                                                </div>
                                                <div class="user-action">
                                                    <a class="btn btn-icon me-n2"
                                                        href="html/user-profile-setting.html"><em
                                                            class="icon ni ni-setting"></em></a>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="dropdown-inner">
                                            <ul class="link-list">
                                                <li><a href="html/user-profile-setting.html"><em
                                                            class="icon ni ni-setting-alt"></em><span>Account
                                                            Setting</span></a></li>
                                                <li><a href="html/user-profile-activity.html"><em
                                                            class="icon ni ni-activity-alt"></em><span>Login
                                                            Activity</span></a></li>
                                            </ul>
                                        </div>
                                        <div class="dropdown-inner">
                                            <ul class="link-list">
                                                <li><a href="#"><em class="icon ni ni-signout"></em><span>Sign
                                                            out</span></a></li>
                                            </ul>
                                        </div>
                                    </div>
                                </li><!-- .dropdown -->
                            </ul><!-- .nk-quick-nav -->
                        </div><!-- .nk-header-tools -->
                    </div><!-- .nk-header-wrap -->
                </div><!-- .container-fliud -->
            </div>
            <!-- main header @e -->
            <!-- content @s -->
            <div class="nk-content ">
                <div class="container-fluid">
                    <div class="nk-content-inner">
                        <div class="nk-content-body">
                            @yield('content')
                        </div>
                    </div>
                </div>
            </div>
            <!-- content @e -->
            <!-- footer @s -->
            <div class="nk-footer bg-white">
                <div class="container-fluid">
                    <div class="nk-footer-wrap">
                        <div class="nk-footer-copyright">Copyright &copy; 2022<a href="#"> By Service Quality
                                Division</a>
                        </div>
                        <div class="nk-footer-links">
                            <ul class="nav nav-sm">

                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            <!-- footer @e -->
        </div>
        <!-- wrap @e -->
    </div>
    <!-- app-root @e -->


    @yield('script')
</body>

</html>
