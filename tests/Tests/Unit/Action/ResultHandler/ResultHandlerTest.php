<?php

namespace Dms\Web\Laravel\Tests\Unit\Action\ResultHandler;

use Dms\Core\Exception\InvalidArgumentException;
use Dms\Core\Module\IAction;
use Dms\Web\Laravel\Action\IActionResultHandler;
use Dms\Web\Laravel\Http\ModuleContext;
use Dms\Web\Laravel\Tests\Unit\UnitTest;
use Symfony\Component\HttpFoundation\Response;

/**
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
abstract class ResultHandlerTest extends UnitTest
{
    /**
     * @var IActionResultHandler
     */
    protected $handler;

    public function setUp()
    {
        parent::setUp();
        $this->handler = $this->buildHandler();
    }

    abstract protected function buildHandler() : IActionResultHandler;

    abstract public function resultHandlingTests() : array;

    abstract public function unhandleableResultTests() : array;

    protected function mockAction($resultType = null) : IAction
    {
        $mock = $this->getMockForAbstractClass(IAction::class);

        $mock->method('getReturnTypeClass')->willReturn($resultType);

        return $mock;
    }

    public function testAcceptsResult()
    {
        foreach ($this->resultHandlingTests() as list($action, $result, $response)) {
            $this->assertTrue($this->handler->accepts($this->mockModuleContext(), $action, $result));
        }

        foreach ($this->unhandleableResultTests() as list($action, $result)) {
            $this->assertFalse($this->handler->accepts($this->mockModuleContext(), $action, $result));
        }
    }

    /**
     * @dataProvider unhandleableResultTests
     */
    public function testHandleThrowsOnInvalidException(IAction $action, $result)
    {
        $this->expectException(InvalidArgumentException::class);
        $this->handler->handle($this->mockModuleContext(), $action, $result);
    }

    /**
     * @dataProvider resultHandlingTests
     */
    public function testHandleException(IAction $action, $result, $response)
    {
        $this->assertResponsesMatch(
            $response,
            $this->handler->handle($this->mockModuleContext(), $action, $result)
        );
    }

    protected function assertResponsesMatch($expected, $actual)
    {
        if ($expected instanceof Response) {
            $expected->headers->remove('date');
        }

        if ($actual instanceof Response) {
            $actual->headers->remove('date');
        }

        $this->assertEquals($expected, $actual);
    }

    protected function mockModuleContext() : ModuleContext
    {
        return $this->createMock(ModuleContext::class);
    }
}