<?php declare(strict_types=1);

namespace Dms\Web\Expressive\Http\Controllers;

use Interop\Http\Server\RequestHandlerInterface;
use Interop\Http\Server\MiddlewareInterface as ServerMiddlewareInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * The error controller.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class ErrorController extends DmsController implements ServerMiddlewareInterface
{
    public function process(ServerRequestInterface $request, RequestHandlerInterface $delegate)
    {
        return $this->abort($request, 404);
    }
}
