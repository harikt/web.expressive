<?php declare(strict_types=1);

namespace Dms\Web\Expressive\File;

use Dms\Common\Structure\FileSystem\Directory;
use Dms\Common\Structure\FileSystem\IApplicationDirectories;

/**
 * The laravel application directories class.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class LaravelApplicationDirectories implements IApplicationDirectories
{
    /**
     * @var Directory
     */
    private $root;

    /**
     * @var Directory
     */
    private $privateStorage;

    /**
     * @var Directory
     */
    private $publicStorage;

    /**
     * LaravelApplicationDirectories constructor.
     */
    public function __construct(string $basePath, string $storagePath, string $publicPath)
    {
        $this->root           = new Directory($basePath);
        $this->privateStorage = new Directory($storagePath);
        $this->publicStorage  = new Directory($publicPath);
    }

    /**
     * Gets the root directory of the application.
     *
     * All source files, stored uploads etc should be within
     * this directory.
     *
     * @return Directory
     */
    public function getRootDirectory() : Directory
    {
        return $this->root;
    }

    /**
     * Gets the private storage directory of the application.
     *
     * All *private* stored uploads etc should be within this directory.
     *
     * @return Directory
     */
    public function getPrivateStorageDirectory() : Directory
    {
        return $this->privateStorage;
    }

    /**
     * Gets the public storage directory of the application.
     *
     * All *public* stored uploads etc should be within this directory.
     *
     * @return Directory
     */
    public function getPublicStorageDirectory() : Directory
    {
        return $this->publicStorage;
    }
}
