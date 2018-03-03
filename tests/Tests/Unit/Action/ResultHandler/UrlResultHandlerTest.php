<?php

namespace Dms\Web\Expressive\Tests\Unit\Action\ResultHandler;

use Dms\Common\Structure\Web\Url;
use Dms\Web\Expressive\Action\IActionResultHandler;
use Dms\Web\Expressive\Action\ResultHandler\UrlResultHandler;
use Zend\Diactoros\Response\JsonResponse;

/**
 * @author Hari KT <kthari85@gmail.com>
 */
class UrlResultHandlerTest extends ResultHandlerTest
{
    protected function buildHandler() : IActionResultHandler
    {
        return new UrlResultHandler();
    }

    public function resultHandlingTests() : array
    {
        $url = new Url("http://harikt.com");

        return [
            [$this->mockAction(), $url, new JsonResponse([
                'redirect' => $url->asString(),
            ])],
        ];
    }

    public function unhandleableResultTests() : array
    {
        return [
            [$this->mockAction(\stdClass::class), new \stdClass()],
        ];
    }
}
