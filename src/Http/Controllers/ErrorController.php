<?php declare(strict_types=1);

namespace Dms\Web\Expressive\Http\Controllers;

use Dms\Web\Expressive\Error\DmsError;
use Interop\Http\ServerMiddleware\DelegateInterface;
use Interop\Http\ServerMiddleware\MiddlewareInterface as ServerMiddlewareInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * The error controller.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class ErrorController extends DmsController implements ServerMiddlewareInterface
{
    public function process(ServerRequestInterface $request, DelegateInterface $delegate)
    {
        DmsError::abort($request, 404);
    }
}
