<?php declare(strict_types=1);

namespace Dms\Web\Expressive\File;

use Dms\Core\File\IUploadedFile;
use Dms\Common\Structure\FileSystem\UploadedImage;
use Dms\Common\Structure\FileSystem\UploadedFile;

/**
 * The uploaded file factory class.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class UploadedFileFactory
{
    /**
     * Builds a new uploaded file instance based on the given mime type.
     *
     * @param string      $fullPath
     * @param int         $uploadStatus
     * @param string|null $clientFileName
     * @param string|null $clientMimeType
     *
     * @return IUploadedFile
     */
    public static function build(string $fullPath, int $uploadStatus, string $clientFileName = null, string $clientMimeType = null) : \Dms\Core\File\IUploadedFile
    {
        if ($clientMimeType && stripos($clientMimeType, 'image') === 0) {
            return new UploadedImage(
                    $fullPath,
                    $uploadStatus,
                    $clientFileName,
                    $clientMimeType
            );
        } else {
            return new UploadedFile(
                    $fullPath,
                    $uploadStatus,
                    $clientFileName,
                    $clientMimeType
            );
        }
    }
}
