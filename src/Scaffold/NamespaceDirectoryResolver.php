<?php declare(strict_types=1);

namespace Dms\Web\Expressive\Scaffold;

use Dms\Common\Structure\FileSystem\PathHelper;
use Dms\Core\Exception\InvalidArgumentException;

/**
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class NamespaceDirectoryResolver
{
    /**
     * @param string $namespace
     *
     * @return string
     * @throws InvalidArgumentException
     */
    public function getDirectoryFor(string $namespace) : string
    {
        $psr4Rules = require base_path('vendor/composer/autoload_psr4.php');
        $psr0Rules = require base_path('vendor/composer/autoload_namespaces.php');

        foreach ($psr4Rules as $ruleNamespace => list($directory)) {
            $ruleNamespace = rtrim($ruleNamespace, '\\');
            if (starts_with($namespace, $ruleNamespace)) {
                $path = PathHelper::combine($directory, substr($namespace, strlen($ruleNamespace)));
                break;
            }
        }

        foreach ($psr0Rules as $ruleNamespace => list($directory)) {
            $ruleNamespace = rtrim($ruleNamespace, '\\');
            if (starts_with($namespace, $ruleNamespace)) {
                $path = PathHelper::combine($directory, $namespace);
                break;
            }
        }

        if (isset($path)) {
            return rtrim($path, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
        }

        throw InvalidArgumentException::format('No directory could be found for namespace \'%s\'', $namespace);
    }
}