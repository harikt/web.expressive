<?php declare(strict_types=1);

namespace Dms\Web\Expressive\Http\Controllers\Package\Module\Action;

use Dms\Core\Auth\IAuthSystem;
use Dms\Core\Common\Crud\Action\Object\IObjectAction;
use Dms\Core\Form\Builder\Form;
use Dms\Core\Form\Field\Type\ObjectIdType;
use Dms\Core\Form\IForm;
use Dms\Core\Form\InvalidInputException;
use Dms\Core\ICms;
use Dms\Core\Language\ILanguageProvider;
use Dms\Core\Model\IIdentifiableObjectSet;
use Dms\Core\Model\ITypedObject;
use Dms\Core\Module\ActionNotFoundException;
use Dms\Core\Module\IAction;
use Dms\Core\Module\IModule;
use Dms\Core\Module\IParameterizedAction;
use Dms\Core\Persistence\IRepository;
use Dms\Web\Expressive\Action\ActionExceptionHandlerCollection;
use Dms\Web\Expressive\Action\ActionInputTransformerCollection;
use Dms\Web\Expressive\Action\ActionResultHandlerCollection;
use Dms\Web\Expressive\Error\DmsError;
use Dms\Web\Expressive\Http\Controllers\DmsController;
use Dms\Web\Expressive\Http\Controllers\Package\Module\ModuleContextTrait;
use Dms\Web\Expressive\Http\ModuleContext;
use Dms\Web\Expressive\Renderer\Action\ObjectActionButtonBuilder;
use Dms\Web\Expressive\Renderer\Form\ActionFormRenderer;
use Dms\Web\Expressive\Renderer\Form\FormRenderingContext;
use Dms\Web\Expressive\Util\ActionSafetyChecker;
use Illuminate\Http\Exceptions\HttpResponseException;
use Interop\Http\ServerMiddleware\DelegateInterface;
use Interop\Http\ServerMiddleware\MiddlewareInterface as ServerMiddlewareInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Diactoros\Response\HtmlResponse;
use Zend\Diactoros\Response\JsonResponse;
use Zend\Expressive\Router\RouterInterface;
use Zend\Expressive\Template\TemplateRendererInterface;

/**
 * The action controller
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class FormStageController extends DmsController implements ServerMiddlewareInterface
{
    use ModuleContextTrait;

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

    /**
     * ActionController constructor.
     *
     * @param ICms                             $cms
     * @param IAuthSystem                      $auth
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
        TemplateRendererInterface $template,
        RouterInterface $router,
        ActionInputTransformerCollection $inputTransformers,
        ActionResultHandlerCollection $resultHandlers,
        ActionExceptionHandlerCollection $exceptionHandlers,
        ActionSafetyChecker $actionSafetyChecker,
        ActionFormRenderer $actionFormRenderer,
        ObjectActionButtonBuilder $actionButtonBuilder
    ) {
        parent::__construct($cms, $auth, $template, $router);
        $this->lang                = $cms->getLang();
        $this->inputTransformers   = $inputTransformers;
        $this->resultHandlers      = $resultHandlers;
        $this->exceptionHandlers   = $exceptionHandlers;
        $this->actionSafetyChecker = $actionSafetyChecker;
        $this->actionFormRenderer  = $actionFormRenderer;
        $this->actionButtonBuilder = $actionButtonBuilder;
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
        if ($action instanceof ResponseInterface) {
            return $action;
        }

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

        if (isset($request->getParsedBody()['__initial_dependent_data'])) {
            for ($i = 1; $i < $stageNumber; $i++) {
                $formStage = $form->getFormForStage($i, $input);
                $input += $formStage->unprocess($formStage->getInitialValues());
            }
        }

        return $form->getFormForStage($stageNumber, $input);
    }

    public function process(ServerRequestInterface $request, DelegateInterface $delegate)
    {
        $moduleContext = $this->getModuleContext($request, $this->router, $this->cms);

        $objectId = $request->getAttribute('object_id');
        $actionName = $request->getAttribute('action');
        $stageNumber = (int) $request->getAttribute('stage');
        $formRendererAction = $request->getAttribute('form_action');

        if (!$objectId) {
            $objectId = isset($request->getParsedBody()[IObjectAction::OBJECT_FIELD_NAME]) ? (string)$request->getParsedBody()[IObjectAction::OBJECT_FIELD_NAME] : null;
        }

        $module = $moduleContext->getModule();
        $action = $this->loadAction($module, $actionName, $request);

        try {
            $form = $this->loadFormStage($request, $moduleContext, $actionName, $stageNumber, $objectId, $object);
        } catch (\Exception $e) {
            return $this->exceptionHandlers->handle($moduleContext, $action, $e);
        }

        $renderingContext = new FormRenderingContext($moduleContext, $action, $stageNumber, $object);

        $this->loadSharedViewVariables($request);

        return new HtmlResponse($this->actionFormRenderer->renderFormFields($renderingContext, $form));
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
                return DmsError::abort($request, 401);
            }

            return $action;
        } catch (ActionNotFoundException $e) {
            $response = new JsonResponse([
                'message' => 'Invalid action name',
            ], 404);
        }

        return $response;
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
            return DmsError::abort($request, 404);
        }
    }

    protected function loadObjectFromDataSource(string $objectId, IIdentifiableObjectSet $dataSource) : ITypedObject
    {
        return $dataSource instanceof IRepository ? $dataSource->get((int)$objectId) : $dataSource->get($objectId);
    }
}
