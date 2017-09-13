<?php declare(strict_types=1);

namespace Dms\Web\Laravel\Tests\Integration\Scaffold\Fixture\ValueObject\Domain;

use Dms\Common\Structure\FileSystem\File;
use Dms\Common\Structure\FileSystem\Image;
use Dms\Core\Model\Object\ClassDefinition;
use Dms\Core\Model\Object\ValueObject;

/**
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class TestFileValueObject extends ValueObject
{
    const FILE = 'file';
    const NULLABLE_FILE = 'nullableFile';
    const IMAGE = 'image';
    const NULLABLE_IMAGE = 'nullableImage';

    /**
     * @var File
     */
    public $file;

    /**
     * @var File|null
     */
    public $nullableFile;

    /**
     * @var Image
     */
    public $image;

    /**
     * @var Image|null
     */
    public $nullableImage;

    /**
     * Defines the structure of this class.
     *
     * @param ClassDefinition $class
     */
    protected function define(ClassDefinition $class)
    {
        $class->property($this->file)->asObject(File::class);

        $class->property($this->nullableFile)->nullable()->asObject(File::class);

        $class->property($this->image)->asObject(Image::class);

        $class->property($this->nullableImage)->nullable()->asObject(Image::class);
    }
}