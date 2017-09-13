<div
        class="dms-inner-module"
        data-name="{{ $name }}"
        data-field-validation-for="{{ $name }}"
        data-root-url="{{ $rootUrl }}"
        @if($required) data-required="1" @endif
        @if($readonly) data-readonly="1" @endif
        @if($value !== null) data-value="{{ json_encode($value) }}" @endif
>
    <div class="dms-inner-module-panel panel panel-default">
        <div class="panel-body">
            {!! $moduleContent !!}

            <div class="dms-inner-module-form-container">
                <div class="dms-inner-module-form"></div>
                @include('dms::partials.spinner')
            </div>
        </div>
    </div>

    @include('dms::partials.spinner')
</div>