<?php declare(strict_types=1);

namespace Dms\Web\Laravel\Tests\Integration\Scaffold\Fixture\ValueObjectCollection\Domain;

use Dms\Common\Structure\FileSystem\File;
use Dms\Common\Structure\FileSystem\Image;
use Dms\Core\Model\Object\ClassDefinition;
use Dms\Core\Model\Object\ValueObject;
use Dms\Core\Model\ValueObjectCollection;

/**
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class TestFileValueObject extends ValueObject
{
    const FILE = 'file';
    const IMAGE = 'image';

    /**
     * @var ValueObjectCollection|File[]
     */
    public $file;

    /**
     * @var ValueObjectCollection|Image[]
     */
    public $image;


    /**
     * Defines the structure of this class.
     *
     * @param ClassDefinition $class
     */
    protected function define(ClassDefinition $class)
    {
        $class->property($this->file)->asType(File::collectionType());

        $class->property($this->image)->asType(Image::collectionType());
    }
}