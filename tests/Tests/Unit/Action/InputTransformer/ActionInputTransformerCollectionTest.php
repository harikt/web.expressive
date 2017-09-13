<?php

namespace Dms\Web\Laravel\Tests\Unit\Action\InputTransformer;

use Dms\Core\Module\IParameterizedAction;
use Dms\Web\Laravel\Action\ActionInputTransformerCollection;
use Dms\Web\Laravel\Action\IActionInputTransformer;
use Dms\Web\Laravel\Http\ModuleContext;

/**
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class ActionInputTransformerCollectionTest extends ActionInputTransformerTest
{
    protected function buildInputTransformer() : IActionInputTransformer
    {
        $transformer1 = $this->getMockForAbstractClass(IActionInputTransformer::class);

        $transformer1->method('transform')
            ->willReturnCallback(function (ModuleContext $moduleContext, IParameterizedAction $action, array $input) {
                return $input + ['abc' => 'foo'];
            });

        $transformer2 = $this->getMockForAbstractClass(IActionInputTransformer::class);

        $transformer2->method('transform')
            ->willReturnCallback(function (ModuleContext $moduleContext, IParameterizedAction $action, array $input) {
                return $input + ['another' => 'bar'];
            });

        return new ActionInputTransformerCollection([
            $transformer1,
            $transformer2,
        ]);
    }

    public function transformationTestCases() : array
    {
        return [
            [$this->mockAction(), ['test' => 'string'], ['test' => 'string', 'abc' => 'foo', 'another' => 'bar']],
        ];
    }
}