<?php declare(strict_types=1);

namespace Dms\Web\Expressive\Action\InputTransformer;

use Dms\Core\File\IImage;
use Dms\Core\File\UploadedFileProxy;
use Dms\Core\File\UploadedImageProxy;
use Dms\Core\Module\IParameterizedAction;
use Dms\Web\Expressive\Action\IActionInputTransformer;
use Dms\Web\Expressive\File\ITemporaryFileService;
use Dms\Web\Expressive\File\TemporaryFile;
use Dms\Web\Expressive\Http\ModuleContext;

/**
 * Transforms any temp uploaded files referenced by token to the equivalent uploaded file input.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class TempUploadedFileToUploadedFileTransformer implements IActionInputTransformer
{
    const TEMP_FILES_KEY = '__temp_uploaded_files';

    /**
     * @var ITemporaryFileService
     */
    protected $tempFileService;

    /**
     * TempUploadedFileToUploadedFileTransformer constructor.
     *
     * @param ITemporaryFileService $tempFileService
     */
    public function __construct(ITemporaryFileService $tempFileService)
    {
        $this->tempFileService = $tempFileService;
    }

    /**
     * Transforms for the supplied action.
     *
     * @param ModuleContext        $moduleContext
     * @param IParameterizedAction $action
     * @param array                $input
     *
     * @return array
     */
    public function transform(ModuleContext $moduleContext, IParameterizedAction $action, array $input) : array
    {
        if (isset($input[self::TEMP_FILES_KEY]) && is_array($input[self::TEMP_FILES_KEY])) {
            $uploadedTokenStructure = $input[self::TEMP_FILES_KEY];
            $uploadedFileTokens     = [];

            array_walk_recursive($uploadedTokenStructure, function ($token) use (&$uploadedFileTokens) {
                $uploadedFileTokens[] = $token;
            });

            $uploadedFiles = [];
            foreach ($this->tempFileService->getTempFiles($uploadedFileTokens) as $file) {
                $uploadedFiles[$file->getToken()] = $this->buildUploadedFileProxy($file);
            }

            $uploadedFileStructure = $uploadedTokenStructure;
            array_walk_recursive($uploadedFileStructure, function (&$token) use (&$uploadedFiles) {
                $token = $uploadedFiles[$token];
            });

            unset($input[self::TEMP_FILES_KEY]);

            return array_replace_recursive($input, $uploadedFileStructure);
        } else {
            return $input;
        }
    }

    /**
     * @param $tempFile
     *
     * @return UploadedFileProxy|UploadedImageProxy
     */
    protected function buildUploadedFileProxy(TemporaryFile $tempFile)
    {
        $file = $tempFile->getFile();

        $moveCallback = [$file, 'copyTo'];

        return $file instanceof IImage
            ? new UploadedImageProxy($file, $moveCallback)
            : new UploadedFileProxy($file, $moveCallback);
    }
}
