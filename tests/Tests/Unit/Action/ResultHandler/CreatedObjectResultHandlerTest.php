<?php

namespace Dms\Web\Expressive\Tests\Unit\Action\ResultHandler;

use Dms\Core\Model\Object\TypedObject;
use Dms\Core\Common\Crud\Action\Crud\CreateAction;
use Dms\Web\Expressive\Action\IActionResultHandler;
use Dms\Web\Expressive\Action\ResultHandler\CreatedObjectResultHandler;
use Zend\Diactoros\Response\JsonResponse;

/**
 * @author Hari KT <kthari85@gmail.com>
 */
class CreatedObjectResultHandlerTest extends ResultHandlerTest
{
    protected function buildHandler() : IActionResultHandler
    {
        return new CreatedObjectResultHandler();
    }

    private function typedObject()
    {
        return $this->createMock(TypedObject::class);
    }

    private function mockCreateAction()
    {
        return $this->createMock(CreateAction::class);
    }

    public function resultHandlingTests() : array
    {
        return [
            [$this->mockCreateAction(), $this->typedObject(), new JsonResponse([
                'message'      => "The 'Hello' IReadModule has been created.",
                'message_type' => 'success',
                'redirect'     => '/url',
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
