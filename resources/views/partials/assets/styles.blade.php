@inject('config', 'Illuminate\Config\Repository')

@foreach (array_merge(['global'], $assetGroups ?? []) as $assetGroup)
    @foreach ($config->get('dms.front-end.' . $assetGroup . '.stylesheets') as $stylesheet)
        <link rel="stylesheet" href="{{ $stylesheet }}"/>
    @endforeach
@endforeach
