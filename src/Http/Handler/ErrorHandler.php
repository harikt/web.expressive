<?php declare(strict_types=1);

namespace Dms\Web\Expressive\Http\Handler;

use Psr\Http\Server\MiddlewareInterface as ServerMiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * The error controller.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class ErrorHandler extends DmsHandler implements RequestHandlerInterface
{
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        return $this->abort($request, 404);
    }
}
