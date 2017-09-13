<?php

namespace Dms\Web\Laravel\Tests\Unit\Action\ResultHandler;

use Dms\Core\Language\Message;
use Dms\Web\Laravel\Action\IActionResultHandler;
use Dms\Web\Laravel\Action\ResultHandler\MessageResultHandler;
use Dms\Web\Laravel\Tests\Mock\Language\MockLanguageProvider;
use Illuminate\Http\JsonResponse;

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