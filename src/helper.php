<?php
use Aura\Session\Session;
use Dms\Web\Expressive\Ioc\LaravelIocContainer;
use Illuminate\Config\Repository;
use Illuminate\Container\Container;
use Illuminate\Contracts\Auth\Factory as AuthFactory;
use Illuminate\Contracts\View\Factory as ViewFactory;
use Zend\Expressive\Router\RouterInterface;

if (! function_exists('abort')) {
    /**
     * Throw an HttpException with the given data.
     *
     * @param  int    $code
     * @param  string $message
     * @param  array  $headers
     * @return void
     *
     * @throws \Symfony\Component\HttpKernel\Exception\HttpException
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    function abort($code, $message = '', array $headers = [])
    {
        app()->abort($code, $message, $headers);
    }
}

if (! function_exists('app')) {
    /**
     * Get the available container instance.
     *
     * @param  string                                   $abstract
     * @param  array                                    $parameters
     * @return mixed|\Illuminate\Foundation\Application
     */
    function app($abstract = null, array $parameters = [])
    {
        if (is_null($abstract)) {
            return LaravelIocContainer::getInstance();
        }

        return empty($parameters)
            ? LaravelIocContainer::getInstance()->get($abstract)
            : LaravelIocContainer::getInstance()->makeWith($abstract, $parameters);
    }
}

if (! function_exists('app_path')) {
    /**
     * Get the path to the application folder.
     *
     * @param  string $path
     * @return string
     */
    function app_path($path = '')
    {
        return app('path').($path ? DIRECTORY_SEPARATOR.$path : $path);
    }
}

// if (! function_exists('auth')) {
//     /**
//      * Get the available auth instance.
//      *
//      * @param  string|null                                                                                                  $guard
//      * @return \Illuminate\Contracts\Auth\Factory|\Illuminate\Contracts\Auth\Guard|\Illuminate\Contracts\Auth\StatefulGuard
//      */
//     function auth($guard = null)
//     {
//         if (is_null($guard)) {
//             return app(AuthFactory::class);
//         } else {
//             return app(AuthFactory::class)->guard($guard);
//         }
//     }
// }

// modified
if (! function_exists('config')) {
    /**
     * Get / set the specified configuration value.
     *
     * If an array is passed as the key, we will assume you want to set an array of values.
     *
     * @param  array|string $key
     * @param  mixed        $default
     * @return mixed
     */
    function config($key = null, $default = null)
    {
        if (is_null($key)) {
            return app(Repository::class);
        }

        if (is_array($key)) {
            return app(Repository::class)->set($key);
        }

        return app(Repository::class)->get($key, $default);
    }
}

if (! function_exists('config_path')) {
    /**
     * Get the configuration path.
     *
     * @param  string $path
     * @return string
     */
    function config_path($path = '')
    {
        return app()->make('path.config').($path ? DIRECTORY_SEPARATOR.$path : $path);
    }
}

if (! function_exists('csrf_token')) {
    /**
     * Get the CSRF token value.
     *
     * @return string
     *
     * @throws \RuntimeException
     */
    function csrf_token()
    {
        $session = app(Session::class);

        return $session->getCsrfToken()->getValue();
    }
}

// if (! function_exists('elixir')) {
//     /**
//      * Get the path to a versioned Elixir file.
//      *
//      * @param  string $file
//      * @param  string $buildDirectory
//      * @return string
//      *
//      * @throws \InvalidArgumentException
//      */
//     function elixir($file, $buildDirectory = 'build')
//     {
//         static $manifest = [];
//         static $manifestPath;
//
//         if (empty($manifest) || $manifestPath !== $buildDirectory) {
//             $path = public_path($buildDirectory.'/rev-manifest.json');
//
//             if (file_exists($path)) {
//                 $manifest = json_decode(file_get_contents($path), true);
//                 $manifestPath = $buildDirectory;
//             }
//         }
//
//         $file = ltrim($file, '/');
//
//         if (isset($manifest[$file])) {
//             return '/'.trim($buildDirectory.'/'.$manifest[$file], '/');
//         }
//
//         $unversioned = public_path($file);
//
//         if (file_exists($unversioned)) {
//             return '/'.trim($file, '/');
//         }
//
//         throw new InvalidArgumentException("File {$file} not defined in asset manifest.");
//     }
// }

if (! function_exists('public_path')) {
    /**
     * Get the path to the public folder.
     *
     * @param  string $path
     * @return string
     */
    function public_path($path = '')
    {
        return app()->make('path.public').($path ? DIRECTORY_SEPARATOR.ltrim($path, DIRECTORY_SEPARATOR) : $path);
    }
}

if (! function_exists('route')) {
    /**
     * Generate the URL to a named route.
     *
     * @param  string $name
     * @param  array  $parameters
     * @param  bool   $absolute
     * @return string
     */
    function route($name, $parameters = [], $absolute = [])
    {
        // if (in_array($name, ['dms::package.module.dashboard'])) {
        // 	return "/" . $name;
        // }
        try {
            return app()
                ->get(RouterInterface::class)
                ->generateUri($name, $parameters, []);
        } catch (\Exception $e) {
            // var_dump($name, $parameters);
            // exit;
            throw $e;
        }
    }
}

if (! function_exists('session')) {
    /**
     * Get / set the specified session value.
     *
     * If an array is passed as the key, we will assume you want to set an array of values.
     *
     * @param  array|string $key
     * @param  mixed        $default
     * @return mixed
     */
    function session($key = null, $default = null)
    {
        // return "session" . var_export($key, true) . var_export($default, true);
        if (is_null($key)) {
            return app(Session::class)->getSegment('session');
        }

        if (is_array($key)) {
            $segment = app(Session::class)->getSegment('session');

            foreach ($key as $k => $value) {
                $segment->set($k, $value);
            }

            return true;
        }

        $segment = app(Session::class)->getSegment('session');

        return $segment->get($key, $default);
    }
}

// if (! function_exists('storage_path')) {
//     /**
//      * Get the path to the storage folder.
//      *
//      * @param  string $path
//      * @return string
//      */
//     function storage_path($path = '')
//     {
//         return app('path.storage').($path ? DIRECTORY_SEPARATOR.$path : $path);
//     }
// }

if (! function_exists('view')) {
    /**
     * Get the evaluated view contents for the given view.
     *
     * @param  string                                                   $view
     * @param  array                                                    $data
     * @param  array                                                    $mergeData
     * @return \Illuminate\View\View|\Illuminate\Contracts\View\Factory
     */
    function view($view = null, $data = [], $mergeData = [])
    {
        $factory = app(ViewFactory::class);

        if (func_num_args() === 0) {
            return $factory;
        }

        return $factory->make($view, $data, $mergeData);
    }
}

if (!function_exists('asset_file_url')) {
    /**
     * Generates an asset URL from a file stored within the public directory
     *
     * @param \Dms\Core\File\IFile $file
     *
     * @return string
     * @throws \Dms\Core\Exception\InvalidArgumentException
     */
    function asset_file_url(\Dms\Core\File\IFile $file) : string
    {
        $s3Prefixes = ['s3://', 's3-dms://'];

        foreach ($s3Prefixes as $s3Prefix) {
            $isFileOnS3 = starts_with($file->getFullPath(), $s3Prefix);

            if ($isFileOnS3) {
                list($bucketName, $objectKey) = explode('/', substr($file->getFullPath(), strlen($s3Prefix)), 2);

                return 'https://' . $bucketName . '.s3.amazonaws.com/' . $objectKey;
            }
        }

        $filePath   = $file->exists() ? realpath($file->getFullPath()) : $file->getFullPath();
        $publicPath = realpath(public_path()) ?: public_path();

        if (!starts_with($filePath, $publicPath)) {
            throw \Dms\Core\Exception\InvalidArgumentException::format(
                'Invalid call to %s: the supplied file must be located within the application public directory \'%s\', \'%s\' given',
                __FUNCTION__,
                $publicPath,
                $filePath
            );
        }

        $relativePath = substr($filePath, strlen($publicPath));

        return str_replace(DIRECTORY_SEPARATOR, '/', $relativePath);
    }
}
