<?php declare(strict_types=1);

namespace Dms\Web\Expressive\Renderer\Form;

use Dms\Core\Exception\InvalidArgumentException;
use Dms\Core\Form\IForm;
use Zend\Expressive\Template\TemplateRendererInterface;

/**
 * The form renderer base class.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
abstract class FormRenderer implements IFormRenderer
{
    /**
     * @var FieldRendererCollection
     */
    protected $fieldRenderers;

    /**
     * @var TemplateRendererInterface
     */
    protected $template;

    /**
     * FormRenderer constructor.
     *
     * @param FieldRendererCollection   $fieldRenderers
     * @param TemplateRendererInterface $template
     */
    public function __construct(FieldRendererCollection $fieldRenderers, TemplateRendererInterface $template)
    {
        $this->fieldRenderers = $fieldRenderers;
        $this->template = $template;
    }

    /**
     * @return FieldRendererCollection
     */
    public function getFieldRenderers() : FieldRendererCollection
    {
        return $this->fieldRenderers;
    }

    /**
     * Renders the supplied form as a html string.
     *
     * @param FormRenderingContext $renderingContext
     * @param IForm                $form
     *
     * @return string
     * @throws InvalidArgumentException
     */
    final public function renderFields(FormRenderingContext $renderingContext, IForm $form) : string
    {
        if (!$this->accepts($renderingContext, $form)) {
            throw InvalidArgumentException::format(
                'Invalid form supplied to %s: this form is not supported',
                __METHOD__
            );
        }

        $originalForm = $renderingContext->getCurrentForm();
        $renderingContext->setCurrentForm($form);

        $renderedForm = $this->renderFormFields($renderingContext, $form);

        $renderingContext->setCurrentForm($originalForm);

        return $renderedForm;
    }

    /**
     * @param FormRenderingContext $renderingContext
     * @param IForm                $form
     *
     * @return string
     */
    abstract protected function renderFormFields(FormRenderingContext $renderingContext, IForm $form) : string;

    /**
     * Renders the supplied form as a html string.
     *
     * @param FormRenderingContext $renderingContext
     * @param IForm                $form
     *
     * @return string
     * @throws InvalidArgumentException
     * @throws UnrenderableFieldException
     */
    final public function renderFieldsAsValues(FormRenderingContext $renderingContext, IForm $form) : string
    {
        if (!$this->accepts($renderingContext, $form)) {
            throw InvalidArgumentException::format(
                'Invalid form supplied to %s: this form is not supported',
                __METHOD__
            );
        }

        $originalForm = $renderingContext->getCurrentForm();
        $renderingContext->setCurrentForm($form);

        $renderedForm = $this->renderFormFieldsAsValues($renderingContext, $form);

        $renderingContext->setCurrentForm($originalForm);

        return $renderedForm;
    }

    /**
     * @param FormRenderingContext $renderingContext
     * @param IForm                $form
     *
     * @return string
     */
    abstract protected function renderFormFieldsAsValues(FormRenderingContext $renderingContext, IForm $form) : string;
}
