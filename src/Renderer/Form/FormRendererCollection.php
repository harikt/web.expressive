<?php declare(strict_types=1);

namespace Dms\Web\Expressive\Renderer\Form;

use Dms\Common\Structure\Web\Form\HtmlType;
use Dms\Core\Exception\InvalidArgumentException;
use Dms\Core\Form\IForm;

/**
 * The Form renderer collection.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class FormRendererCollection
{
    /**
     * @var IFormRenderer[]
     */
    protected $formRenderers;

    /**
     * FormRendererCollection constructor.
     *
     * @param IFormRenderer[] $formRenderers
     */
    public function __construct(array $formRenderers)
    {
        InvalidArgumentException::verifyAllInstanceOf(
            __METHOD__,
            'formRenderers',
            $formRenderers,
            IFormRenderer::class
        );

        $this->formRenderers = $formRenderers;
    }

    /**
     * @param FormRenderingContext $renderingContext
     * @param IForm                $form
     *
     * @return IFormRenderer
     * @throws UnrenderableFormException
     */
    public function findRendererFor(FormRenderingContext $renderingContext, IForm $form) : IFormRenderer
    {
        foreach ($this->formRenderers as $formRenderer) {
            if ($formRenderer->accepts($renderingContext, $form)) {
                return $formRenderer;
            }
        }

        throw UnrenderableFormException::format(
            'Could not render form for action \'%s\' with form type of class: no matching form renderer could be found',
            $renderingContext->getAction()->getName()
        );
    }
}
