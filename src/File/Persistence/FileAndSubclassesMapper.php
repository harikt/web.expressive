<?php declare(strict_types=1);

namespace Dms\Web\Expressive\File\Persistence;

use Dms\Common\Structure\FileSystem\File;
use Dms\Common\Structure\FileSystem\Image;
use Dms\Common\Structure\FileSystem\InMemoryFile;
use Dms\Common\Structure\FileSystem\Persistence\FileMapper as BaseFileMapper;
use Dms\Common\Structure\FileSystem\RelativePathCalculator;
use Dms\Common\Structure\FileSystem\UploadedFile;
use Dms\Common\Structure\FileSystem\UploadedImage;
use Dms\Core\Persistence\Db\Mapping\Definition\MapperDefinition;

/**
 * The file mapper.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class FileAndSubclassesMapper extends BaseFileMapper
{
    /**
     * FileAndSubclassesMapper constructor.
     *
     * @param string                      $filePathColumnName
     * @param string|null                 $clientFileNameColumnName
     * @param string|null                 $baseDirectoryPath
     * @param RelativePathCalculator|null $relativePathCalculator
     */
    public function __construct(
        string $filePathColumnName,
        string $clientFileNameColumnName = null,
        string $baseDirectoryPath = null,
        RelativePathCalculator $relativePathCalculator = null
    ) {
        parent::__construct($filePathColumnName, $clientFileNameColumnName, $baseDirectoryPath, $relativePathCalculator, true);
    }

    /**
     * Defines the entity mapper
     *
     * @param MapperDefinition $map
     *
     * @return void
     */
    protected function define(MapperDefinition $map)
    {
        parent::define($map);

        $map->subclass()->withTypeInColumnMap('type', [
            'uploaded-image' => UploadedImage::class,
            'uploaded-file'  => UploadedFile::class,
            'stored-image'   => Image::class,
            'in-memory'      => InMemoryFile::class,
            'stored-file'    => File::class,
        ]);
    }
}
