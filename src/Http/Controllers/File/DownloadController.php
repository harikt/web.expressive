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
use Interop\Http\ServerMiddleware\DelegateInterface;
use Interop\Http\ServerMiddleware\MiddlewareInterface as ServerMiddlewareInterface;
use Psr\Http\Message\ServerRequestInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Zend\Diactoros\Response;
use Zend\Expressive\Template\TemplateRendererInterface;

/**
 * The file upload/download controller.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class DownloadController extends DmsController implements ServerMiddlewareInterface
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

    // public function download($token, CookieJar $cookieJar)
    public function process(ServerRequestInterface $request, DelegateInterface $delegate)
    {
        $token = $request->getAttribute('token');
        try {
            $file = $this->tempFileService->getTempFile($token)->getFile();

            // $cookieJar->queue('file-download-' . $token, true, 60, null, null, false, false);

            $response = new Response();

            if ($file instanceof InMemoryFile) {
                $response = $response->withHeader('Content-Type', 'application/octet-stream')
                    ->withHeader('Content-Disposition', "attachment; filename=\"{$file->getName()}\"");
                $response->getBody()->write(file_get_contents($file->getFullPath()));

                return $response;
            } else {
                $response = $response->withHeader('Content-Type', 'application/octet-stream')
                    ->withHeader('Content-Disposition', "attachment; filename=\"{$file->getClientFileNameWithFallback()}\"");

                $response->getBody()->write(file_get_contents($file->getFullPath()));

                return $response;
            }
        } catch (EntityNotFoundException $e) {
            DmsError::abort($request, 404);
        }
    }
}
