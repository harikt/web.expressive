<?php declare(strict_types=1);

namespace Dms\Web\Expressive\Error;

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
     * @return Response
     */
    public static function abort(ServerRequestInterface $request, int $statusCode, string $message = '')
    {
        if ('XMLHttpRequest' == $request->getHeaderLine('X-Requested-With')) {
            $response = new Response('php://memory', $statusCode);
            if ($statusCode == 401) {
                $message = json_encode(['redirect' => '/dms']);
            }
            $response->getBody()->write($message);
            return $response;
        }

        $response = new Response();
        $response = $response->withHeader('Status', $statusCode);
        $response->getBody()->write(self::renderErrorView($statusCode));

        return $response;
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
            // ->with('user', request()->user())
            ->with('pageTitle', $statusCode)
            ->with('finalBreadcrumb', $statusCode)
            ->render();
    }
}
