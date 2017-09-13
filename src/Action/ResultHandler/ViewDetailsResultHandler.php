<?php declare(strict_types=1);

namespace Dms\Web\Expressive\Action\ResultHandler;

use Dms\Core\Common\Crud\Action\Crud\ViewDetailsAction;
use Dms\Core\Common\Crud\Action\Object\IObjectAction;
use Dms\Core\Form\IStagedForm;
use Dms\Core\Form\Stage\IndependentFormStage;
use Dms\Core\Module\IAction;
use Dms\Web\Expressive\Action\ActionResultHandler;
use Dms\Web\Expressive\Http\ModuleContext;
use Dms\Web\Expressive\Renderer\Form\DefaultFormRenderer;
use Dms\Web\Expressive\Renderer\Form\FormRenderingContext;
use Zend\Diactoros\Response;

/**
 * The created entity action result handler.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class ViewDetailsResultHandler extends ActionResultHandler
{
    /**
     * @var DefaultFormRenderer
     */
    protected $formRenderer;

    /**
     * ViewDetailsResultHandler constructor.
     *
     * @param DefaultFormRenderer $formRenderer
     */
    public function __construct(DefaultFormRenderer $formRenderer)
    {
        parent::__construct();
        $this->formRenderer = $formRenderer;
    }

    /**
     * @return string|null
     */
    protected function supportedResultType()
    {
        return null;
    }

    /**
     * @param ModuleContext $moduleContext
     * @param IAction       $action
     * @param mixed         $result
     *
     * @return bool
     */
    protected function canHandleResult(ModuleContext $moduleContext, IAction $action, $result) : bool
    {
        return $action instanceof ViewDetailsAction;
    }

    /**
     * @param ModuleContext $moduleContext
     * @param IAction       $action
     * @param mixed         $result
     *
     * @return Response|mixed
     */
    protected function handleResult(ModuleContext $moduleContext, IAction $action, $result)
    {
        /** @var IStagedForm $result */
        $object           = $result->getFirstForm()->getField(IObjectAction::OBJECT_FIELD_NAME)->getInitialValue();
        $stageNumber      = 2;
        $renderingContext = new FormRenderingContext($moduleContext, $action, $stageNumber, $object);

        $forms = [];

        foreach (array_slice($result->getAllStages(), 1) as $stage) {
            /** @var IndependentFormStage $stage */
            $forms[] = $this->formRenderer->renderFieldsAsValues($renderingContext, $stage->loadForm());
            $stageNumber++;
            $renderingContext->setCurrentStageNumber($stageNumber);
        }

        return implode('', $forms);
    }
}
