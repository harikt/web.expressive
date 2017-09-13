<?php /** @var \Dms\Core\Form\IFieldOption[] $options */ ?>
<?php /** @var array $value */ ?>
@if (count($value) === 0)
    @include('dms::components.field.null.value')
@else
    <ul class="dms-display-list list-group">
        @foreach ($value as $item)
            @if(isset($options[$item]))
                <li class="list-group-item">
                    @if($urlCallback ?? false)
                        <a href="{{ $urlCallback($options[$item]->getValue()) }}">{{ $options[$item]->getLabel() }}</a>
                    @else
                        {{ $options[$item]->getLabel() }}
                    @endif
                </li>
            @endif
        @endforeach
    </ul>
@endif