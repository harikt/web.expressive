@inject('urlHelper', 'Zend\Expressive\Helper\UrlHelper')

<section class="content-header">
    <h1>
        {{ $pageTitle }}
        @if (!empty($pageSubtitle))
            <small>{{ $pageSubtitle }}</small>
        @endif
    </h1>
    <ol class="breadcrumb">
        @if (!empty($breadcrumbs))
            @foreach ($breadcrumbs as $link => $label)
                <li>
                    <a href="{{ $link }}">
                        @if ($link === $urlHelper->generate('dms::index')) <i class="fa fa-dashboard"></i> @endif {{ $label }}
                    </a>
                </li>
            @endforeach
        @endif
        <li class="active">{{ $finalBreadcrumb }} </li>
    </ol>
</section>

<section class="content">
    @yield('content')
</section>
