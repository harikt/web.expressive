<?php

namespace Dms\Web\Laravel\Tests\Unit\Action\InputTransformer;

use Dms\Common\Structure\DateTime\DateTime;
use Dms\Common\Structure\FileSystem\UploadedFile;
use Dms\Core\File\IFile;
use Dms\Core\File\UploadedFileProxy;
use Dms\Web\Laravel\Action\IActionInputTransformer;
use Dms\Web\Laravel\Action\InputTransformer\TempUploadedFileToUploadedFileTransformer;
use Dms\Web\Laravel\File\ITemporaryFileService;
use Dms\Web\Laravel\File\TemporaryFile;

/**
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class TempUploadedFileToUploadedFileTransformerTest extends ActionInputTransformerTest
{
    protected function buildInputTransformer() : IActionInputTransformer
    {
        $tempFileService = $this->getMockForAbstractClass(ITemporaryFileService::class);

        $tempFileService
            ->method('getTempFiles')
            ->willReturnCallback(function (array $tokens) {
                $files = [];

                foreach ($tokens as $token) {
                    $files[] = $this->mockTempFile($token);
                }

                return $files;
            });

        return new TempUploadedFileToUploadedFileTransformer($tempFileService);
    }

    protected function mockTempFile(string $token)
    {
        $file = new UploadedFile(__FILE__, UPLOAD_ERR_OK, $token);

        return new TemporaryFile($token, $file, new DateTime(new \DateTime()));
    }

    public function transformationTestCases() : array
    {
        $tempFilesKey = TempUploadedFileToUploadedFileTransformer::TEMP_FILES_KEY;

        return [
            [$this->mockAction(), ['test' => 'string'], ['test' => 'string']],
            [
                $this->mockAction(),
                [$tempFilesKey => ['file' => 'some-token']],
                ['file' => $this->mockFileProxyWithCopyCallback($this->mockTempFile('some-token')->getFile())],
            ],
            [
                $this->mockAction(),
                [$tempFilesKey => ['file' => 'some-token', 'inner' => ['one-file-token', 'another-file-token']]],
                [
                    'file'  => $this->mockFileProxyWithCopyCallback($this->mockTempFile('some-token')->getFile()),
                    'inner' => [
                        $this->mockFileProxyWithCopyCallback($this->mockTempFile('one-file-token')->getFile()),
                        $this->mockFileProxyWithCopyCallback($this->mockTempFile('another-file-token')->getFile())
                    ],
                ],
            ],
        ];
    }

    protected function mockFileProxyWithCopyCallback(IFile $file)
    {
        return new UploadedFileProxy($file, [$file, 'copyTo']);
    }
}