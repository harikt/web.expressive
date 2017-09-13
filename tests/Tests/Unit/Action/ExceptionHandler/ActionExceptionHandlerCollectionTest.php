<?php

namespace Dms\Web\Laravel\Tests\Unit\Action\ExceptionHandler;

use Dms\Core\Auth\AdminForbiddenException;
use Dms\Core\Form\InvalidFormSubmissionException;
use Dms\Core\Module\IAction;
use Dms\Web\Laravel\Action\ActionExceptionHandlerCollection;
use Dms\Web\Laravel\Action\ExceptionHandler\InvalidFormSubmissionExceptionHandler;
use Dms\Web\Laravel\Action\ExceptionHandler\AdminForbiddenExceptionHandler;
use Dms\Web\Laravel\Action\UnhandleableActionExceptionException;
use Dms\Web\Laravel\Http\ModuleContext;
use Dms\Web\Laravel\Tests\Mock\Language\MockLanguageProvider;
use Dms\Web\Laravel\Tests\Unit\UnitTest;

/**
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class ActionExceptionHandlerCollectionTest extends UnitTest
{
    /**
     * @var ActionExceptionHandlerCollection
     */
    protected $collection;

    public function setUp()
    {
        $this->collection = new ActionExceptionHandlerCollection([
            new InvalidFormSubmissionExceptionHandler(new MockLanguageProvider()),
            new AdminForbiddenExceptionHandler(),
        ]);
    }

    public function testFindHandler()
    {
        $this->assertInstanceOf(
            InvalidFormSubmissionExceptionHandler::class,
            $this->collection->findHandlerFor($this->mockModuleContext(), $this->mockAction(), $this->createMock(InvalidFormSubmissionException::class))
        );

        $this->assertInstanceOf(
            AdminForbiddenExceptionHandler::class,
            $this->collection->findHandlerFor($this->mockModuleContext(), $this->mockAction(), $this->createMock(AdminForbiddenException::class))
        );
    }

    public function testUnhandleableException()
    {
        $this->expectException(UnhandleableActionExceptionException::class);

        $this->collection->findHandlerFor($this->mockModuleContext(), $this->mockAction(), new \Exception());
    }

    protected function mockModuleContext() : ModuleContext
    {
        return $this->createMock(ModuleContext::class);
    }

    protected function mockAction() : IAction
    {
        return $this->getMockForAbstractClass(IAction::class);
    }
}