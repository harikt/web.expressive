<div class="dms-display-inner-module"
     data-root-url="{{ $rootUrl }}"
     data-display-only="1"
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
</div>