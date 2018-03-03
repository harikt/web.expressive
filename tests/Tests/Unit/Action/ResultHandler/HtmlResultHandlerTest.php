<?php

namespace Dms\Web\Expressive\Tests\Unit\Action\ResultHandler;

use Dms\Common\Structure\Web\Html;
use Dms\Web\Expressive\Action\IActionResultHandler;
use Dms\Web\Expressive\Action\ResultHandler\HtmlResultHandler;
use Zend\Diactoros\Response\JsonResponse;

/**
 * @author Hari KT <kthari85@gmail.com>
 */
class HtmlResultHandlerTest extends ResultHandlerTest
{
    protected function buildHandler() : IActionResultHandler
    {
        return new HtmlResultHandler();
    }

    public function resultHandlingTests() : array
    {
        return [
            [
                $this->mockAction(),
                new Html('Hello World'),
                new JsonResponse([
                    'content'       => 'Hello World',
                    'iframe'        => true,
                ]),
            ],
        ];
    }

    public function unhandleableResultTests() : array
    {
        return [
            [$this->mockAction(\stdClass::class), new \stdClass()],
        ];
    }
}
