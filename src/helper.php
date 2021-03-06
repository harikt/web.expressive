<?php
use Aura\Session\Session;
use Dms\Web\Expressive\AppContainer;
use Illuminate\Container\Container;
use ParagonIE\AntiCSRF\AntiCSRF;

if (! function_exists('app')) {
    /**
     * Get the available container instance.
     *
     * @param  string $abstract
     * @param  array  $parameters
     * @return mixed|\Illuminate\Foundation\Application
     */
    function app($abstract = null, array $parameters = [])
    {
        if (is_null($abstract)) {
            return AppContainer::getInstance();
        }

        return empty($parameters)
            ? AppContainer::getInstance()->get($abstract)
            : AppContainer::getInstance()->makeWith($abstract, $parameters);
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
    function csrf_token($lock_to = '')
    {
        $csrf = app(AntiCSRF::class);
        return $csrf->insertToken($lock_to, false);
    }
}

if (! function_exists('js_csrf_token')) {
    /**
     * Get the CSRF token value.
     *
     * @return string
     *
     * @throws \RuntimeException
     */
    function js_csrf_token($lock_to = '')
    {
        $csrf = app(AntiCSRF::class);
        $tokens = $csrf->getTokenArray($lock_to);

        return $tokens['_CSRF_TOKEN'];
    }
}

// used by asset_file_url
if (! function_exists('public_path')) {
    /**
     * Get the path to the public folder.
     *
     * @param  string $path
     * @return string
     */
    function public_path($path = '')
    {
        return app()->get('path.public').($path ? DIRECTORY_SEPARATOR.ltrim($path, DIRECTORY_SEPARATOR) : $path);
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

if (!function_exists('asset_file_url')) {
    /**
     * Generates an asset URL from a file stored within the public directory
     *
     * @param \Dms\Core\File\IFile $file
     *
     * @return string
     * @throws \Dms\Core\Exception\InvalidArgumentException
     */
    function asset_file_url(\Dms\Core\File\IFile $file = null) : string
    {
        if (! $file) {
            return '';
        }

        $s3Prefixes = ['s3://', 's3-dms://'];

        foreach ($s3Prefixes as $s3Prefix) {
            if ($s3Prefix !== '' && substr($file->getFullPath(), 0, strlen($s3Prefix)) === (string) $s3Prefix) {
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
