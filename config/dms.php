<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Backend Authentication Settings
    |--------------------------------------------------------------------------
    |
    | Here you can define the security settings for your backend authentication
    | login. By default, a user will be locked out for 60 seconds every 5 failed
    | login attempts.
    |
    | You may specify any number of oauth providers to allow user's authenticated
    | from other providers to login to the backend. The 'client-id' and
    | 'client-secret' can be configured from the provider. The redirect
    | uri is: '/dms/auth/oauth/{name}/response'
    |
    |
    */
    'auth'         => [
        'login'           => [
            'max-attempts' => 5,
            'lockout-time' => 60,
        ],
        'oauth-providers' => [
            [
                'name'           => 'developer',
                'label'          => 'Developer Login',
                'provider'       => \Dms\Web\Expressive\Auth\Oauth\Provider\GoogleOauthProvider::class,
                'client-id'      => '',
                'client-secret'  => '',
                'super-user'     => true,
                'roles'          => ['Developer'],
                'allowed-emails' => [
                    // 'some@mail.com',
                    // '*@some-domain.com.au',
                ],
            ],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Localization Settings
    |--------------------------------------------------------------------------
    |
    | Here you can define localization defaults and settings.
    |
    */
    'localisation' => [
        'form' => [
            'defaults' => [
                'currency' => 'AUD',
                'map'      => [-25.3455606, 131.0195906],
            ],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Company Information
    |--------------------------------------------------------------------------
    |
    | Here you can define the website and name of your company to be found
    | in the footer of every page.
    |
    */
    'contact'      => [
        'website' => 'http://contact-us...',
        'company' => 'Company Inc.',
    ],


    /*
    |--------------------------------------------------------------------------
    | File Storage Settings
    |--------------------------------------------------------------------------
    |
    | Public files are those that are stored directly in the public directory,
    | they are accessible via the web server and hence useful for downloads
    | and images to be used as content. These files can be trashed, that is,
    | moved to a private folder in the storage path and permanently deleted
    | if necessary.
    |
    | Temporary files are used for file uploads within forms, this allows
    | files to be uploaded immediately when they are selected. They will
    | then be moved to the correct folder when the form is submitted.
    |
    */
    'storage'      => [
        'public-files'  => [
            'dir' => public_path('files/'),
        ],
        'trashed-files' => [
            'dir' => storage_path('trash/'),
        ],
        'temp-files'    => [
            'dir'             => storage_path('dms/temp-uploads/'),
            'upload-expiry'   => 3600,
            'download-expiry' => 3600,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Database & Migration Settings
    |--------------------------------------------------------------------------
    |
    | Here you can define the settings for the migration generator command.
    | Running `php artisan dms:make:migration` will generate a command to
    | automatically sync the database to the current structure as per the
    | application's orm.
    |
    */
    'database'     => [
        'migrations' => [
            'dir'            => database_path('migrations'),
            'ignored-tables' => ['migrations'],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Services & Renderers
    |--------------------------------------------------------------------------
    |
    | You can extend the core functionality by providing your services here.
    | You may control the input and results of actions by adding transformers
    | and handlers.
    |
    | If you need to customize a page / form / field / column / chart here is where
    | you would supply your renderer class.
    |
    */
    'services'     => [
        'actions' => [
            'input-transformers' => [
                Dms\Web\Expressive\Action\InputTransformer\SymfonyToDmsUploadedFileTransformer::class,
                Dms\Web\Expressive\Action\InputTransformer\TempUploadedFileToUploadedFileTransformer::class,
            ],
            'result-handlers'    => [
                Dms\Web\Expressive\Action\ResultHandler\ViewDetailsResultHandler::class,
                Dms\Web\Expressive\Action\ResultHandler\CreatedObjectResultHandler::class,
                Dms\Web\Expressive\Action\ResultHandler\EditedObjectResultHandler::class,
                Dms\Web\Expressive\Action\ResultHandler\DeletedObjectResultHandler::class,
                Dms\Web\Expressive\Action\ResultHandler\NullResultHandler::class,
                Dms\Web\Expressive\Action\ResultHandler\MessageResultHandler::class,
                Dms\Web\Expressive\Action\ResultHandler\FileResultHandler::class,
                Dms\Web\Expressive\Action\ResultHandler\HtmlResultHandler::class,
                Dms\Web\Expressive\Action\ResultHandler\UrlResultHandler::class,
                Dms\Web\Expressive\Action\ResultHandler\GenericEntityResultHandler::class,
                Dms\Web\Expressive\Action\ResultHandler\GenericEntityCollectionResultHandler::class,
            ],
            'exception-handlers' => [
                Dms\Web\Expressive\Action\ExceptionHandler\AdminForbiddenExceptionHandler::class,
                Dms\Web\Expressive\Action\ExceptionHandler\InvalidFormSubmissionExceptionHandler::class,
                Dms\Web\Expressive\Action\ExceptionHandler\EntityOutOfSyncExceptionHandler::class,
                Dms\Web\Expressive\Action\ExceptionHandler\ErrorMessageExceptionHandler::class,
            ],
        ],

        'renderers' => [
            'forms' => [
                \Dms\Web\Expressive\Renderer\Form\DefaultFormRenderer::class,
            ],
            'form-fields' => [
                Dms\Web\Expressive\Renderer\Form\Field\AddressFieldRenderer::class,
                Dms\Web\Expressive\Renderer\Form\Field\ArrayOfFilesFieldsRenderer::class,
                Dms\Web\Expressive\Renderer\Form\Field\ArrayOfOptionsFieldRenderer::class,
                Dms\Web\Expressive\Renderer\Form\Field\ArrayOfFieldRenderer::class,
                Dms\Web\Expressive\Renderer\Form\Field\BoolFieldRenderer::class,
                Dms\Web\Expressive\Renderer\Form\Field\DateOrTimeFieldRenderer::class,
                Dms\Web\Expressive\Renderer\Form\Field\DateOrTimeRangeFieldRenderer::class,
                Dms\Web\Expressive\Renderer\Form\Field\DecimalFieldRenderer::class,
                Dms\Web\Expressive\Renderer\Form\Field\InnerFormFieldRenderer::class,
                Dms\Web\Expressive\Renderer\Form\Field\InnerModuleFieldRenderer::class,
                Dms\Web\Expressive\Renderer\Form\Field\IntFieldRenderer::class,
                Dms\Web\Expressive\Renderer\Form\Field\FileFieldRenderer::class,
                Dms\Web\Expressive\Renderer\Form\Field\MoneyFieldRenderer::class,
                Dms\Web\Expressive\Renderer\Form\Field\RadioOptionsFieldRender::class,
                Dms\Web\Expressive\Renderer\Form\Field\SelectOptionsFieldRender::class,
                Dms\Web\Expressive\Renderer\Form\Field\RgbaColourFieldRenderer::class,
                Dms\Web\Expressive\Renderer\Form\Field\RgbColourFieldRenderer::class,
                Dms\Web\Expressive\Renderer\Form\Field\StringFieldRenderer::class,
                Dms\Web\Expressive\Renderer\Form\Field\TextareaFieldRenderer::class,
                Dms\Web\Expressive\Renderer\Form\Field\TableOfFieldsRenderer::class,
                Dms\Web\Expressive\Renderer\Form\Field\WysiwygFieldRenderer::class,
            ],
            'table'       => [
                'columns'           => [
                    Dms\Web\Expressive\Renderer\Table\Column\DefaultColumnRendererAndFactory::class,
                ],
                'column-components' => [
                    Dms\Web\Expressive\Renderer\Table\Column\Component\OptimizedScalarValueComponentRenderer::class,
                    Dms\Web\Expressive\Renderer\Table\Column\Component\FilePreviewComponentRenderer::class,
                    // Will default to field renderers
                ],
            ],
            'charts'      => [
                Dms\Web\Expressive\Renderer\Chart\GraphChartRenderer::class,
                Dms\Web\Expressive\Renderer\Chart\GeoChartRenderer::class,
                Dms\Web\Expressive\Renderer\Chart\PieChartRenderer::class,
            ],
            'widgets'     => [
                Dms\Web\Expressive\Renderer\Widget\UnparameterizedActionWidgetRenderer::class,
                Dms\Web\Expressive\Renderer\Widget\ParameterizedActionWidgetRenderer::class,
                Dms\Web\Expressive\Renderer\Widget\TableWidgetRenderer::class,
                Dms\Web\Expressive\Renderer\Widget\ChartWidgetRenderer::class,
                Dms\Web\Expressive\Renderer\Widget\FormDataWidgetRenderer::class,
            ],
            'modules'     => [
                Dms\Web\Expressive\Renderer\Module\FileTreeModuleRenderer::class,
                Dms\Web\Expressive\Renderer\Module\ReadModuleRenderer::class,
                Dms\Web\Expressive\Renderer\Module\DefaultModuleRenderer::class,
            ],
            'packages'    => [
                Dms\Web\Expressive\Renderer\Package\DefaultPackageRenderer::class,
            ],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Action Configuration
    |--------------------------------------------------------------------------
    |
    | Here is some metadata about your defined action classes.
    |
    | You may mark an action as 'safe' meaning it does not perform any
    | dangerous operations such as updating or deleting data and hence
    | the result of the action can be loaded via a http GET request.
    |
    */
    'actions'      => [
        'safe' => [
            Dms\Core\Common\Crud\Action\Crud\ViewDetailsAction::class,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Keyword Convention
    |--------------------------------------------------------------------------
    |
    | The ui detects the type of operation being performed by the name of the action.
    |
    | Actions, depending on their type, may be treated differently from a ux perspective,
    | dangerous actions for instance may require extra confirmation before preceding.
    |
    */
    'keywords'     => [
        'danger'    => ['delete', 'remove', 'trash', 'drop', 'cancel', 'reset'],
        'warning'   => [],
        'success'   => ['confirm', 'approve', 'accept', 'verify', 'download'],
        'info'      => ['download', 'stats', 'display', 'details', 'view'],
        'primary'   => ['edit'],
        'overrides' => [
            // 'example-name' => 'danger',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Assets Configuration
    |--------------------------------------------------------------------------
    |
    | Here you may declare any extra asset files you wish to be loaded in
    | various parts of the backend. Put you custom scripts and styles here
    | as appropriate for your use case.
    |
    */
    'front-end'    => [
        'global' => [
            'stylesheets' => [
                '/vendor/dms/css/all.css',
            ],
            'scripts'     => [
                '/vendor/dms/js/all.js',
            ],
        ],
        'forms'  => [
            'stylesheets' => [
                '/vendor/dms/wysiwyg/wysiwyg.css',
            ],
            'scripts'     => [
                '/vendor/dms/wysiwyg/wysiwyg.js',
                // 'https://maps.googleapis.com/maps/api/js?v=3.exp&libraries=places',
            ],
        ],
        'tables' => [
            'stylesheets' => [
                //
            ],
            'scripts'     => [
                //
            ],
        ],
        'charts' => [
            'stylesheets' => [
                //
            ],
            'scripts'     => [
                'https://www.gstatic.com/charts/loader.js',
                'https://www.google.com/jsapi',
            ],
        ],
    ],

	"path" => [
		"storage" => dirname(__DIR__) . "/data/cache"
	]
];
