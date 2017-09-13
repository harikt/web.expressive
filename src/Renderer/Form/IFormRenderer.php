<?php declare(strict_types=1);

namespace Dms\Web\Expressive\Renderer\Form;

use Dms\Core\Exception\InvalidArgumentException;
use Dms\Core\Form\IForm;

/**
 * The form renderer interface.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
interface IFormRenderer
{
    /**
     * @return FieldRendererCollection
     */
    public function getFieldRenderers() : FieldRendererCollection;
    
    /**
     * Returns whether this renderer can render the supplied form.
     *
     * @param FormRenderingContext $renderingContext
     * @param IForm                $form
     *
     * @return bool
     */
    public function accepts(FormRenderingContext $renderingContext, IForm $form) : bool;

    /**
     * Renders the supplied form as a html string.
     *
     * @param FormRenderingContext $renderingContext
     * @param IForm                $form
     *
     * @return string
     */
    public function renderFields(FormRenderingContext $renderingContext, IForm $form) : string;

    /**
     * Renders the supplied form values display as a html string.
     *
     * @param FormRenderingContext $renderingContext
     * @param IForm                $form
     *
     * @return string
     */
    public function renderFieldsAsValues(FormRenderingContext $renderingContext, IForm $form) : string;
}
