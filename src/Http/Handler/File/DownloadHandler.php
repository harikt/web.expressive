<?php declare(strict_types=1);

namespace Dms\Web\Expressive\Http\Handler\File;

use Dms\Common\Structure\FileSystem\InMemoryFile;
use Dms\Core\Auth\IAuthSystem;
use Dms\Core\ICms;
use Dms\Core\Model\EntityNotFoundException;
use Dms\Web\Expressive\File\ITemporaryFileService;
use Dms\Web\Expressive\Http\Handler\DmsHandler;
use Illuminate\Contracts\Config\Repository;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Zend\Diactoros\Response;
use Zend\Expressive\Router\RouterInterface;
use Zend\Expressive\Template\TemplateRendererInterface;

/**
 * The file download controller.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class DownloadHandler extends DmsHandler implements RequestHandlerInterface
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
     * DownloadHandler constructor.
     *
     * @param ICms                      $cms
     * @param IAuthSystem               $auth
     * @param TemplateRendererInterface $template
     * @param RouterInterface           $router
     * @param ITemporaryFileService     $tempFileService
     * @param Repository                $config
     */
    public function __construct(
        ICms $cms,
        IAuthSystem $auth,
        TemplateRendererInterface $template,
        RouterInterface $router,
        ITemporaryFileService $tempFileService,
        Repository $config
    ) {
        parent::__construct($cms, $auth, $template, $router);
        $this->tempFileService = $tempFileService;
        $this->config          = $config;
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $token = $request->getAttribute('token');
        try {
            $file = $this->tempFileService->getTempFile($token)->getFile();

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
            return $this->abort($request, 404);
        }
    }
}
