@inject('config', 'Illuminate\Config\Repository')
@inject('urlHelper', 'Zend\Expressive\Helper\UrlHelper')

@if($__content_only)
    @yield('content')
    <?php return ?>
@endif
@if($__no_template)
    <div id="page">
        <div class="title">{{ ($pageTitle ?? false) ? $pageTitle . ' :: ' : '' }}{{ $title }}</div>
        <div class="scripts">
            @include('dms::partials.assets.scripts')
        </div>
        <div class="styles">
            @include('dms::partials.assets.styles')
        </div>
        <div class="content">
            @include('dms::partials.content')
        </div>
    </div>
    <?php return ?>
@endif

@extends('dms::template.template')
<?php /** @var \Dms\Core\Auth\IAdmin $user */ ?>
<?php /** @var array $navigation */ ?>
@section('body-content')
    <body class="hold-transition skin-blue sidebar-mini">

    <div class="wrapper">

        <header class="main-header">
            <!-- Logo -->
            <a href="{{ $urlHelper->generate('dms::index') }}" class="logo">
                <!-- mini logo for sidebar mini 50x50 pixels -->
                <span class="logo-mini"><strong>DMS</strong></span>
                <!-- logo for regular state and mobile devices -->
                <span class="logo-lg"><strong>{{ $title }}</strong></span>
            </a>
            <!-- Header Navbar: style can be found in header.less -->
            <nav class="navbar navbar-static-top" role="navigation">
                <!-- Sidebar toggle button-->
                <a href="#" class="sidebar-toggle" data-toggle="offcanvas" role="button">
                    <span class="sr-only">Toggle navigation</span>
                </a>

                <div class="navbar-custom-menu">
                    <ul class="nav navbar-nav">
                        <!-- User Account: style can be found in dropdown.less -->
                        <li class="dropdown user user-menu">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                                <img src="https://www.gravatar.com/avatar/{{ md5($user->getEmailAddress()) }}" class="user-image" alt="User Image">
                                <span>{{ $user->getFullName() }}</span>
                            </a>
                            <ul class="dropdown-menu">
                                <!-- Menu Body-->
                                <li class="user-body">
                                    <a href="{{ $urlHelper->generate('dms::package.module.dashboard', ['package' => 'admin', 'module' => 'account']) }}">{{ $user->getUsername() }}</a>
                                </li>
                                <!-- Menu Footer-->
                                <li class="user-footer">
                                    <div class="pull-left">
                                        <a href="{{ $urlHelper->generate('dms::package.module.dashboard', ['package' => 'admin', 'module' => 'account']) }}" class="btn btn-default btn-flat">
                                            <i class="fa fa-cog"></i> Account
                                        </a>
                                    </div>
                                    <div class="pull-right">
                                        <a href="{{ $urlHelper->generate('dms::auth.logout') }}" class="btn btn-default btn-flat">
                                            <i class="fa fa-sign-out"></i> Log out
                                        </a>
                                    </div>
                                </li>
                            </ul>
                        </li>
                    </ul>
                </div>
            </nav>
        </header>
        <!-- Left side column. contains the logo and sidebar -->
        <aside class="main-sidebar">
            <section class="sidebar">
                <!-- Sidebar user panel -->
                <div class="user-panel">
                    <div class="pull-left image">
                        <img src="https://www.gravatar.com/avatar/{{-- md5($user->getEmailAddress()) --}}" class="img-circle" alt="User Image">
                    </div>
                    <div class="pull-left info">
                        <p>{{ $user->getFullName() }}</p>
                        <a href="{{ $urlHelper->generate('dms::package.module.dashboard', ['package' => 'admin', 'module' => 'account']) }}">
                            <i class="fa fa-circle text-success"></i> {{-- $user->getEmailAddress() --}}
                        </a>
                    </div>
                </div>
                <!-- search form -->
                <div class="sidebar-form">
                    <div class="input-group">
                        <input type="text" class="form-control dms-nav-quick-filter" placeholder="Search packages...">
                        <span class="input-group-btn">
                            <button type="submit" name="search" id="search-btn" class="btn btn-flat">
                                <i class="fa fa-search"></i>
                            </button>
                        </span>
                    </div>
                </div>
                <!-- /.search form -->
                <!-- sidebar menu: : style can be found in sidebar.less -->
                <ul class="sidebar-menu dms-packages-nav">
                    <li class="header">INSTALLED PACKAGES</li>
                    <li class="active treeview">
                    @foreach($navigation as $element)
                        @if($element instanceof \Dms\Web\Expressive\View\NavigationElementGroup)
                            <li class="treeview  @if(starts_with($requestUri, $element->getAllUrls())) active @endif">
                                <a href="javascript:void(0)">
                                    <i class="fa fa-{{ $element->getIcon() }}"></i>
                                    <span class="dms-nav-label dms-nav-label-group">{{ $element->getLabel() }}</span>
                                    <i class="fa fa-angle-left pull-right"></i>
                                </a>
                                <ul class="treeview-menu">
                                    @foreach($element->getElements() as $innerElement)
                                        <li @if(starts_with($requestUri, $innerElement->getUrl())) class="active" @endif>
                                            <a href="{{ $serverUrlHelper->generate($innerElement->getUrl()) }}">
                                                <i class="fa fa-{{ $innerElement->getIcon() }}"></i>
                                                <span class="dms-nav-label">{{ $innerElement->getLabel() }}</span>
                                            </a>
                                        </li>
                                    @endforeach
                                </ul>
                            </li>
                        @elseif ($element instanceof \Dms\Web\Expressive\View\NavigationElement)
                            <li @if($element->getUrl() === $urlHelper->generate('dms::index') ? $element->getUrl() === $requestUri : starts_with($requestUri, $element->getUrl())) class="active" @endif>
                                <a href="{{ $serverUrlHelper->generate($element->getUrl()) }}">
                                    <i class="fa fa-{{ $element->getIcon() }}"></i>
                                    <span class="dms-nav-label">{{ $element->getLabel() }}</span>
                                    <i class="fa fa-angle-left pull-right"></i>
                                </a>
                            </li>
                        @endif
                    @endforeach
                </ul>
            </section>
            <!-- /.sidebar -->
        </aside>

        @include('dms::partials.alerts')

                <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper">
            <div class="dms-page-content">
                @include('dms::partials.content')
            </div>

            @include('dms::partials.spinner')
            @include('dms::partials.dialog.login')
            @include('dms::partials.dialog.content')
            @include('dms::partials.dialog.error')
        </div>

        <!-- /.content-wrapper -->
        <footer class="main-footer">
            <div class="pull-right hidden-xs">
                <b>Version</b> {{ \Dms\Core\ICms::VERSION }}
            </div>
            <span>
                For issues or enquiries please contact
                <a href="{{ $config->get('dms.contact.website') }}">{{ $config->get('dms.contact.company') }}</a>.
            </span>
        </footer>
    </div>
    </body>
@endsection
