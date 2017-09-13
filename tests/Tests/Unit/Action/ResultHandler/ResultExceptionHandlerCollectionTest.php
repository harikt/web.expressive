<?php

namespace Dms\Web\Laravel\Tests\Unit\Action\ResultHandler;

use Dms\Core\Language\Message;
use Dms\Core\Module\IAction;
use Dms\Web\Laravel\Action\ActionResultHandlerCollection;
use Dms\Web\Laravel\Action\ResultHandler\MessageResultHandler;
use Dms\Web\Laravel\Action\ResultHandler\NullResultHandler;
use Dms\Web\Laravel\Action\UnhandleableActionResultException;
use Dms\Web\Laravel\Http\ModuleContext;
use Dms\Web\Laravel\Tests\Mock\Language\MockLanguageProvider;
use Dms\Web\Laravel\Tests\Unit\UnitTest;

/**
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class ActionResultHandlerCollectionTest extends UnitTest
{
    /**
     * @var ActionResultHandlerCollection
     */
    protected $collection;

    public function setUp()
    {
        $this->collection = new ActionResultHandlerCollection([
            new NullResultHandler(),
            new MessageResultHandler(new MockLanguageProvider()),
        ]);
    }

    public function testFindHandler()
    {
        $this->assertInstanceOf(
            NullResultHandler::class,
            $this->collection->findHandlerFor($this->mockModuleContext(), $this->mockAction(), null)
        );

        $this->assertInstanceOf(
            MessageResultHandler::class,
            $this->collection->findHandlerFor($this->mockModuleContext(), $this->mockAction(), new Message('id', []))
        );
    }

    public function testUnhandleableResult()
    {
        $this->expectException(UnhandleableActionResultException::class);

        $this->collection->findHandlerFor($this->mockModuleContext(), $this->mockAction(\stdClass::class), new \stdClass());
    }

    protected function mockAction($resultType = null) : IAction
    {
        $mock = $this->getMockForAbstractClass(IAction::class);

        $mock->method('getReturnTypeClass')->willReturn($resultType);

        return $mock;
    }

    private function mockModuleContext() : ModuleContext
    {
        return $this->createMock(ModuleContext::class);
    }
}