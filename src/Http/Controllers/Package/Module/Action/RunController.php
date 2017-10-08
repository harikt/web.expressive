<?php declare(strict_types=1);

namespace Dms\Web\Expressive\Http\Controllers\Package\Module\Action;

use Dms\Core\Auth\IAuthSystem;
use Dms\Core\ICms;
use Dms\Core\Language\ILanguageProvider;
use Dms\Core\Module\ActionNotFoundException;
use Dms\Core\Module\IAction;
use Dms\Core\Module\IModule;
use Dms\Core\Module\IParameterizedAction;
use Dms\Core\Module\IUnparameterizedAction;
use Dms\Web\Expressive\Action\ActionExceptionHandlerCollection;
use Dms\Web\Expressive\Action\ActionInputTransformerCollection;
use Dms\Web\Expressive\Action\ActionResultHandlerCollection;
use Dms\Web\Expressive\Action\UnhandleableActionExceptionException;
use Dms\Web\Expressive\Action\UnhandleableActionResultException;
use Dms\Web\Expressive\Error\DmsError;
use Dms\Web\Expressive\Http\Controllers\DmsController;
use Dms\Web\Expressive\Http\Controllers\Package\Module\ModuleContextTrait;
use Dms\Web\Expressive\Http\ModuleContext;
use Dms\Web\Expressive\Renderer\Action\ObjectActionButtonBuilder;
use Dms\Web\Expressive\Renderer\Form\ActionFormRenderer;
use Dms\Web\Expressive\Util\ActionSafetyChecker;
use Interop\Http\ServerMiddleware\DelegateInterface;
use Interop\Http\ServerMiddleware\MiddlewareInterface as ServerMiddlewareInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Diactoros\Response\JsonResponse;
use Zend\Expressive\Router\RouterInterface;
use Zend\Expressive\Template\TemplateRendererInterface;

/**
 * The action controller
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class RunController extends DmsController implements ServerMiddlewareInterface
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
        $actionName = $request->getAttribute('action');

        $moduleContext = $this->getModuleContext($request, $this->router, $this->cms);

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
     * @return \Zend\Diactoros\Response\JsonResponse
     */
    protected function handleUnknownHandlerException(\Exception $e)
    {
        return new JsonResponse([
            'message_type' => 'danger',
            'message'      => 'An internal error occurred',
        ], 500);
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
