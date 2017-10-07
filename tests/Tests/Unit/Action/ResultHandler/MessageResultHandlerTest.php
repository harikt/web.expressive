<?php

namespace Dms\Web\Expressive\Tests\Unit\Action\ResultHandler;

use Dms\Core\Language\Message;
use Dms\Web\Expressive\Action\IActionResultHandler;
use Dms\Web\Expressive\Action\ResultHandler\MessageResultHandler;
use Dms\Web\Expressive\Tests\Mock\Language\MockLanguageProvider;
use Zend\Diactoros\Response\JsonResponse;

/**
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class MessageResultHandlerTest extends ResultHandlerTest
{
    protected function buildHandler() : IActionResultHandler
    {
        return new MessageResultHandler(new MockLanguageProvider());
    }

    public function resultHandlingTests() : array
    {
        return [
            [$this->mockAction(), new Message('id', ['param' => 'val']), new JsonResponse(['message' => 'id:[param=val]'])],
        ];
    }

    public function unhandleableResultTests() : array
    {
        return [
            [$this->mockAction(\stdClass::class), new \stdClass()],
        ];
    }
}
