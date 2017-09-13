@foreach (array_merge(['global'], $assetGroups ?? []) as $assetGroup)
    @foreach (config('dms.front-end.' . $assetGroup . '.scripts') as $script)
        <script src="{{ asset($script) }}"></script>
    @endforeach
@endforeach
