<?php declare(strict_types=1);

namespace Dms\Web\Expressive\Renderer\Form;

use Dms\Core\Form\IForm;
use Dms\Core\Model\ITypedObject;
use Dms\Core\Module\IParameterizedAction;
use Dms\Web\Expressive\Http\ModuleContext;
use Dms\Web\Expressive\Util\KeywordTypeIdentifier;
use Zend\Expressive\Template\TemplateRendererInterface;

/**
 * The action form renderer class.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class ActionFormRenderer
{
    /**
     * @var FormRendererCollection
     */
    protected $formRendererCollection;

    /**
     * @var KeywordTypeIdentifier
     */
    protected $keywordTypeIdentifier;

    protected $template;

    /**
     * ActionFormRenderer constructor.
     *
     * @param FormRendererCollection $formRendererCollection
     * @param KeywordTypeIdentifier  $keywordTypeIdentifier
     */
    public function __construct(
        FormRendererCollection $formRendererCollection,
        KeywordTypeIdentifier $keywordTypeIdentifier,
        TemplateRendererInterface $template
    ) {
        $this->formRendererCollection = $formRendererCollection;
        $this->keywordTypeIdentifier  = $keywordTypeIdentifier;
        $this->template = $template;
    }

    /**
     * @param FormRenderingContext $renderingContext
     * @param IForm                $form
     *
     * @return IFormRenderer
     */
    public function getFormRenderer(FormRenderingContext $renderingContext, IForm $form) : IFormRenderer
    {
        return $this->formRendererCollection->findRendererFor($renderingContext, $form);
    }

    /**
     * Renders the action form as a staged form.
     *
     * @param ModuleContext        $moduleContext
     * @param IParameterizedAction $action
     * @param array                $hiddenValues
     * @param ITypedObject         $object
     * @param int                  $initialStageNumber
     *
     * @return string
     * @throws \Exception
     * @throws \Throwable
     */
    public function renderActionForm(
        ModuleContext $moduleContext,
        IParameterizedAction $action,
        array $hiddenValues = [],
        ITypedObject $object = null,
        int $initialStageNumber = 1
    ) : string {
        return $this->template->render(
            'dms::components.form.staged-form',
            [
            'moduleContext'          => $moduleContext,
            'renderingContext'       => new FormRenderingContext($moduleContext, $action, null, $object),
            'action'                 => $action,
            'stagedForm'             => $action->getStagedForm(),
            'formRendererCollection' => $this->formRendererCollection,
            'actionName'             => $action->getName(),
            'submitButtonClass'      => $this->keywordTypeIdentifier->getTypeFromName($action->getName()),
            'hiddenValues'           => $hiddenValues,
            'initialStageNumber'     => $initialStageNumber,
            ]
        );
    }

    /**
     * Renders the supplied form fields.
     *
     * @param FormRenderingContext $renderingContext
     * @param IForm                $form
     *
     * @return string
     */
    public function renderFormFields(FormRenderingContext $renderingContext, IForm $form) : string
    {
        return $this->getFormRenderer($renderingContext, $form)->renderFields($renderingContext, $form);
    }
}
