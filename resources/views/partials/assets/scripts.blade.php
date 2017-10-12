@inject('config', 'Illuminate\Config\Repository')

@foreach (array_merge(['global'], $assetGroups ?? []) as $assetGroup)
    @foreach ($config->get('dms.front-end.' . $assetGroup . '.scripts') as $script)
        <script src="{{ $script }}"></script>
    @endforeach
@endforeach
