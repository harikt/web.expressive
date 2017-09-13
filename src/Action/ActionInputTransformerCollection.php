<?php declare(strict_types=1);

namespace Dms\Web\Expressive\Action;

use Dms\Core\Exception\InvalidArgumentException;
use Dms\Core\Module\IParameterizedAction;
use Dms\Web\Expressive\Http\ModuleContext;

/**
 * The input transformer collection.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class ActionInputTransformerCollection implements IActionInputTransformer
{
    /**
     * @var IActionInputTransformer[]
     */
    protected $transformers;

    /**
     * InputTransformerCollection constructor.
     *
     * @param IActionInputTransformer[] $transformers
     */
    public function __construct(array $transformers)
    {
        InvalidArgumentException::verifyAllInstanceOf(__METHOD__, 'transformers', $transformers, IActionInputTransformer::class);

        $this->transformers = $transformers;
    }

    /**
     * Transforms for the supplied action.
     *
     * @param ModuleContext        $moduleContext
     * @param IParameterizedAction $action
     * @param array                $input
     *
     * @return array
     */
    public function transform(ModuleContext $moduleContext, IParameterizedAction $action, array $input) : array
    {
        foreach ($this->transformers as $transformer) {
            $input = $transformer->transform($moduleContext, $action, $input);
        }

        return $input;
    }
}
