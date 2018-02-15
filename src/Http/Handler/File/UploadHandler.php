<?php declare(strict_types=1);

namespace Dms\Web\Expressive\Http\Handler\File;

use Dms\Common\Structure\FileSystem\UploadedFileFactory;
use Dms\Core\ICms;
use Dms\Web\Expressive\File\ITemporaryFileService;
use Illuminate\Contracts\Config\Repository;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Zend\Diactoros\Response\JsonResponse;

/**
 * The file upload controller.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class UploadHandler implements RequestHandlerInterface
{
    protected $cms;

    /**
     * @var ITemporaryFileService
     */
    protected $tempFileService;

    /**
     * @var Repository
     */
    protected $config;

    /**
     * FileHandler constructor.
     *
     * @param ICms                  $cms
     * @param ITemporaryFileService $tempFileService
     * @param Repository            $config
     */
    public function __construct(
        ICms $cms,
        ITemporaryFileService $tempFileService,
        Repository $config
    ) {
        $this->cms = $cms;
        $this->tempFileService = $tempFileService;
        $this->config          = $config;
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $tokens = [];

        /**
 * @var UploadedFile $file
*/
        foreach ($request->getUploadedFiles() as $key => $file) {
            $tokens[$key] = $this->tempFileService->storeTempFile(
                UploadedFileFactory::build(
                    $_FILES[$key]['tmp_name'],
                    $file->getError(),
                    $file->getClientFilename(),
                    $file->getClientMediaType()
                ),
                $this->config->get('dms.storage.temp-files.upload-expiry')
            )->getToken();
        }

        return new JsonResponse(
            [
            'message' => 'The files were successfully uploaded',
            'tokens'  => $tokens,
            ]
        );
    }
}
