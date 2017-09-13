<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Authentication Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines are used during authentication for various
    | messages that we need to display to the user. You are free to modify
    | these language lines according to your application's requirements.
    |
    */

    'failed'   => 'These credentials do not match our records.',
    'banned'   => 'This account is currently under suspension.',
    'throttle' => 'Too many login attempts. Please try again in :seconds seconds.',
    'oauth'    => [
        'invalid-state'      => 'The login attempt could not be verified',
        'invalid-email'      => 'This account is not authorized to login',
        'registered-locally' => 'An account with this email address has an account locally, please enter your credentials below',
        'other-provider'     => 'This email has been associated with another account provider',
        'id-mismatch'        => 'This account ID does not match that from the provider',
    ],
    'user'     => [
        'password-reset'  => 'The account\'s password has been reset',
        'profile-updated' => 'The account\'s profile has been updated',
    ],
];
