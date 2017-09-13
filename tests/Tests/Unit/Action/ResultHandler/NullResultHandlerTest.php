<?php

namespace Dms\Web\Laravel\Tests\Unit\Action\ResultHandler;

use Dms\Web\Laravel\Action\IActionResultHandler;
use Dms\Web\Laravel\Action\ResultHandler\NullResultHandler;
use Illuminate\Http\JsonResponse;

/**
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class NullResultHandlerTest extends ResultHandlerTest
{

    protected function buildHandler() : IActionResultHandler
    {
        return new NullResultHandler();
    }

    public function resultHandlingTests() : array
    {
        return [
            [$this->mockAction(), null, new JsonResponse(['message' => 'The action was successfully executed'])],
        ];
    }

    public function unhandleableResultTests() : array
    {
        return [
            [$this->mockAction(\stdClass::class), new \stdClass()],
        ];
    }
}