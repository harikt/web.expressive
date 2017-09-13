<?php declare(strict_types=1);

namespace Dms\Web\Expressive\Http\Controllers;

use Dms\Core\Auth\IAuthSystem;
use Dms\Core\ICms;
use Dms\Web\Expressive\Http\ModuleContext;
use Dms\Web\Expressive\Renderer\Form\ActionFormRenderer;
use Dms\Web\Expressive\Util\StringHumanizer;
use Interop\Http\ServerMiddleware\MiddlewareInterface as ServerMiddlewareInterface;
use Psr\Http\Message\ServerRequestInterface;
use Illuminate\Routing\Router;

/**
 * The user controller
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class UserController extends DmsController implements ServerMiddlewareInterface
{
    /**
     * @var ModuleContext
     */
    protected $moduleContext;

    public function __construct(ICms $cms)
    {
        parent::__construct($cms, $auth);
    }


    public function showProfileForm()
    {
        $user = $this->auth->getAuthenticatedUser();

        return view('dms::auth.profile', ['user' => $user]);
    }

    public function updateUserProfile(ServerRequestInterface $request)
    {
        $this->validate($request, [
            'username' => ''
        ]);
    }

    public function showChangePasswordForm()
    {
    }

    public function updateUserPassword()
    {
    }
}
