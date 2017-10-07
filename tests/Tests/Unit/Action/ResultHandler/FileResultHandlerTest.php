<?php

namespace Dms\Web\Expressive\Tests\Unit\Action\ResultHandler;

use Dms\Common\Structure\DateTime\DateTime;
use Dms\Common\Structure\FileSystem\File;
use Dms\Web\Expressive\Action\IActionResultHandler;
use Dms\Web\Expressive\Action\ResultHandler\FileResultHandler;
use Dms\Web\Expressive\File\ITemporaryFileService;
use Dms\Web\Expressive\File\TemporaryFile;
use Illuminate\Config\Repository;
use Zend\Diactoros\Response\JsonResponse;

/**
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class FileResultHandlerTest extends ResultHandlerTest
{
    protected function buildHandler() : IActionResultHandler
    {
        $tempFileService = $this->getMockForAbstractClass(ITemporaryFileService::class);

        $tempFileService
            ->method('storeTempFile')
            ->willReturn(new TemporaryFile('some-token', $this->mockFile(), new DateTime(new \DateTime())));

        $config = new Repository();
        $config->set('dms.storage.temp-files.download-expiry', 10);

        return new FileResultHandler($tempFileService, $config);
    }

    /**
     * @return File
     */
    protected function mockFile()
    {
        return new File(__FILE__, 'file-name');
    }

    public function resultHandlingTests() : array
    {
        return [
            [
                $this->mockAction(),
                $this->mockFile(),
                new JsonResponse([
                    'message' => 'The action was successfully executed',
                    'files'   => [
                        [
                            'name'  => 'file-name',
                            'token' => 'some-token',
                        ],
                    ],
                ]),
            ],
        ];
    }

    public function unhandleableResultTests() : array
    {
        return [
            [$this->mockAction(\stdClass::class), new \stdClass()],
        ];
    }
}
