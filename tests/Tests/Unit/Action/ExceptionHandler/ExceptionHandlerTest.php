<?php

namespace Dms\Web\Expressive\Tests\Unit\Action\ExceptionHandler;

use Dms\Core\Exception\InvalidArgumentException;
use Dms\Core\Module\IAction;
use Dms\Web\Expressive\Action\IActionExceptionHandler;
use Dms\Web\Expressive\Http\ModuleContext;
use PHPUnit\Framework\TestCase;

/**
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
abstract class ExceptionHandlerTest extends TestCase
{
    /**
     * @var IActionExceptionHandler
     */
    protected $handler;

    public function setUp()
    {
        parent::setUp();
        $this->handler = $this->buildHandler();
    }

    abstract protected function buildHandler() : IActionExceptionHandler;

    abstract public function exceptionsHandlingTests() : array;

    abstract public function unhandleableExceptionTests() : array;

    protected function mockAction()
    {
        return $this->getMockForAbstractClass(IAction::class);
    }

    public function testAcceptException()
    {
        foreach ($this->exceptionsHandlingTests() as list($action, $exception, $response)) {
            $this->assertTrue($this->handler->accepts($this->mockModuleContext(), $action, $exception));
        }

        foreach ($this->unhandleableExceptionTests() as list($action, $exception)) {
            $this->assertFalse($this->handler->accepts($this->mockModuleContext(), $action, $exception));
        }
    }

    /**
     * @dataProvider unhandleableExceptionTests
     */
    public function testHandleThrowsOnInvalidException(IAction $action, \Exception $exception)
    {
        $this->expectException(InvalidArgumentException::class);
        $this->handler->handle($this->mockModuleContext(), $action, $exception);
    }

    /**
     * @dataProvider exceptionsHandlingTests
     */
    public function testHandleException(IAction $action, \Exception $exception, $response)
    {
        $this->assertResponsesMatch(
            $response,
            $this->handler->handle($this->mockModuleContext(), $action, $exception)
        );
    }

    protected function assertResponsesMatch($expected, $actual)
    {
        $this->assertEquals($expected, $actual);
    }

    protected function mockModuleContext() : ModuleContext
    {
        return $this->createMock(ModuleContext::class);
    }
}
