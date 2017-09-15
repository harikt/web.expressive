<?php

namespace Dms\Web\Expressive;

use Dms\Web\Expressive\Middleware\AuthenticationMiddleware;
use Zend\Expressive\Router\Route;

/**
 * The configuration provider for the App module
 *
 * @see https://docs.zendframework.com/zend-component-installer/
 */
class ConfigProvider
{
    /**
     * Returns the configuration array
     *
     * To add a bit of a structure, each section is defined in a separate
     * method which returns an array with its configuration.
     *
     * @return array
     */
    public function __invoke()
    {
        return [
            'dependencies' => $this->getDependencies(),
            'templates'    => $this->getTemplates(),
            'routes'       => $this->getRoutes(),
            'console' => [
                'commands' => [
                    Scaffold\ScaffoldCmsCommand::class,
                    Scaffold\ScaffoldPersistenceCommand::class,
                ],
            ],
        ];
    }

    /**
     * Returns the container dependencies
     *
     * @return array
     */
    public function getDependencies()
    {
        return [
            'invokables' => [
                // Action\PingAction::class => Action\PingAction::class,
            ],
            'factories'  => [
                // Action\HomePageAction::class => Action\HomePageFactory::class,
            ],
        ];
    }

    /**
     * Returns the templates configuration
     *
     * @return array
     */
    public function getTemplates()
    {
        return [
            'paths' => [
                'dms'    => [__DIR__ . '/../resources/views'],
            ],
        ];
    }

    public function getRoutes() : array
    {
        return [
            // Auth
            [
                'name'            => 'dms::auth.login',
                'path'            => '/dms/auth/login',
                'middleware'      => Http\Controllers\Auth\LoginController::class,
                'allowed_methods' => ['GET', 'POST'],
            ],
            [
                'name'            => 'dms::auth.logout',
                'path'            => '/dms/auth/logout',
                'middleware'      => Http\Controllers\Auth\LogoutController::class,
                'allowed_methods' => ['GET'],
            ],
            [
                'name'            => 'dms::auth.password.forgot',
                'path'            => '/dms/auth/password/email',
                'middleware'      => Http\Controllers\Auth\Password\ResetLinkEmailController::class,
                'allowed_methods' => ['GET', 'POST'],
            ],

            // OauthController@redirectToProvider
            // [
            //     'name'            => 'dms::auth.oauth.redirect',
            //     'path'            => '/dms/auth/oauth/{provider}/redirect',
            //     'middleware'      => Http\Controllers\Auth\OauthController::class,
            //     'allowed_methods' => ['GET'],
            // ],
            // OauthController@handleProviderResponse
            // [
            //     'name'            => 'dms::auth.oauth.response',
            //     'path'            => '/dms/auth/oauth/{provider}/response',
            //     'middleware'      => Http\Controllers\Auth\OauthController::class,
            //     'allowed_methods' => ['GET'],
            // ],
            // PasswordController@sendResetLinkEmail
            // [
            //     'name'            => 'dms::auth.oauth.response',
            //     'path'            => '/dms/auth/password/email',
            //     'middleware'      => Http\Controllers\Auth\PasswordController::class,
            //     'allowed_methods' => ['POST'],
            // ],
            // PasswordController@showPasswordResetForm
            // [
            //     'name'            => 'dms::password.reset',
            //     'path'            => '/dms/auth/password/reset[/{token}]',
            //     'middleware'      => Http\Controllers\Auth\PasswordController::class,
            //     'allowed_methods' => ['GET'],
            // ],
            // PasswordController@reset
            // [
            //     'name'            => 'dms::auth.oauth.response',
            //     'path'            => '/dms/auth/password/reset',
            //     'middleware'      => Http\Controllers\Auth\PasswordController::class,
            //     'allowed_methods' => ['POST'],
            // ],

            [
                'name'            => 'dms::index',
                'path'            => '/dms',
                'middleware'      => Http\Controllers\IndexController::class,
                'allowed_methods' => ['GET'],
            ],
            // FileController@upload
            [
                'name'            => 'dms::file.upload',
                'path'            => '/dms/file/upload',
                'middleware'      => Http\Controllers\File\UploadController::class,
                'allowed_methods' => ['POST'],
            ],
            // FileController@preview
            [
                'name'            => 'dms::file.preview',
                'path'            => '/dms/file/preview/{token}',
                'middleware'      => Http\Controllers\File\PreviewController::class,
                'allowed_methods' => ['GET'],
            ],
            // FileController@download
            [
                'name'            => 'dms::file.download',
                'path'            => '/dms/file/download/{token}',
                'middleware'      => Http\Controllers\File\DownloadController::class,
                'allowed_methods' => ['GET'],
            ],
            // Packages
            // Package\PackageController@showDashboard
            [
                'name'            => 'dms::package.dashboard',
                'path'            => '/dms/package/{package}/dashboard',
                'middleware'      => Http\Controllers\Package\PackageController::class,
                'allowed_methods' => ['GET'],
            ],
            [
                'name'            => 'dms::package.module.dashboard',
                'path'            => '/dms/package/{package}/{module}',
                'middleware'      => Http\Controllers\Package\Module\ModuleController::class,
                'allowed_methods' => ['GET'],
            ],
            [
                'name'            => 'dms::package.module.action.form',
                'path'            => '/dms/package/{package}/{module}/action/{action}/form[/{object_id}]',
                'middleware'      => Http\Controllers\Package\Module\Action\ShowFormController::class,
                'allowed_methods' => ['GET'],
            ],
            [
                'name'            => 'dms::package.module.action.form.stage',
                'path'            => '/dms/package/{package}/{module}/action/{action}/form/stage/{stage}',
                'middleware'      => Http\Controllers\Package\Module\Action\FormStageController::class,
                'allowed_methods' => ['POST'],
            ],
            [
                'name'            => 'dms::package.module.action.form.stage.action',
                'path'            => '/dms/package/{package}/{module}/action/{action}/form/stage/{stage}/form[/{form_action}]',
                'middleware'      => Http\Controllers\Package\Module\Action\FormRendererController::class,
                // 'allowed_methods' => [Route::HTTP_METHOD_ANY],
            ],
            [
                'name'            => 'dms::package.module.action.form.object.stage.action',
                'path'            => '/dms/package/{package}/{module}/action/{action}/form/{object_id}/stage/{stage}/form[/{form_action}]',
                'middleware'      => Http\Controllers\Package\Module\Action\FormRendererController::class,
                // 'allowed_methods' => [Route::HTTP_METHOD_ANY],
            ],
            [
                'name'            => 'dms::package.module.action.form.stage.field.action',
                'path'            => '/dms/package/{package}/{module}/action/{action}/form/stage/{stage}/field/{field_name}[/{field_action}]',
                'middleware'      => Http\Controllers\Package\Module\Action\FieldRendererController::class,
                // 'allowed_methods' => [Route::HTTP_METHOD_ANY],
            ],
            [
                'name'            => 'dms::package.module.action.form.object.stage.field.action',
                'path'            => '/dms/package/{package}/{module}/action/{action}/form/{object_id}/stage/{stage}/field/{field_name}[/{field_action}]',
                'middleware'      => Http\Controllers\Package\Module\Action\FieldRendererController::class,
                // 'allowed_methods' => [Route::HTTP_METHOD_ANY],
            ],
            [
                'name'            => 'dms::package.module.action.run',
                'path'            => '/dms/package/{package}/{module}/action/{action}/run',
                'middleware'      => Http\Controllers\Package\Module\Action\RunController::class,
                'allowed_methods' => ['POST'],
            ],
            [
                'name'            => 'dms::package.module.action.show',
                'path'            => '/dms/package/{package}/{module}/action/{action}/show[/{object_id}]',
                'middleware'      => Http\Controllers\Package\Module\Action\ShowResultController::class,
                'allowed_methods' => ['GET'],
            ],
            [
                'name'            => 'dms::package.module.table.view.show',
                'path'            => '/dms/package/{package}/{module}/table/{table}/{view}',
                'middleware'      => Http\Controllers\Package\Module\Table\ShowController::class,
                'allowed_methods' => ['GET'],
            ],
            [
                'name'            => 'dms::package.module.table.view.load',
                'path'            => '/dms/package/{package}/{module}/table/{table}/{view}/load',
                'middleware'      => Http\Controllers\Package\Module\Table\LoadTableRowsController::class,
                'allowed_methods' => ['POST'],
            ],

            // Charts
            // Http\Controllers\Package\Module\ChartController@showChart
            [
                'name'            => 'dms::package.module.chart.view.show',
                'path'            => '/dms/package/{package}/{module}/chart/{chart}/{view}',
                'middleware'      => Http\Controllers\Package\Module\Chart\ShowChartController::class,
                'allowed_methods' => ['GET'],
            ],
            // Http\Controllers\Package\Module\ChartController@loadChartData
            [
                'name'            => 'dms::package.module.chart.view.load',
                'path'            => '/dms/package/{package}/{module}/chart/{chart}/{view}/load',
                'middleware'      => Http\Controllers\Package\Module\Chart\LoadChartDataController::class,
                'allowed_methods' => ['POST'],
            ],

            // $app->any('{catch_all}', 'ErrorController@notFound')->where('catch_all', '(.*)');
        ];
    }
}
