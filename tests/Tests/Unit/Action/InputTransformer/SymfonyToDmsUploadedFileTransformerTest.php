<?php

namespace Dms\Web\Laravel\Tests\Unit\Action\InputTransformer;

use Dms\Common\Structure\FileSystem\UploadedFile;
use Dms\Common\Structure\FileSystem\UploadedImage;
use Dms\Web\Laravel\Action\IActionInputTransformer;
use Dms\Web\Laravel\Action\InputTransformer\SymfonyToDmsUploadedFileTransformer;
use Symfony\Component\HttpFoundation\File\UploadedFile as SymfonyFile;

/**
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class SymfonyToDmsUploadedFileTransformerTest extends ActionInputTransformerTest
{
    protected function buildInputTransformer() : IActionInputTransformer
    {
        return new SymfonyToDmsUploadedFileTransformer();
    }

    public function transformationTestCases() : array
    {
        return [
            [$this->mockAction(), ['test' => 'string'], ['test' => 'string']],
            [
                $this->mockAction(),
                ['file' => new SymfonyFile(__FILE__, 'name', 'text/html')],
                ['file' => new UploadedFile(__FILE__, UPLOAD_ERR_OK, 'name', 'text/html')],
            ],
            [
                $this->mockAction(),
                ['inner' => ['file' => new SymfonyFile(__FILE__, 'name', 'image/png', null, UPLOAD_ERR_EXTENSION)]],
                ['inner' => ['file' => new UploadedImage(__FILE__, UPLOAD_ERR_EXTENSION, 'name', 'image/png')]],
            ],
        ];
    }
}