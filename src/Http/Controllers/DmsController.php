<?php declare(strict_types=1);

namespace Dms\Web\Expressive\Http\Controllers;

use Dms\Core\Auth\IAuthSystem;
use Dms\Core\ICms;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Psr\Http\Message\ServerRequestInterface;

/**
 * The base dms controller.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class DmsController
{
    // use ValidatesRequests;

    /**
     * @var ICms
     */
    protected $cms;

    /**
     * @var IAuthSystem
     */
    protected $auth;

    /**
     * DmsController constructor.
     *
     * @param ICms $cms
     * @param IAuthSystem $auth
     *
     */
    public function __construct(ICms $cms, IAuthSystem $auth)
    {
        $this->cms  = $cms;
        $this->auth = $auth;

        // $this->cms = $cms;
        // $this->auth = app()->make(IAuthSystem::class);
        //
        // $this->middleware(function ($request, $next) {
        //     $this->loadSharedViewVariables(request());
        //
        //     return $next($request);
        // });
    }

    /**
     * @param ServerRequestInterface $request
     */
    protected function loadSharedViewVariables(ServerRequestInterface $request)
    {
        // 	$params = [
        //      'cms'   => $this->cms,
        //      'user'  => $this->auth->isAuthenticated() ? $this->auth->getAuthenticatedUser() : null,
        //      'title' => 'DMS {' . $request->getServerParams()['SERVER_NAME'] . '}',
        //  ];
        // 	foreach ($params as $param => $value) {
        // 		$this->template->addDefaultParam(TemplateRendererInterface::TEMPLATE_ALL,
        // 			$param,
        //          $value
        //      );
        // 	}

        view()->share([
            'cms'   => $this->cms,
            'user'  => $this->auth->isAuthenticated() ? $this->auth->getAuthenticatedUser() : null,
            'title' => 'DMS {' . $request->getServerParams()['SERVER_NAME'] . '}',
            'request' => $request,
        ]);
    }
}
