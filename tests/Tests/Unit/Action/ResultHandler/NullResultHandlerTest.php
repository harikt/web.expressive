<?php

namespace Dms\Web\Expressive\Tests\Unit\Action\ResultHandler;

use Dms\Web\Expressive\Action\IActionResultHandler;
use Dms\Web\Expressive\Action\ResultHandler\NullResultHandler;
use Zend\Diactoros\Response\JsonResponse;

/**
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
// class NullResultHandlerTest extends ResultHandlerTest
// {
//     protected function buildHandler() : IActionResultHandler
//     {
//         return new NullResultHandler();
//     }
//
//     public function resultHandlingTests() : array
//     {
//         return [
//             [$this->mockAction(), null, new JsonResponse(['message' => 'The action was successfully executed'])],
//         ];
//     }
//
//     public function unhandleableResultTests() : array
//     {
//         return [
//             [$this->mockAction(\stdClass::class), new \stdClass()],
//         ];
//     }
// }
