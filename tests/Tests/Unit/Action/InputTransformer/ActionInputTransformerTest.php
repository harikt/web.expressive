<?php

namespace Dms\Web\Laravel\Tests\Unit\Action\InputTransformer;

use Dms\Core\Module\IParameterizedAction;
use Dms\Web\Laravel\Action\IActionInputTransformer;
use Dms\Web\Laravel\Http\ModuleContext;
use Dms\Web\Laravel\Tests\Unit\UnitTest;

/**
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
abstract class ActionInputTransformerTest extends UnitTest
{
    /**
     * @var IActionInputTransformer
     */
    protected $transformer;

    public function setUp()
    {
        $this->transformer = $this->buildInputTransformer();
    }

    abstract protected function buildInputTransformer() : IActionInputTransformer;

    abstract public function transformationTestCases() : array;

    protected function mockAction()
    {
        return $this->getMockForAbstractClass(IParameterizedAction::class);
    }

    /**
     * @dataProvider transformationTestCases
     */
    public function testInputTransformer(IParameterizedAction $action, array $input, array $output)
    {
        $this->assertEquals(
            $output,
            $this->transformer->transform($this->createMock(ModuleContext::class), $action, $input)
        );
    }
}