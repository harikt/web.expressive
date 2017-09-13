<span class="dms-display-string">
    @if (!empty($url))
        <a href="{{ $url }}">{{ $value }}</a>
    @elseif (!isset($type) || $type === 'text')
        {{ $value }}
    @elseif ($type === 'url')
        <a href="{{ $value }}">{{ $value }}</a>
    @elseif ($type === 'ip-address')
        <a href="http://{{ $value }}">{{ $value }}</a>
    @elseif ($type === 'email')
        <a href="mailto:{{ $value }}">{{ $value }}</a>
    @elseif ($type === 'password')
        <span class="dms-password-display hidden">{{ $value }}</span>
    @else
        {{ $value }}
    @endif
</span>