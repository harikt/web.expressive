<?php

namespace Dms\Web\Expressive\Tests\Unit\Action\ResultHandler;

use Dms\Core\Model\Object\TypedObject;
use Dms\Core\Common\Crud\Action\Object\IObjectAction;
use Dms\Core\Common\Crud\ICrudModule;
use Dms\Web\Expressive\Action\IActionResultHandler;
use Dms\Web\Expressive\Action\ResultHandler\DeletedObjectResultHandler;
use Zend\Diactoros\Response\JsonResponse;

/**
 * @author Hari KT <kthari85@gmail.com>
 */
class DeletedObjectResultHandlerTest extends ResultHandlerTest
{
    protected function buildHandler() : IActionResultHandler
    {
        return new DeletedObjectResultHandler();
    }

    private function typedObject()
    {
        return $this->createMock(TypedObject::class);
    }

    private function mockDeleteAction()
    {
        $action = $this->createMock(IObjectAction::class);
        $action->method('getName')
            ->willReturn(ICrudModule::REMOVE_ACTION);

        return $action;
    }

    public function resultHandlingTests() : array
    {
        return [
            [$this->mockDeleteAction(), $this->typedObject(), new JsonResponse([
                'message'      => "The 'Hello' IReadModule has been removed.",
                'message_type' => 'info',
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
