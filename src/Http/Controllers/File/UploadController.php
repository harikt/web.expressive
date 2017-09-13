<?php declare(strict_types=1);

namespace Dms\Web\Expressive\Http\Controllers\File;

use Dms\Core\Auth\IAuthSystem;
use Dms\Common\Structure\FileSystem\InMemoryFile;
use Dms\Common\Structure\FileSystem\UploadedFileFactory;
use Dms\Core\ICms;
use Dms\Core\Model\EntityNotFoundException;
use Dms\Web\Expressive\Error\DmsError;
use Dms\Web\Expressive\File\ITemporaryFileService;
use Dms\Web\Expressive\Http\Controllers\DmsController;
use Illuminate\Contracts\Config\Repository;
use Illuminate\Cookie\CookieJar;
use Psr\Http\Message\ServerRequestInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Zend\Diactoros\Response\JsonResponse;
use Interop\Http\ServerMiddleware\DelegateInterface;
use Interop\Http\ServerMiddleware\MiddlewareInterface as ServerMiddlewareInterface;
use Zend\Expressive\Template\TemplateRendererInterface;

/**
 * The file upload/download controller.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class UploadController extends DmsController implements ServerMiddlewareInterface
{
    /**
     * @var ITemporaryFileService
     */
    protected $tempFileService;

    /**
     * @var Repository
     */
    protected $config;

    /**
     * FileController constructor.
     *
     * @param ICms                  $cms
     * @param ITemporaryFileService $tempFileService
     * @param Repository            $config
     */
    public function __construct(
        ICms $cms,
        IAuthSystem $auth,
        ITemporaryFileService $tempFileService,
        Repository $config
    ) {
        parent::__construct($cms, $auth);

        $this->tempFileService = $tempFileService;
        $this->config          = $config;
    }

    public function process(ServerRequestInterface $request, DelegateInterface $delegate)
    {
        $tokens = [];

        /** @var UploadedFile $file */
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

        return new JsonResponse([
            'message' => 'The files were successfully uploaded',
            'tokens'  => $tokens,
        ]);
    }
}
