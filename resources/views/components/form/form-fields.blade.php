<?php /** @var string[][] $groupedFields */ ?>
<div
        class="dms-form-fields"
        @if ($equalFields ?? false) data-equal-fields="{{ json_encode($equalFields) }}" @endif
        @if ($greaterThanFields ?? false) data-greater-than-fields="{{ json_encode($greaterThanFields) }}" @endif
        @if ($greaterThanOrEqualFields ?? false) data-greater-than-or-eqaul-fields="{{ json_encode($greaterThanOrEqualFields) }}" @endif
        @if ($lessThanFields ?? false) data-less-than-fields="{{ json_encode($lessThanFields) }}" @endif
        @if ($lessThanOrEqualFields ?? false) data-less-than-or-equal-fields="{{ json_encode($lessThanOrEqualFields) }}" @endif
>
    @foreach($groupedFields as $groupTitle => $fields)
        <fieldset class="dms-form-fieldset @if(count(array_filter(array_column($fields, 'hidden'))) === count($fields)) hidden @endif">
            @if($groupTitle !== '')
                <legend>{{ $groupTitle }}</legend>@endif
            @foreach($fields as $label => $field)
                <div class="form-group clearfix{{ ($field['hidden'] ?? false) ? ' hidden' : '' }}" data-field-name="{{ $field['name'] }}">
                    @if($field['withoutLabel'] ?? false)
                        <div class="col-sm-12">
                            {!! $field['content'] !!}
                        </div>
                    @else
                        <div class="dms-label-container col-lg-2 col-md-3 col-sm-4">
                            <label data-for="{{ $field['name'] }}">{{ $label }}</label>
                        </div>
                        <div class="col-lg-10 col-md-9 col-sm-8">
                            {!! $field['content'] !!}
                            <div class="dms-validation-messages-container"></div>
                            @if($field['helpText'] ?? false)
                                <p class="help-block">{{ $field['helpText'] }}</p>
                            @endif
                        </div>
                    @endif
                </div>
            @endforeach
        </fieldset>
    @endforeach
</div>