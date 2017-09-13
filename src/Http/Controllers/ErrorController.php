<?php declare(strict_types=1);

namespace Dms\Web\Expressive\Http\Controllers;

use Dms\Web\Expressive\Error\DmsError;
use Interop\Http\ServerMiddleware\MiddlewareInterface as ServerMiddlewareInterface;

/**
 * The error controller.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class ErrorController extends DmsController implements ServerMiddlewareInterface
{
    public function notFound()
    {
        DmsError::abort($request, 404);
    }
}
