<?php declare(strict_types=1);

namespace Dms\Web\Expressive\Document;

use Dms\Common\Structure\FileSystem\File;
use Dms\Core\Model\Object\ClassDefinition;
use Dms\Core\Model\Object\Entity;

/**
 * The public file entity.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class PublicFile extends Entity
{
    /**
     * @var File
     */
    public $file;

    /**
     * PublicFile constructor.
     *
     * @param File $file
     */
    public function __construct(File $file)
    {
        parent::__construct();
        $this->file = $file;
    }

    /**
     * Defines the structure of this entity.
     *
     * @param ClassDefinition $class
     */
    protected function defineEntity(ClassDefinition $class)
    {
        $class->property($this->file)->asObject(File::class);
    }
}
