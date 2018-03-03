<?php

namespace Dms\Web\Expressive\Tests\Unit\Action\ResultHandler;

use Dms\Core\Exception\InvalidArgumentException;
use Dms\Core\Module\IAction;
use Dms\Core\Common\Crud\IReadModule;
use Dms\Web\Expressive\Action\IActionResultHandler;
use Dms\Web\Expressive\Http\ModuleContext;
use PHPUnit\Framework\TestCase;

/**
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
abstract class ResultHandlerTest extends TestCase
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
     * @param mixed $result
     */
    public function testHandleThrowsOnInvalidException(IAction $action, $result)
    {
        $this->expectException(InvalidArgumentException::class);
        $this->handler->handle($this->mockModuleContext(), $action, $result);
    }

    /**
     * @dataProvider resultHandlingTests
     * @param mixed $result
     * @param mixed $response
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
        $this->assertEquals($expected->getBody()->__toString(), $actual->getBody()->__toString());
    }

    protected function mockModuleContext() : ModuleContext
    {
        $module = $this->createMock(IReadModule::class);
        $module->method('getLabelFor')
            ->willReturn('Hello');

        $module->method('getName')
            ->willReturn('IReadModule');

        $stub = $this->createMock(ModuleContext::class);
        $stub->method('getModule')
            ->willReturn($module);

        $stub->method('getUrl')
            ->willReturn('/url');

        return $stub;
    }
}
