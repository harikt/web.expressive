<ol class="breadcrumb">
    @foreach($breadcrumbs as $url => $name)
        <li><a href="{{ $url }}">{{ $name }}</a></li>
    @endforeach
</ol>