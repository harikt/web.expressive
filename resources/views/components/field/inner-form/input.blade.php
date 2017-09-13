<div
        class="dms-inner-form"
        data-name="{{ $name }}"
        @if($required) data-required="1" @endif
        @if($readonly) data-readonly="1" @endif
>
        {!! $formContent !!}
</div>