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
use Interop\Http\ServerMiddleware\DelegateInterface;
use Interop\Http\ServerMiddleware\MiddlewareInterface as ServerMiddlewareInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Expressive\Router\RouterInterface;
use Zend\Expressive\Template\TemplateRendererInterface;
use Zend\Diactoros\Response\JsonResponse;

/**
 * The action controller
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class RunController extends DmsController implements ServerMiddlewareInterface
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

    public function process(ServerRequestInterface $request, DelegateInterface $delegate)
    {
        $packageName = $request->getAttribute('package');
        $moduleName = $request->getAttribute('module');
        $actionName = $request->getAttribute('action');

        $moduleContext = ModuleContext::rootContext($this->router, $packageName, $moduleName, function () use ($packageName, $moduleName) {
            $package = $this->cms->loadPackage($packageName);

            return $package->loadModule($moduleName);
        });

        $action = $this->loadAction($moduleContext->getModule(), $actionName, $request);
        if ($action instanceof ResponseInterface) {
            return $action;
        }

        $this->loadSharedViewVariables($request);

        try {
            $result = $this->runActionWithDataFromRequest($request, $moduleContext, $action);
        } catch (\Exception $e) {
            return $this->handleActionException($moduleContext, $action, $e);
        }

        try {
            return $this->resultHandlers->handle($moduleContext, $action, $result);
        } catch (UnhandleableActionResultException $e) {
            return $this->handleUnknownHandlerException($e);
        }
    }

    /**
     * @param ServerRequestInterface       $request
     * @param ModuleContext $moduleContext
     * @param IAction       $action
     * @param array         $extraData
     *
     * @return mixed
     */
    protected function runActionWithDataFromRequest(ServerRequestInterface $request, ModuleContext $moduleContext, IAction $action, array $extraData = [])
    {
        if ($action instanceof IParameterizedAction) {
            /** @var IParameterizedAction $action */
            $input  = $this->inputTransformers->transform($moduleContext, $action, $request->getParsedBody() + $request->getQueryParams() + $extraData);
            $result = $action->run($input);

            return $result;
        } else {
            /** @var IUnparameterizedAction $action */
            $result = $action->run();

            return $result;
        }
    }

    /**
     * @param ModuleContext $moduleContext
     * @param IAction       $action
     * @param \Exception    $e
     *
     * @return mixed
     * @throws \Exception
     */
    protected function handleActionException(ModuleContext $moduleContext, IAction $action, \Exception $e)
    {
        try {
            return $this->exceptionHandlers->handle($moduleContext, $action, $e);
        } catch (UnhandleableActionExceptionException $e) {
            return $this->handleUnknownHandlerException($e);
        }
    }

    /**
     * @param \Exception $e
     *
     * @return \Illuminate\Http\JsonResponse
     * @throws
     */
    protected function handleUnknownHandlerException(\Exception $e)
    {
        if (app()->isLocal()) {
            throw $e;
        } else {
            if ($e instanceof UnhandleableActionExceptionException) {
                $e = $e->getPrevious();
            }

            // log error
            // $e->getMessage() . $e->getTraceAsString();

            return new JsonResponse([
                'message_type' => 'danger',
                'message'      => 'An internal error occurred',
            ], 500);
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
                return DmsError::abort($request, 401);
            }

            return $action;
        } catch (ActionNotFoundException $e) {
            return new JsonResponse([
                'message' => 'Invalid action name',
            ], 404);
        }
    }
}
