<?php

namespace Dms\Web\Expressive\Tests\Unit\Action\ResultHandler;

use Dms\Core\Model\Object\TypedObject;
use Dms\Core\Common\Crud\Action\Crud\EditAction;
use Dms\Web\Expressive\Action\IActionResultHandler;
use Dms\Web\Expressive\Action\ResultHandler\EditedObjectResultHandler;
use Zend\Diactoros\Response\JsonResponse;

/**
 * @author Hari KT <kthari85@gmail.com>
 */
class EditedObjectResultHandlerTest extends ResultHandlerTest
{
    protected function buildHandler() : IActionResultHandler
    {
        return new EditedObjectResultHandler();
    }

    private function typedObject()
    {
        return $this->createMock(TypedObject::class);
    }

    private function mockEditAction()
    {
        return $this->createMock(EditAction::class);
    }

    public function resultHandlingTests() : array
    {
        return [
            [$this->mockEditAction(), $this->typedObject(), new JsonResponse([
                'message' => "The 'Hello' IReadModule has been updated.",
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
