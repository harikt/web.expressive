<?php

namespace Dms\Web\Expressive;

use Aura\Intl\Package;
use Aura\Intl\TranslatorLocator;
use Aura\Intl\TranslatorLocatorFactory;
use Aura\Session\Session;
use Aura\Session\SessionFactory;
use BehEh\Flaps\Flap;
use BehEh\Flaps\Flaps;
use BehEh\Flaps\Storage\DoctrineCacheAdapter;
use BehEh\Flaps\Throttling\LeakyBucketStrategy;
use BehEh\Flaps\Violation\PassiveViolationHandler;
use Cache\Adapter\Filesystem\FilesystemCachePool;
use Dms\Common\Structure\FileSystem\IApplicationDirectories;
use Dms\Core\Auth\IAdminRepository;
use Dms\Core\Auth\IAuthSystem;
use Dms\Core\Auth\IRoleRepository;
use Dms\Core\Event\IEventDispatcher;
use Dms\Core\Ioc\IIocContainer;
use Dms\Core\Language\ILanguageProvider;
use Dms\Core\Persistence\Db\Connection\IConnection;
use Dms\Core\Util\DateTimeClock;
use Dms\Core\Util\IClock;
use Dms\Web\Expressive\Action\ActionExceptionHandlerCollection;
use Dms\Web\Expressive\Action\ActionInputTransformerCollection;
use Dms\Web\Expressive\Action\ActionResultHandlerCollection;
use Dms\Web\Expressive\Auth\AdminDmsUserProvider;
use Dms\Web\Expressive\Auth\HktAuthSystem;
use Dms\Web\Expressive\Auth\Oauth\OauthProvider;
use Dms\Web\Expressive\Auth\Oauth\OauthProviderCollection;
use Dms\Web\Expressive\Auth\Password\BcryptPasswordHasher;
use Dms\Web\Expressive\Auth\Password\IPasswordHasherFactory;
use Dms\Web\Expressive\Auth\Password\IPasswordResetService;
use Dms\Web\Expressive\Auth\Password\PasswordHasherFactory;
use Dms\Web\Expressive\Auth\Password\PasswordResetService;
use Dms\Web\Expressive\Auth\Persistence\AdminRepository;
use Dms\Web\Expressive\Auth\Persistence\RoleRepository;
use Dms\Web\Expressive\Document\DirectoryTree;
use Dms\Web\Expressive\Document\PublicFileModule;
use Dms\Web\Expressive\Event\LaravelEventDispatcher;
use Dms\Web\Expressive\File\ITemporaryFileService;
use Dms\Web\Expressive\File\LaravelApplicationDirectories;
use Dms\Web\Expressive\File\Persistence\ITemporaryFileRepository;
use Dms\Web\Expressive\File\Persistence\TemporaryFileRepository;
use Dms\Web\Expressive\File\TemporaryFileService;
use Dms\Web\Expressive\Ioc\LaravelIocContainer;
use Dms\Web\Expressive\Language\LanguageProvider;
use Dms\Web\Expressive\Middleware\AuthenticationMiddleware;
use Dms\Web\Expressive\Renderer\Chart\ChartRendererCollection;
use Dms\Web\Expressive\Renderer\Form\FieldRendererCollection;
use Dms\Web\Expressive\Renderer\Form\FormRendererCollection;
use Dms\Web\Expressive\Renderer\Module\ModuleRendererCollection;
use Dms\Web\Expressive\Renderer\Package\PackageRendererCollection;
use Dms\Web\Expressive\Renderer\Table\ColumnComponentRendererCollection;
use Dms\Web\Expressive\Renderer\Table\ColumnRendererFactoryCollection;
use Dms\Web\Expressive\Renderer\Widget\WidgetRendererCollection;
use Dms\Web\Expressive\View\DmsNavigationViewComposer;
use Doctrine\Common\Cache\FilesystemCache;
use Harikt\Blade\BladeViewFactory;
use Illuminate\Contracts\Container\Container;
use Illuminate\Contracts\View\Factory as ViewFactory;
use Illuminate\Events\Dispatcher;
use League\Flysystem\Adapter\Local;
use League\Flysystem\Filesystem;
use Psr\Cache\CacheItemPoolInterface;
use Zend\Expressive\Helper\UrlHelper;

class ContainerConfig
{
    public function define(LaravelIocContainer $container)
    {
        $container->bindCallback(IIocContainer::SCOPE_SINGLETON, IIocContainer::class, function () use ($container) {
            return $container;
        });

        $container->bindValue(Container::class, $container->getLaravelContainer());

        $container->bindCallback(IIocContainer::SCOPE_SINGLETON, Session::class, function () {
            $session_factory = new SessionFactory;
            $session = $session_factory->newInstance($_COOKIE);
            return $session;
        });

        $container->bind(IIocContainer::SCOPE_SINGLETON, AdminDmsUserProvider::class, AdminDmsUserProvider::class);

        $container->bindCallback(IIocContainer::SCOPE_SINGLETON, IPasswordHasherFactory::class, function () {
            return new PasswordHasherFactory(
                [
                    BcryptPasswordHasher::ALGORITHM => function ($costFactor) {
                        return new BcryptPasswordHasher($costFactor);
                    },
                ],
                BcryptPasswordHasher::ALGORITHM,
                10
            );
        });

        $container->bind(IIocContainer::SCOPE_SINGLETON, IAuthSystem::class, HktAuthSystem::class);
        $container->bind(IIocContainer::SCOPE_SINGLETON, IAdminRepository::class, AdminRepository::class);
        $container->bind(IIocContainer::SCOPE_SINGLETON, IRoleRepository::class, RoleRepository::class);
        $container->bind(IIocContainer::SCOPE_SINGLETON, IPasswordResetService::class, PasswordResetService::class);
        $container->bind(IIocContainer::SCOPE_SINGLETON, \Psr\Http\Message\ResponseInterface::class, \Zend\Diactoros\Response::class);

        $container->bindCallback(IIocContainer::SCOPE_SINGLETON, PackageRendererCollection::class, function () use ($container) {
            return new PackageRendererCollection($container->makeAll(
                config('dms.services.renderers.packages')
            ));
        });

        $container->bindCallback(IIocContainer::SCOPE_SINGLETON, FormRendererCollection::class, function () use ($container) {
            return new FormRendererCollection($container->makeAll(
                config('dms.services.renderers.forms')
            ));
        });

        $container->bindCallback(IIocContainer::SCOPE_SINGLETON, FieldRendererCollection::class, function () use ($container) {
            return new FieldRendererCollection($container->makeAll(
                config('dms.services.renderers.form-fields')
            ));
        });

        $container->bindCallback(IIocContainer::SCOPE_SINGLETON, ColumnComponentRendererCollection::class, function () use ($container) {
            return new ColumnComponentRendererCollection($container->makeAll(
                array_merge(
                    config('dms.services.renderers.table.column-components'),
                    config('dms.services.renderers.form-fields')
                )
            ));
        });

        $container->bindCallback(IIocContainer::SCOPE_SINGLETON, ColumnRendererFactoryCollection::class, function () use ($container) {
            return new ColumnRendererFactoryCollection(
                $container->make(ColumnComponentRendererCollection::class),
                $container->makeAll(
                    config('dms.services.renderers.table.columns')
                )
            );
        });

        $container->bindCallback(IIocContainer::SCOPE_SINGLETON, ChartRendererCollection::class, function () use ($container) {
            return new ChartRendererCollection($container->makeAll(
                config('dms.services.renderers.charts')
            ));
        });

        $container->bindCallback(IIocContainer::SCOPE_SINGLETON, WidgetRendererCollection::class, function () use ($container) {
            return new WidgetRendererCollection($container->makeAll(
                config('dms.services.renderers.widgets')
            ));
        });

        $container->bindCallback(IIocContainer::SCOPE_SINGLETON, ModuleRendererCollection::class, function () use ($container) {
            return new ModuleRendererCollection($container->makeAll(
                config('dms.services.renderers.modules')
            ));
        });



        $container->bind(IIocContainer::SCOPE_SINGLETON, ILanguageProvider::class, LanguageProvider::class);

        $container->bindCallback(IIocContainer::SCOPE_SINGLETON, CacheItemPoolInterface::class, function () {
            return new FilesystemCachePool(new Filesystem(new Local(storage_path('dms/cache'))));
        });

        $container->bindCallback(IIocContainer::SCOPE_SINGLETON, IEventDispatcher::class, function () {
            /** @var LaravelEventDispatcher $eventDispatcher */
            $eventDispatcher = new LaravelEventDispatcher(new Dispatcher());

            return $eventDispatcher->inNamespace('dms::');
        });

        $container->bindCallback(IIocContainer::SCOPE_SINGLETON, \Illuminate\Contracts\Cache\Store::class, function () use ($container) {
            return $container->make(\Illuminate\Cache\ArrayStore::class);
        });

        $container->bindCallback(IIocContainer::SCOPE_SINGLETON, \Dms\Common\Structure\FileSystem\Directory::class, function () {
            return new \Dms\Common\Structure\FileSystem\Directory(dirname(__DIR__) . '/public');
        });

        $container->bindCallback(IIocContainer::SCOPE_SINGLETON, \Dms\Web\Expressive\Document\DirectoryTree::class, function () use ($container) {
            $directory = $container->get(\Dms\Common\Structure\FileSystem\Directory::class);
            return new \Dms\Web\Expressive\Document\DirectoryTree($directory, [], []);
        });

        $container->bindCallback(IIocContainer::SCOPE_SINGLETON, IConnection::class, function () {
            $config = new \Doctrine\DBAL\Configuration();

            $connectionParams = [
                'url' => getenv('driver') . '://' . getenv('username') . ':' . getenv('password') . '@' . getenv('host') . '/' . getenv('database'),
                'driverOptions' => [
                    \PDO::MYSQL_ATTR_FOUND_ROWS => true
                ],
            ];
            $connection = \Doctrine\DBAL\DriverManager::getConnection($connectionParams, $config);

            return new \Dms\Core\Persistence\Db\Doctrine\DoctrineConnection($connection);
        });

        $container->bindCallback(IIocContainer::SCOPE_SINGLETON, OauthProviderCollection::class, function () {
            $providers = [];

            foreach (config('dms.auth.oauth-providers', []) as $providerConfig) {
                /** @var OauthProvider $providerClass */
                $providerClass = $providerConfig['provider'];
                $providers[]   = $providerClass::fromConfiguration($providerConfig);
            }

            return new OauthProviderCollection($providers);
        });

        $container->bindValue('path.storage', dirname(__DIR__) . '/data/cache');
        // $container->bindValue('url', $container->get(UrlHelper::class));
        // $container->bindValue('route', $container->get(UrlHelper::class));

        $container->bindValue('path.public', dirname(__DIR__) . '/public');

        $dmsconfig = require dirname(__DIR__) . '/config/dms.php';

        $container->bindValue('laravel.config', new \Illuminate\Config\Repository(['dms' => $dmsconfig]));

        $container->alias('laravel.config', \Illuminate\Config\Repository::class);
        $container->alias('laravel.config', \Illuminate\Contracts\Config\Repository::class);

        $container->bind(IIocContainer::SCOPE_SINGLETON, IClock::class, DateTimeClock::class);
        $container->bind(IIocContainer::SCOPE_SINGLETON, ITemporaryFileService::class, TemporaryFileService::class);
        $container->bind(IIocContainer::SCOPE_SINGLETON, ITemporaryFileRepository::class, TemporaryFileRepository::class);
        $container->bind(IIocContainer::SCOPE_SINGLETON, IApplicationDirectories::class, LaravelApplicationDirectories::class);

        $container->bindCallback(IIocContainer::SCOPE_SINGLETON, ViewFactory::class, function () use ($container) {
            $factory = new BladeViewFactory();

            $viewFactory = $factory->createViewFactory($container);

            $viewFactory->composer('dms::template.default', DmsNavigationViewComposer::class);
            $viewFactory->composer('dms::dashboard', DmsNavigationViewComposer::class);
            $viewFactory->setContainer($container->getLaravelContainer());

            return $viewFactory;
        });

        $container->bind(IIocContainer::SCOPE_SINGLETON, Illuminate\Contracts\Events\Dispatcher::class, Illuminate\Events\Dispatcher::class);

        $container->bindCallback(IIocContainer::SCOPE_SINGLETON, ActionInputTransformerCollection::class, function () use ($container) {
            return new ActionInputTransformerCollection($container->makeAll(
                config('dms.services.actions.input-transformers')
            ));
        });

        $container->bindCallback(IIocContainer::SCOPE_SINGLETON, ActionResultHandlerCollection::class, function () use ($container) {
            return new ActionResultHandlerCollection($container->makeAll(
                config('dms.services.actions.result-handlers')
            ));
        });

        $container->bindCallback(IIocContainer::SCOPE_SINGLETON, ActionExceptionHandlerCollection::class, function () use ($container) {
            return new ActionExceptionHandlerCollection($container->makeAll(
                config('dms.services.actions.exception-handlers')
            ));
        });

        $container->bind(IIocContainer::SCOPE_SINGLETON, AuthenticationMiddleware::class, AuthenticationMiddleware::class);

        $container->bindCallback(IIocContainer::SCOPE_SINGLETON, TranslatorLocator::class, function () {
            $factory = new TranslatorLocatorFactory();
            $translators = $factory->newInstance();

            // get the package locator
            $packages = $translators->getPackages();

            $directory = dirname(dirname(__DIR__)) . '/resources/lang/';
            foreach (new \DirectoryIterator($directory) as $fileInfo) {
                if ($fileInfo->isDot()) {
                    continue;
                }

                // place into the locator for dms
                $packages->set('dms', $fileInfo->getBasename('.php'), function () {
                    $package = new Package;
                    $package->setMessages(require $fileInfo->getFilename());

                    return $package;
                });
            }

            return $translators;
        });

        $container->alias(TranslatorLocator::class, 'translator');

        $container->bindCallback(IIocContainer::SCOPE_SINGLETON, PublicFileModule::class, function () use ($container) {
            return new PublicFileModule(
               DirectoryTree::from(config('dms.storage.public-files.dir')),
               DirectoryTree::from(config('dms.storage.trashed-files.dir')),
               $container->get(IAuthSystem::class)
           );
        });

        $container->bindCallback(IIocContainer::SCOPE_SINGLETON, Flap::class, function () use ($container) {
            $cache = new FilesystemCache(dirname(__DIR__) . '/data/cache');
            $storage = new DoctrineCacheAdapter($cache);
            $flaps = new Flaps($storage);
            $flap = $flaps->__get('login-throttle');
            $flap->pushThrottlingStrategy(
                new LeakyBucketStrategy(3, '15s')
            );
            $flap->setViolationHandler(new PassiveViolationHandler);

            return $flap;
        });

        $container->alias(ConnectionResolverInterface::class, 'db');
    }
}
