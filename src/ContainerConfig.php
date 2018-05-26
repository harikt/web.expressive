<?php

namespace Dms\Web\Expressive;

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
use Dms\Web\Expressive\Http\Middleware\Authenticate;
use Dms\Web\Expressive\Http\Middleware\LoadVariablesToTemplate;
use Dms\Web\Expressive\Http\Middleware\VerifyCsrfToken;
use Dms\Web\Expressive\Language\SymfonyLanguageProvider;
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
use Illuminate\Config\Repository;
use Illuminate\Contracts\Config\Repository as ConfigRepository;
use Illuminate\Contracts\Container\Container;
use Illuminate\Contracts\View\Factory as ViewFactory;
use Illuminate\Events\Dispatcher;
use League\Flysystem\Adapter\Local;
use League\Flysystem\Filesystem;
use ParagonIE\AntiCSRF\AntiCSRF;
use Psr\Cache\CacheItemPoolInterface;
use RKA\Middleware\IpAddress;
use Symfony\Component\Translation\Loader\ArrayLoader;
use Symfony\Component\Translation\MessageSelector;
use Symfony\Component\Translation\Translator;

class ContainerConfig
{
    public function define(IIocContainer $container)
    {
        $container->bindValue(Repository::class, new Repository($container->get('config')));
        $container->alias(Repository::class, ConfigRepository::class);

        $container->bindValue('path.storage', $container->get(Repository::class)->get('dms.datastorage.path') . '/cache');
        $container->bindValue('path.public', $container->get(Repository::class)->get('dms.public.path'));

        $container->bindCallback(
            IIocContainer::SCOPE_SINGLETON,
            IIocContainer::class,
            function () use ($container) {
                return $container;
            }
        );

        $container->bindValue(Container::class, $container->getIlluminateContainer());

        $container->bindCallback(
            IIocContainer::SCOPE_SINGLETON,
            Session::class,
            function () {
                $session_factory = new SessionFactory;
                $session = $session_factory->newInstance($_COOKIE);
                return $session;
            }
        );

        $container->bind(IIocContainer::SCOPE_SINGLETON, AdminDmsUserProvider::class, AdminDmsUserProvider::class);

        $container->bind(IIocContainer::SCOPE_SINGLETON, Authenticate::class, Authenticate::class);
        $container->bind(IIocContainer::SCOPE_SINGLETON, LoadVariablesToTemplate::class, LoadVariablesToTemplate::class);
        $container->bind(IIocContainer::SCOPE_SINGLETON, IpAddress::class, IpAddress::class);

        $container->bindCallback(
            IIocContainer::SCOPE_SINGLETON,
            IPasswordHasherFactory::class,
            function () {
                return new PasswordHasherFactory(
                    [
                        BcryptPasswordHasher::ALGORITHM => function ($costFactor) {
                            return new BcryptPasswordHasher($costFactor);
                        },
                    ],
                    BcryptPasswordHasher::ALGORITHM,
                    10
                );
            }
        );

        $container->bind(IIocContainer::SCOPE_SINGLETON, IAuthSystem::class, HktAuthSystem::class);
        $container->bind(IIocContainer::SCOPE_SINGLETON, IAdminRepository::class, AdminRepository::class);
        $container->bind(IIocContainer::SCOPE_SINGLETON, IRoleRepository::class, RoleRepository::class);
        $container->bind(IIocContainer::SCOPE_SINGLETON, IPasswordResetService::class, PasswordResetService::class);

        $container->bindCallback(
            IIocContainer::SCOPE_SINGLETON,
            PackageRendererCollection::class,
            function () use ($container) {
                return new PackageRendererCollection(
                    $this->makeAll(
                        $container,
                        $container->get(Repository::class)->get('dms.services.renderers.packages')
                    )
                );
            }
        );

        $container->bindCallback(
            IIocContainer::SCOPE_SINGLETON,
            FormRendererCollection::class,
            function () use ($container) {
                return new FormRendererCollection(
                    $this->makeAll(
                        $container,
                        $container->get(Repository::class)->get('dms.services.renderers.forms')
                    )
                );
            }
        );

        $container->bindCallback(
            IIocContainer::SCOPE_SINGLETON,
            FieldRendererCollection::class,
            function () use ($container) {
                return new FieldRendererCollection(
                    $this->makeAll(
                        $container,
                        $container->get(Repository::class)->get('dms.services.renderers.form-fields')
                    )
                );
            }
        );

        $container->bindCallback(
            IIocContainer::SCOPE_SINGLETON,
            ColumnComponentRendererCollection::class,
            function () use ($container) {
                return new ColumnComponentRendererCollection(
                    $this->makeAll(
                        $container,
                        array_merge(
                            $container->get(Repository::class)->get('dms.services.renderers.table.column-components'),
                            $container->get(Repository::class)->get('dms.services.renderers.form-fields')
                        )
                    )
                );
            }
        );

        $container->bindCallback(
            IIocContainer::SCOPE_SINGLETON,
            ColumnRendererFactoryCollection::class,
            function () use ($container) {
                return new ColumnRendererFactoryCollection(
                    $container->get(ColumnComponentRendererCollection::class),
                    $this->makeAll(
                        $container,
                        $container->get(Repository::class)->get('dms.services.renderers.table.columns')
                    )
                );
            }
        );

        $container->bindCallback(
            IIocContainer::SCOPE_SINGLETON,
            ChartRendererCollection::class,
            function () use ($container) {
                return new ChartRendererCollection(
                    $this->makeAll(
                        $container,
                        $container->get(Repository::class)->get('dms.services.renderers.charts')
                    )
                );
            }
        );

        $container->bindCallback(
            IIocContainer::SCOPE_SINGLETON,
            WidgetRendererCollection::class,
            function () use ($container) {
                return new WidgetRendererCollection(
                    $this->makeAll(
                        $container,
                        $container->get(Repository::class)->get('dms.services.renderers.widgets')
                    )
                );
            }
        );

        $container->bindCallback(
            IIocContainer::SCOPE_SINGLETON,
            ModuleRendererCollection::class,
            function () use ($container) {
                return new ModuleRendererCollection(
                    $this->makeAll(
                        $container,
                        $container->get(Repository::class)->get('dms.services.renderers.modules')
                    )
                );
            }
        );

        $container->bindCallback(
            IIocContainer::SCOPE_SINGLETON,
            Translator::class,
            function () use ($container) {
                $translator = new Translator('en_US', new MessageSelector());
                $translator->addLoader('array', new ArrayLoader());

                $directory = dirname(__DIR__) . '/resources/lang/';
                foreach (new \DirectoryIterator($directory) as $fileInfo) {
                    if ($fileInfo->isDot()) {
                        continue;
                    }

                    $file = $fileInfo->getPathname();

                    if (is_readable($file)) {
                        // place into the locator for dms
                        $translator->addResource('array', include $file, $fileInfo->getBasename('.php'), 'dms');
                    }
                }

                return $translator;
            }
        );

        $container->bindCallback(
            IIocContainer::SCOPE_SINGLETON,
            SymfonyLanguageProvider::class,
            function () use ($container) {
                $translator = $container->get(Translator::class);

                return new SymfonyLanguageProvider($translator);
            }
        );

        $container->bind(IIocContainer::SCOPE_SINGLETON, ILanguageProvider::class, SymfonyLanguageProvider::class);

        $container->bindCallback(
            IIocContainer::SCOPE_SINGLETON,
            CacheItemPoolInterface::class,
            function () use ($container) {
                $repository = $container->get(Repository::class);
                $storage = $repository->get('dms.datastorage.path') . '/dms/cache';

                return new FilesystemCachePool(new Filesystem(new Local($storage)));
            }
        );

        $container->bindCallback(
            IIocContainer::SCOPE_SINGLETON,
            IEventDispatcher::class,
            function () {
                /**
                 * @var LaravelEventDispatcher $eventDispatcher
                 */
                $eventDispatcher = new LaravelEventDispatcher(new Dispatcher());

                return $eventDispatcher->inNamespace('dms::');
            }
        );

        $container->bindCallback(
            IIocContainer::SCOPE_SINGLETON,
            \Illuminate\Contracts\Cache\Store::class,
            function () use ($container) {
                return $container->get(\Illuminate\Cache\ArrayStore::class);
            }
        );

        $container->bindCallback(
            IIocContainer::SCOPE_SINGLETON,
            \Dms\Common\Structure\FileSystem\Directory::class,
            function () {
                return new \Dms\Common\Structure\FileSystem\Directory(dirname(__DIR__) . '/public');
            }
        );

        $container->bindCallback(
            IIocContainer::SCOPE_SINGLETON,
            \Dms\Web\Expressive\Document\DirectoryTree::class,
            function () use ($container) {
                $directory = $container->get(\Dms\Common\Structure\FileSystem\Directory::class);
                return new \Dms\Web\Expressive\Document\DirectoryTree($directory, [], []);
            }
        );

        $container->bindCallback(
            IIocContainer::SCOPE_SINGLETON,
            IConnection::class,
            function () {
                $config = new \Doctrine\DBAL\Configuration();

                $connectionParams = [
                'url' => getenv('driver') . '://' . getenv('username') . ':' . getenv('password') . '@' . getenv('host') . '/' . getenv('database'),
                'driverOptions' => [
                    \PDO::MYSQL_ATTR_FOUND_ROWS => true
                ],
                ];
                $connection = \Doctrine\DBAL\DriverManager::getConnection($connectionParams, $config);

                return new \Dms\Core\Persistence\Db\Doctrine\DoctrineConnection($connection);
            }
        );

        $container->bindCallback(
            IIocContainer::SCOPE_SINGLETON,
            OauthProviderCollection::class,
            function () use ($container) {
                $providers = [];

                foreach ($container->get(Repository::class)->get('dms.auth.oauth-providers', []) as $providerConfig) {
                    /**
                     * @var OauthProvider $providerClass
                     */
                    $providerClass = $providerConfig['provider'];
                    $providers[]   = $providerClass::fromConfiguration($providerConfig);
                }

                return new OauthProviderCollection($providers);
            }
        );

        $container->bind(IIocContainer::SCOPE_SINGLETON, IClock::class, DateTimeClock::class);
        $container->bind(IIocContainer::SCOPE_SINGLETON, ITemporaryFileService::class, TemporaryFileService::class);
        $container->bind(IIocContainer::SCOPE_SINGLETON, ITemporaryFileRepository::class, TemporaryFileRepository::class);
        $container->bindCallback(
            IIocContainer::SCOPE_SINGLETON,
            IApplicationDirectories::class,
            function () use ($container) {
                $repository = $container->get(Repository::class);

                return new LaravelApplicationDirectories(
                    $repository->get('dms.base.path'),
                    $repository->get('dms.storage.path'),
                    $repository->get('dms.public.path')
                );
            }
        );

        $container->bindCallback(
            IIocContainer::SCOPE_SINGLETON,
            ViewFactory::class,
            function () use ($container) {
                $factory = new BladeViewFactory();

                $viewFactory = $factory->createViewFactory($container);

                $viewFactory->composer('dms::template.default', DmsNavigationViewComposer::class);
                $viewFactory->composer('dms::dashboard', DmsNavigationViewComposer::class);
                $viewFactory->setContainer($container->getIlluminateContainer());

                return $viewFactory;
            }
        );

        $container->bind(IIocContainer::SCOPE_SINGLETON, Illuminate\Contracts\Events\Dispatcher::class, Illuminate\Events\Dispatcher::class);

        $container->bindCallback(
            IIocContainer::SCOPE_SINGLETON,
            ActionInputTransformerCollection::class,
            function () use ($container) {
                return new ActionInputTransformerCollection(
                    $this->makeAll(
                        $container,
                        $container->get(Repository::class)->get('dms.services.actions.input-transformers')
                    )
                );
            }
        );

        $container->bindCallback(
            IIocContainer::SCOPE_SINGLETON,
            ActionResultHandlerCollection::class,
            function () use ($container) {
                return new ActionResultHandlerCollection(
                    $this->makeAll(
                        $container,
                        $container->get(Repository::class)->get('dms.services.actions.result-handlers')
                    )
                );
            }
        );

        $container->bindCallback(
            IIocContainer::SCOPE_SINGLETON,
            ActionExceptionHandlerCollection::class,
            function () use ($container) {
                return new ActionExceptionHandlerCollection(
                    $this->makeAll(
                        $container,
                        $container->get(Repository::class)->get('dms.services.actions.exception-handlers')
                    )
                );
            }
        );

        $container->bindCallback(
            IIocContainer::SCOPE_SINGLETON,
            PublicFileModule::class,
            function () use ($container) {
                return new PublicFileModule(
                    DirectoryTree::from($container->get(Repository::class)->get('dms.storage.public-files.dir')),
                    DirectoryTree::from($container->get(Repository::class)->get('dms.storage.trashed-files.dir')),
                    $container->get(IAuthSystem::class)
                );
            }
        );

        $container->bindCallback(
            IIocContainer::SCOPE_SINGLETON,
            Flap::class,
            function () use ($container) {
                $cache = new FilesystemCache(dirname(__DIR__) . '/data/cache');
                $storage = new DoctrineCacheAdapter($cache);
                $flaps = new Flaps($storage);
                $flap = $flaps->__get('login-throttle');
                $flap->pushThrottlingStrategy(
                    new LeakyBucketStrategy(3, '15s')
                );
                $flap->setViolationHandler(new PassiveViolationHandler);

                return $flap;
            }
        );

        $container->bindCallback(IIocContainer::SCOPE_SINGLETON, AntiCSRF::class, function ($container) {
            $session = $container->get(Session::class);
            if (! $session->isStarted()) {
                $session->start();
            }

            return new AntiCSRF();
        });

        $container->bind(IIocContainer::SCOPE_SINGLETON, VerifyCsrfToken::class, VerifyCsrfToken::class);
    }


    private function makeAll(IIocContainer $container, array $services)
    {
        foreach ($services as $key => $service) {
            $services[$key] = $container->get($service);
        }
        return $services;
    }
}
