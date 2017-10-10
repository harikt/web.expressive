@foreach (array_merge(['global'], $assetGroups ?? []) as $assetGroup)
    @foreach (config('dms.front-end.' . $assetGroup . '.stylesheets') as $stylesheet)
        <link rel="stylesheet" href="{{ $stylesheet }}"/>
    @endforeach
@endforeach
