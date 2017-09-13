<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines contain the default error messages used by
    | the validator class. Some of these rules have multiple versions such
    | as the size rules. Feel free to tweak each of these messages here.
    |
    */

    'accepted'                  => 'The :field must be accepted.',
    'active_url'                => 'The :field is not a valid URL.',
    'after'                     => 'The :field must be a date after :date.',
    'alpha'                     => 'The :field may only contain letters.',
    'alpha_dash'                => 'The :field may only contain letters, numbers, and dashes.',
    'alpha_num'                 => 'The :field may only contain letters and numbers.',
    'array'                     => 'The :field must be an array.',
    'array-contains-duplicates' => 'The :field cannot contain duplicate values',
    'before'                    => 'The :field must be a date before :date.',
    'between'                   => [
        'numeric' => 'The :field must be between :min and :max.',
        'file'    => 'The :field must be between :min and :max kilobytes.',
        'string'  => 'The :field must be between :min and :max characters.',
        'array'   => 'The :field must have between :min and :max items.',
    ],
    'boolean'                   => 'The :field field must be true or false.',
    'characters'                => 'The :field can only contain the :valid_chars characters',
    'colour'                    => [
        'rgb'  => 'The :field must be a valid RGB colour.',
        'rgba' => 'The :field must be a valid RGBA colour.',
    ],
    'confirmed'                 => 'The :field confirmation does not match.',
    'date'                      => 'The :field is not a valid date.',
    'date_format'               => 'The :field does not match the format :format.',
    'date-format'               => 'The :field does not match the format :format.',
    'different'                 => 'The :field and :other must be different.',
    'digits'                    => 'The :field must be :digits digits.',
    'digits_between'            => 'The :field must be between :min and :max digits.',
    'email'                     => 'The :field must be a valid email address.',
    'exists'                    => 'The selected :field is invalid.',
    'filled'                    => 'The :field field is required.',
    'file-extensions'           => 'The :field field only allows files with the :extensions extension',
    'image'                     => 'The :field must be an image.',
    'in'                        => 'The selected :field is invalid.',
    'object-id'                 => 'The selected :field is invalid.',
    'invalid-object'            => 'The requested operation cannot be executed.',
    'int'                       => 'The :field must be a whole number.',
    'integer'                   => 'The :field must be an integer.',
    'ip-address'                => 'The :field must be a valid IP address.',
    'json'                      => 'The :field must be a valid JSON string.',
    'max'                       => [
        'numeric' => 'The :field may not be greater than :max.',
        'file'    => 'The :field may not be greater than :max kilobytes.',
        'string'  => 'The :field may not be greater than :max characters.',
        'array'   => 'The :field may not have more than :max items.',
    ],
    'mimes'                     => 'The :field must be a file of type: :values.',
    'min'                       => [
        'numeric' => 'The :field must be at least :min.',
        'file'    => 'The :field must be at least :min kilobytes.',
        'string'  => 'The :field must be at least :min characters.',
        'array'   => 'The :field must have at least :min items.',
    ],
    'not_in'                    => 'The selected :field is invalid.',
    'numeric'                   => 'The :field must be a number.',
    'regex'                     => 'The :field format is invalid.',
    'required'                  => 'The :field field is required.',
    'required_if'               => 'The :field field is required when :other is :value.',
    'required_unless'           => 'The :field field is required unless :other is in :values.',
    'required_with'             => 'The :field field is required when :values is present.',
    'required_with_all'         => 'The :field field is required when :values is present.',
    'required_without'          => 'The :field field is required when :values is not present.',
    'required_without_all'      => 'The :field field is required when none of :values are present.',
    'same'                      => 'The :field and :other must match.',
    'size'                      => [
        'numeric' => 'The :field must be :size.',
        'file'    => 'The :field must be :size kilobytes.',
        'string'  => 'The :field must be :size characters.',
        'array'   => 'The :field must contain :size items.',
    ],
    'string'                    => 'The :field must be a string.',
    'timezone'                  => 'The :field must be a valid zone.',
    'type'                      => 'The :field must be of a :type type, :input given',
    'unique'                    => 'The :field has already been taken.',
    'url'                       => 'The :field format is invalid.',
    'matching-fields'           => 'The \':field1\' and \':field2\' fields must match.',
    'exact-array-length'        => 'The :field must contain exactly :length items',
    'one-of'                    => 'The :field must be one of :options',
    'table'                     => [
        'invalid-structure' => 'The :field does not contain a valid table structure',
    ],

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | Here you may specify custom validation messages for fields using the
    | convention "field.rule" to name the lines. This makes it quick to
    | specify a specific custom language line for a given field rule.
    |
    */

    'custom' => [
        'field-name' => [
            'rule-name' => 'custom-message',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Custom Validation fields
    |--------------------------------------------------------------------------
    |
    | The following language lines are used to swap field place-holders
    | with something more reader friendly such as E-Mail Address instead
    | of "email". This simply helps us make messages a little cleaner.
    |
    */

    'fields' => [],

];
