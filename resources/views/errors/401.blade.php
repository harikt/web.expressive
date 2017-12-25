@inject('urlHelper', 'Zend\Expressive\Helper\UrlHelper')

@extends('dms::template.error')

@section('content')
    <section class="content">
        <div class="error-page">
            <h2 class="headline text-red"> 401</h2>

            <div class="error-content">
                <h3><i class="fa fa-warning text-yellow"></i> Oops! Unauthorized.</h3>

                <p>
                    You are not authorized to view this page.
                    Meanwhile, you may <a href="{{ $serverUrlHelper->generate($urlHelper->generate('dms::index')) }}">return to the dashboard</a>.
                </p>
            </div>
            <!-- /.error-content -->
        </div>
        <!-- /.error-page -->
    </section>
@endsection
