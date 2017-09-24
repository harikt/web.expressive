<?php declare(strict_types=1);

namespace Dms\Web\Expressive\Document;

use Dms\Common\Structure\FileSystem\Directory;
use Dms\Common\Structure\FileSystem\File;
use Dms\Common\Structure\FileSystem\Image;
use Dms\Common\Structure\FileSystem\PathHelper;
use Dms\Core\Model\Object\ClassDefinition;
use Dms\Core\Model\Object\ValueObject;
use Dms\Core\Model\ValueObjectCollection;

/**
 * The directory tree object.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class DirectoryTree extends ValueObject
{
    /**
     * @var Directory
     */
    public $directory;

    /**
     * @var ValueObjectCollection|File[]
     */
    public $files;

    /**
     * @var ValueObjectCollection|DirectoryTree[]
     */
    public $subDirectories;

    /**
     * DirectoryTree constructor.
     *
     * @param Directory                             $directory
     * @param ValueObjectCollection|File[]          $files
     * @param ValueObjectCollection|DirectoryTree[] $subDirectories
     */
    public function __construct(Directory $directory, array $files, array $subDirectories)
    {
        parent::__construct();
        $this->directory      = $directory;
        $this->files          = File::collection($files);
        $this->subDirectories = DirectoryTree::collection($subDirectories);
    }

    /**
     * @return string
     */
    public function getName() : string
    {
        return $this->directory->getName();
    }

    /**
     * @param string $rootDirectory
     *
     * @return DirectoryTree
     */
    public static function from(string $rootDirectory) : DirectoryTree
    {
        $directory      = new Directory($rootDirectory);
        $subDirectories = [];
        $files          = [];

        foreach (@scandir($rootDirectory) ?: [] as $name) {
            if ($name === '.' || $name === '..') {
                continue;
            }

            $fullPath = PathHelper::combine($rootDirectory, $name);
            if (is_dir($fullPath)) {
                $subDirectories[] = self::from($fullPath);
            } else {
                $isImage = @getimagesize($fullPath) !== false;
                $files[$name] = $isImage ? new Image($fullPath, $name) : new File($fullPath, $name);
            }
        }

        ksort($files, SORT_STRING);

        return new self($directory, $files, $subDirectories);
    }

    /**
     * Defines the structure of this class.
     *
     * @param ClassDefinition $class
     */
    protected function define(ClassDefinition $class)
    {
        $class->property($this->directory)->asObject(Directory::class);
        $class->property($this->files)->asType(File::collectionType());
        $class->property($this->subDirectories)->asType(DirectoryTree::collectionType());
    }

    /**
     * @return ValueObjectCollection|File[]
     */
    public function getAllFiles() : ValueObjectCollection
    {
        return File::collection($this->getAllFilesAsArray());
    }

    /**
     * @return File[]
     */
    protected function getAllFilesAsArray() : array
    {
        $allFiles = $this->files->asArray();

        foreach ($this->subDirectories as $subDirectory) {
            foreach ($subDirectory->getAllFilesAsArray() as $file) {
                $allFiles[] = $file;
            }
        }

        return $allFiles;
    }

    /**
     * @return ValueObjectCollection|Directory[]
     */
    public function getAllDirectories() : ValueObjectCollection
    {
        return Directory::collection($this->getAllDirectoriesAsArray());
    }

    /**
     * @return Directory[]
     */
    protected function getAllDirectoriesAsArray() : array
    {
        $directories = [];

        foreach ($this->subDirectories->asArray() as $subDirectory) {
            $directories[] = $subDirectory->directory;
        }

        foreach ($this->subDirectories as $subDirectory) {
            foreach ($subDirectory->getAllDirectoriesAsArray() as $file) {
                $directories[] = $file;
            }
        }

        return $directories;
    }
}
