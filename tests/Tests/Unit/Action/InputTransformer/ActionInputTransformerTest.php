<?php

namespace Dms\Web\Expressive\Tests\Unit\Action\InputTransformer;

use Dms\Core\Module\IParameterizedAction;
use Dms\Web\Expressive\Action\IActionInputTransformer;
use Dms\Web\Expressive\Http\ModuleContext;
use PHPUnit\Framework\TestCase;

/**
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
abstract class ActionInputTransformerTest extends TestCase
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
