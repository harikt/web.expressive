<?php declare(strict_types=1);

namespace Dms\Web\Expressive\Http\Controllers\Package\Module\Action;

use Dms\Core\Auth\IAuthSystem;
use Dms\Core\Common\Crud\Action\Object\IObjectAction;
use Dms\Core\Common\Crud\IReadModule;
use Dms\Core\Form\Builder\Form;
use Dms\Core\Form\Field\Type\ArrayOfType;
use Dms\Core\Form\Field\Type\InnerFormType;
use Dms\Core\Form\Field\Type\ObjectIdType;
use Dms\Core\Form\IForm;
use Dms\Core\Form\InvalidFormSubmissionException;
use Dms\Core\Form\InvalidInputException;
use Dms\Core\ICms;
use Dms\Core\Language\ILanguageProvider;
use Dms\Core\Model\IIdentifiableObjectSet;
use Dms\Core\Model\ITypedObject;
use Dms\Core\Module\ActionNotFoundException;
use Dms\Core\Module\IAction;
use Dms\Core\Module\IModule;
use Dms\Core\Module\IParameterizedAction;
use Dms\Core\Module\IUnparameterizedAction;
use Dms\Core\Persistence\IRepository;
use Dms\Web\Expressive\Action\ActionExceptionHandlerCollection;
use Dms\Web\Expressive\Action\ActionInputTransformerCollection;
use Dms\Web\Expressive\Action\ActionResultHandlerCollection;
use Dms\Web\Expressive\Action\UnhandleableActionExceptionException;
use Dms\Web\Expressive\Action\UnhandleableActionResultException;
use Dms\Web\Expressive\Error\DmsError;
use Dms\Web\Expressive\Http\Controllers\DmsController;
use Dms\Web\Expressive\Http\ModuleContext;
use Dms\Web\Expressive\Renderer\Action\ActionButton;
use Dms\Web\Expressive\Renderer\Action\ObjectActionButtonBuilder;
use Dms\Web\Expressive\Renderer\Form\ActionFormRenderer;
use Dms\Web\Expressive\Renderer\Form\FormRenderingContext;
use Dms\Web\Expressive\Renderer\Form\IFieldRendererWithActions;
use Dms\Web\Expressive\Renderer\Form\IFormRendererWithActions;
use Dms\Web\Expressive\Util\ActionLabeler;
use Dms\Web\Expressive\Util\ActionSafetyChecker;
use Dms\Web\Expressive\Util\StringHumanizer;
use Illuminate\Http\Exceptions\HttpResponseException;
use Interop\Http\ServerMiddleware\DelegateInterface;
use Interop\Http\ServerMiddleware\MiddlewareInterface as ServerMiddlewareInterface;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Expressive\Router\RouterInterface;
use Zend\Expressive\Template\TemplateRendererInterface;
use Zend\Diactoros\Response\HtmlResponse;
use Zend\Diactoros\Response\JsonResponse;

/**
 * The action controller
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class FieldRendererController extends DmsController implements ServerMiddlewareInterface
{
    /**
     * @var ILanguageProvider
     */
    protected $lang;

    /**
     * @var ActionInputTransformerCollection
     */
    protected $inputTransformers;

    /**
     * @var ActionResultHandlerCollection
     */
    protected $resultHandlers;

    /**
     * @var ActionExceptionHandlerCollection
     */
    protected $exceptionHandlers;

    /**
     * @var ActionSafetyChecker
     */
    protected $actionSafetyChecker;

    /**
     * @var ActionFormRenderer
     */
    protected $actionFormRenderer;

    /**
     * @var ObjectActionButtonBuilder
     */
    protected $actionButtonBuilder;

    protected $template;

    protected $router;

    /**
     * ActionController constructor.
     *
     * @param ICms                      	   $cms
     * @param IAuthSystem 			    	   $auth
     * @param ActionInputTransformerCollection $inputTransformers
     * @param ActionResultHandlerCollection    $resultHandlers
     * @param ActionExceptionHandlerCollection $exceptionHandlers
     * @param ActionSafetyChecker              $actionSafetyChecker
     * @param ActionFormRenderer               $actionFormRenderer
     * @param ObjectActionButtonBuilder        $actionButtonBuilder
     */
    public function __construct(
        ICms $cms,
        IAuthSystem $auth,
        ActionInputTransformerCollection $inputTransformers,
        ActionResultHandlerCollection $resultHandlers,
        ActionExceptionHandlerCollection $exceptionHandlers,
        ActionSafetyChecker $actionSafetyChecker,
        ActionFormRenderer $actionFormRenderer,
        ObjectActionButtonBuilder $actionButtonBuilder,
        TemplateRendererInterface $template,
        RouterInterface $router
    ) {
        parent::__construct($cms, $auth);
        $this->lang                = $cms->getLang();
        $this->inputTransformers   = $inputTransformers;
        $this->resultHandlers      = $resultHandlers;
        $this->exceptionHandlers   = $exceptionHandlers;
        $this->actionSafetyChecker = $actionSafetyChecker;
        $this->actionFormRenderer  = $actionFormRenderer;
        $this->actionButtonBuilder = $actionButtonBuilder;
        $this->router = $router;
        $this->template = $template;
    }

    public function process(ServerRequestInterface $request, DelegateInterface $delegate)
    {
        // public function runFieldRendererAction(
        //     ServerRequestInterface $request,
        //     ModuleContext $moduleContext,
        //     string $actionName,
        //     int $stageNumber,
        //     string $fieldName,
        //     string $fieldRendererAction = null,
        //     string $objectId = null
        // ) {
        $packageName = $request->getAttribute('package');
        $moduleName = $request->getAttribute('module');
        $package = $this->cms->loadPackage($packageName);
        $moduleContext = ModuleContext::rootContext($this->router, $packageName, $moduleName, function () use ($package, $moduleName) {
            return $package->loadModule($moduleName);
        });
        $module = $moduleContext->getModule();

        $objectId = $request->getAttribute('object_id');
        $actionName = $request->getAttribute('action');
        $stageNumber = $request->getAttribute('stage');
        $fieldName = $request->getAttribute('field_name');
        $fieldRendererAction = $request->getAttribute('field_action');

        $action = $this->loadAction($moduleContext->getModule(), $actionName, $request);
        $form   = $this->loadFormStage($request, $moduleContext, $actionName, $stageNumber, $objectId, $object);

        $field = $this->findFieldFromBracketSyntaxName($form, $fieldName);

        if (!$field) {
            DmsError::abort($request, 404);
        }

        $renderingContext = new FormRenderingContext($moduleContext, $action, $stageNumber, $object);
        $renderer         = $this->actionFormRenderer->getFormRenderer($renderingContext, $form)
            ->getFieldRenderers()
            ->findRendererFor($renderingContext, $field);

        if (!($renderer instanceof IFieldRendererWithActions)) {
            DmsError::abort($request, 404);
        }

        $this->loadSharedViewVariables($request);

        return $renderer->handleAction($renderingContext, $field, $request, $fieldRendererAction, $request->get('__field_action_data') ?? []);
    }

    protected function loadFormStage(
        ServerRequestInterface $request,
        ModuleContext $moduleContext,
        string $actionName,
        int $stageNumber,
        string $objectId = null,
        &$object = null
    ) : IForm {
        $action = $this->loadAction($moduleContext->getModule(), $actionName, $request);

        if (!($action instanceof IParameterizedAction)) {
            throw new HttpResponseException(new JsonResponse([
                'message' => 'This action does not require an input form',
            ], 403));
        }

        if ($objectId !== null && $action instanceof IObjectAction) {
            $object = $this->loadObject($objectId, $action);

            $action = $action->withSubmittedFirstStage([
                IObjectAction::OBJECT_FIELD_NAME => $object,
            ]);

            $stageNumber--;
        }

        $form        = $action->getStagedForm();
        $stageNumber = (int)$stageNumber;

        if ($stageNumber < 1 || $stageNumber > $form->getAmountOfStages()) {
            throw new HttpResponseException(new JsonResponse([
                'message' => 'Invalid stage number',
            ], 404));
        }

        $input = $this->inputTransformers->transform($moduleContext, $action, $request->getParsedBody());

        if ($request->input('__initial_dependent_data')) {
            for ($i = 1; $i < $stageNumber; $i++) {
                $formStage = $form->getFormForStage($i, $input);
                $input += $formStage->unprocess($formStage->getInitialValues());
            }
        }

        return $form->getFormForStage($stageNumber, $input);
    }

    protected function findFieldFromBracketSyntaxName(IForm $form, string $fieldName)
    {
        $parts = array_map(function (string $part) {
            return trim($part, '][');
        }, explode('[', $fieldName));

        foreach ($parts as $key => $part) {
            if (!$form->hasField($part)) {
                return null;
            }

            $field     = $form->getField($part);
            $fieldType = $field->getType();

            $isLastPart = $key === count($parts) - 1;

            if ($isLastPart) {
                return $field;
            } elseif ($fieldType instanceof InnerFormType) {
                $form = $fieldType->getForm();
            } elseif ($fieldType instanceof ArrayOfType) {
                $nextPart = $parts[$key + 1];

                $form = Form::create()->section('', [
                    $fieldType->getElementField()->withName($nextPart)
                ])->build();
            } else {
                return null;
            }
        }
    }

    /**
     * @param IModule $module
     * @param string  $actionName
     *
     * @return IAction
     */
    protected function loadAction(IModule $module, string $actionName, ServerRequestInterface $request) : IAction
    {
        try {
            $action = $module->getAction($actionName);

            if (!$action->isAuthorized()) {
                DmsError::abort($request, 401);
            }

            return $action;
        } catch (ActionNotFoundException $e) {
            $response = new JsonResponse([
                'message' => 'Invalid action name',
            ], 404);
        }

        throw new HttpResponseException($response);
    }

    /**
     * @param string $objectId
     * @param        $action
     *
     * @return mixed
     */
    protected function loadObject(string $objectId, IObjectAction $action) : ITypedObject
    {
        try {
            /** @var ObjectIdType $objectField */
            $objectFieldType = $action->getObjectForm()->getField(IObjectAction::OBJECT_FIELD_NAME)->getType();

            return $this->loadObjectFromDataSource($objectId, $objectFieldType->getObjects());
        } catch (InvalidInputException $e) {
            DmsError::abort($request, 404);
        }
    }

    protected function loadObjectFromDataSource(string $objectId, IIdentifiableObjectSet $dataSource) : ITypedObject
    {
        return $dataSource instanceof IRepository ? $dataSource->get((int)$objectId) : $dataSource->get($objectId);
    }
}