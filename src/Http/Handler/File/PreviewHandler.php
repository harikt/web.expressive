<?php declare(strict_types=1);

namespace Dms\Web\Expressive\Http\Handler\File;

use Dms\Core\Auth\IAuthSystem;
use Dms\Core\ICms;
use Dms\Core\Model\EntityNotFoundException;
use Dms\Web\Expressive\File\ITemporaryFileService;
use Dms\Web\Expressive\Http\Handler\DmsHandler;
use Illuminate\Contracts\Config\Repository;
use Psr\Http\Server\MiddlewareInterface as ServerMiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Diactoros\Response;
use Zend\Expressive\Router\RouterInterface;
use Zend\Expressive\Template\TemplateRendererInterface;

/**
 * The file preview controller.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class PreviewHandler extends DmsHandler implements RequestHandlerInterface
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
     * PreviewHandler constructor.
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

        return $this->abort($request, 404);
    }
}
