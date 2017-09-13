<?php declare(strict_types=1);

namespace Dms\Web\Expressive\Error;

use Illuminate\Http\Exceptions\HttpResponseException;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Diactoros\Response;

/**
 * The dms error pages.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class DmsError
{
    /**
     * @param int    $statusCode
     * @param string $message
     *
     * @return void
     */
    public static function abort(ServerRequestInterface $request, int $statusCode, string $message = '')
    {
        if ('XMLHttpRequest' == $request->getHeaderLine('X-Requested-With')) {
            $response = new Response('php://memory', $statusCode);
            $response->getBody()->write($message);
            return $response;
        }

        throw new HttpResponseException(response(self::renderErrorView($statusCode), $statusCode));
    }

    /**
     * @param int $statusCode
     *
     * @return mixed
     * @throws \Exception
     * @throws \Throwable
     */
    protected static function renderErrorView(int $statusCode)
    {
        return view('dms::errors.' . $statusCode)
            ->with('title', $statusCode)
            ->with('user', request()->user())
            ->with('pageTitle', $statusCode)
            ->with('finalBreadcrumb', $statusCode)
            ->render();
    }
}
