<?php

namespace Dms\Web\Expressive;

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
                'middleware'      => Http\Handler\Auth\LoginHandler::class,
                'allowed_methods' => ['GET', 'POST'],
            ],
            [
                'name'            => 'dms::auth.logout',
                'path'            => '/dms/auth/logout',
                'middleware'      => Http\Handler\Auth\LogoutHandler::class,
                'allowed_methods' => ['GET'],
            ],
            [
                'name'            => 'dms::auth.password.forgot',
                'path'            => '/dms/auth/password/email',
                'middleware'      => Http\Handler\Auth\Password\ResetLinkEmailHandler::class,
                'allowed_methods' => ['GET', 'POST'],
            ],

            // OauthHandler@redirectToProvider
            [
                'name'            => 'dms::auth.oauth.redirect',
                'path'            => '/dms/auth/oauth/{provider}/redirect',
                'middleware'      => Http\Handler\Auth\Oauth\RedirectToProviderHandler::class,
                'allowed_methods' => ['GET'],
            ],
            // OauthHandler@handleProviderResponse
            [
                'name'            => 'dms::auth.oauth.response',
                'path'            => '/dms/auth/oauth/{provider}/response',
                'middleware'      => Http\Handler\Auth\Oauth\HandleProviderResponseHandler::class,
                'allowed_methods' => ['GET'],
            ],
            // PasswordHandler@sendResetLinkEmail
            // [
            //     'name'            => 'dms::auth.oauth.response',
            //     'path'            => '/dms/auth/password/email',
            //     'middleware'      => Http\Handler\Auth\PasswordHandler::class,
            //     'allowed_methods' => ['POST'],
            // ],
            // PasswordHandler@showPasswordResetForm
            // [
            //     'name'            => 'dms::password.reset',
            //     'path'            => '/dms/auth/password/reset[/{token}]',
            //     'middleware'      => Http\Handler\Auth\PasswordHandler::class,
            //     'allowed_methods' => ['GET'],
            // ],
            // PasswordHandler@reset
            // [
            //     'name'            => 'dms::auth.oauth.response',
            //     'path'            => '/dms/auth/password/reset',
            //     'middleware'      => Http\Handler\Auth\PasswordHandler::class,
            //     'allowed_methods' => ['POST'],
            // ],

            [
                'name'            => 'dms::index',
                'path'            => '/dms',
                'middleware'      => Http\Handler\IndexHandler::class,
                'allowed_methods' => ['GET'],
            ],
            // FileHandler@upload
            [
                'name'            => 'dms::file.upload',
                'path'            => '/dms/file/upload',
                'middleware'      => Http\Handler\File\UploadHandler::class,
                'allowed_methods' => ['POST'],
            ],
            // FileHandler@preview
            [
                'name'            => 'dms::file.preview',
                'path'            => '/dms/file/preview/{token}',
                'middleware'      => Http\Handler\File\PreviewHandler::class,
                'allowed_methods' => ['GET'],
            ],
            // FileHandler@download
            [
                'name'            => 'dms::file.download',
                'path'            => '/dms/file/download/{token}',
                'middleware'      => Http\Handler\File\DownloadHandler::class,
                'allowed_methods' => ['GET'],
            ],
            // Packages
            // Package\PackageHandler@showDashboard
            [
                'name'            => 'dms::package.dashboard',
                'path'            => '/dms/package/{package}/dashboard',
                'middleware'      => Http\Handler\Package\PackageHandler::class,
                'allowed_methods' => ['GET'],
            ],
            [
                'name'            => 'dms::package.module.dashboard',
                'path'            => '/dms/package/{package}/{module}',
                'middleware'      => Http\Handler\Package\Module\ModuleHandler::class,
                'allowed_methods' => ['GET'],
            ],
            [
                'name'            => 'dms::package.module.action.form',
                'path'            => '/dms/package/{package}/{module}/action/{action}/form[/{object_id}]',
                'middleware'      => Http\Handler\Package\Module\Action\ShowFormHandler::class,
                'allowed_methods' => ['GET'],
            ],
            [
                'name'            => 'dms::package.module.action.form.stage',
                'path'            => '/dms/package/{package}/{module}/action/{action}/form/stage/{stage}',
                'middleware'      => Http\Handler\Package\Module\Action\FormStageHandler::class,
                'allowed_methods' => ['POST'],
            ],
            [
                'name'            => 'dms::package.module.action.form.stage.action',
                'path'            => '/dms/package/{package}/{module}/action/{action}/form/stage/{stage}/form[/{form_action}]',
                'middleware'      => Http\Handler\Package\Module\Action\FormRendererHandler::class,
                // 'allowed_methods' => [Route::HTTP_METHOD_ANY],
            ],
            [
                'name'            => 'dms::package.module.action.form.object.stage.action',
                'path'            => '/dms/package/{package}/{module}/action/{action}/form/{object_id}/stage/{stage}/form[/{form_action}]',
                'middleware'      => Http\Handler\Package\Module\Action\FormRendererHandler::class,
                // 'allowed_methods' => [Route::HTTP_METHOD_ANY],
            ],
            [
                'name'            => 'dms::package.module.action.form.stage.field.action',
                'path'            => '/dms/package/{package}/{module}/action/{action}/form/stage/{stage}/field/{field_name}[/{field_action}]',
                'middleware'      => Http\Handler\Package\Module\Action\FieldRendererHandler::class,
                // 'allowed_methods' => [Route::HTTP_METHOD_ANY],
            ],
            [
                'name'            => 'dms::package.module.action.form.object.stage.field.action',
                'path'            => '/dms/package/{package}/{module}/action/{action}/form/{object_id}/stage/{stage}/field/{field_name}[/{field_action}]',
                'middleware'      => Http\Handler\Package\Module\Action\FieldRendererHandler::class,
                // 'allowed_methods' => [Route::HTTP_METHOD_ANY],
            ],
            [
                'name'            => 'dms::package.module.action.run',
                'path'            => '/dms/package/{package}/{module}/action/{action}/run',
                'middleware'      => Http\Handler\Package\Module\Action\RunHandler::class,
                'allowed_methods' => ['POST'],
            ],
            [
                'name'            => 'dms::package.module.action.show',
                'path'            => '/dms/package/{package}/{module}/action/{action}/show[/{object_id}]',
                'middleware'      => Http\Handler\Package\Module\Action\ShowResultHandler::class,
                'allowed_methods' => ['GET'],
            ],
            [
                'name'            => 'dms::package.module.table.view.show',
                'path'            => '/dms/package/{package}/{module}/table/{table}/{view}',
                'middleware'      => Http\Handler\Package\Module\Table\ShowHandler::class,
                'allowed_methods' => ['GET'],
            ],
            [
                'name'            => 'dms::package.module.table.view.load',
                'path'            => '/dms/package/{package}/{module}/table/{table}/{view}/load',
                'middleware'      => Http\Handler\Package\Module\Table\LoadTableRowsHandler::class,
                'allowed_methods' => ['POST'],
            ],

            // Charts
            // Http\Handler\Package\Module\ChartHandler@showChart
            [
                'name'            => 'dms::package.module.chart.view.show',
                'path'            => '/dms/package/{package}/{module}/chart/{chart}/{view}',
                'middleware'      => Http\Handler\Package\Module\Chart\ShowChartHandler::class,
                'allowed_methods' => ['GET'],
            ],
            // Http\Handler\Package\Module\ChartHandler@loadChartData
            [
                'name'            => 'dms::package.module.chart.view.load',
                'path'            => '/dms/package/{package}/{module}/chart/{chart}/{view}/load',
                'middleware'      => Http\Handler\Package\Module\Chart\LoadChartDataHandler::class,
                'allowed_methods' => ['POST'],
            ],

            // $app->any('{catch_all}', 'ErrorHandler@notFound')->where('catch_all', '(.*)');
        ];
    }
}
