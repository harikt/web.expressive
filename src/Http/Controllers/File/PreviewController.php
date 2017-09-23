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
class PreviewController implements ServerMiddlewareInterface
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
     * FileController constructor.
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

    public function process(ServerRequestInterface $request, DelegateInterface $delegate)
    {
        $token = $request->getAttribute('token');
        $response = new Response();
        try {
            $file = $this->tempFileService->getTempFile($token);

            $isImage = @getimagesize($file->getFile()->getFullPath()) !== false;

            if ($isImage) {
                $response = $response->withHeader('Content-Type', 'application/octet-stream')
                    ->withHeader('Content-Disposition', "attachment; filename=\"{$file->getFile()->getClientFileNameWithFallback()}\"");
                $response->getBody()->write(file_get_contents($file->getFile()->getFullPath()));

                return $response;
            }
        } catch (EntityNotFoundException $e) {
        }

        return DmsError::abort($request, 404);
    }
}
