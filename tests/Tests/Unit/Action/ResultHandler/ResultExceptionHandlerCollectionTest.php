<?php

namespace Dms\Web\Expressive\Tests\Unit\Action\ResultHandler;

use Dms\Core\Language\Message;
use Dms\Core\Module\IAction;
use Dms\Web\Expressive\Action\ActionResultHandlerCollection;
use Dms\Web\Expressive\Action\ResultHandler\MessageResultHandler;
use Dms\Web\Expressive\Action\ResultHandler\NullResultHandler;
use Dms\Web\Expressive\Action\UnhandleableActionResultException;
use Dms\Web\Expressive\Http\ModuleContext;
use Dms\Web\Expressive\Tests\Mock\Language\MockLanguageProvider;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Translation\MessageSelector;
use Symfony\Component\Translation\Translator;

/**
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class ActionResultHandlerCollectionTest extends TestCase
{
    /**
     * @var ActionResultHandlerCollection
     */
    protected $collection;

    public function setUp()
    {
        $translator = new Translator('en_US', new MessageSelector());
        $this->collection = new ActionResultHandlerCollection([
            new NullResultHandler($translator),
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
