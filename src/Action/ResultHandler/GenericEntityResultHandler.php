<?php declare(strict_types=1);

namespace Dms\Web\Expressive\Action\ResultHandler;

use Symfony\Component\Translation\Translator;
use Dms\Core\Common\Crud\Action\Crud\CreateAction;
use Dms\Core\Common\Crud\Action\Crud\EditAction;
use Dms\Core\Common\Crud\Action\Crud\ViewDetailsAction;
use Dms\Core\Common\Crud\Action\Object\IObjectAction;
use Dms\Core\Common\Crud\ICrudModule;
use Dms\Core\Model\Object\Entity;
use Dms\Core\Module\IAction;
use Dms\Web\Expressive\Action\ActionResultHandler;
use Dms\Web\Expressive\Http\ModuleContext;
use Dms\Web\Expressive\Util\EntityModuleMap;
use Zend\Diactoros\Response\JsonResponse;
use Zend\Expressive\Router\RouterInterface;

/**
 * The generic entity action result handler.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class GenericEntityResultHandler extends ActionResultHandler
{
    /**
     * @var EntityModuleMap|null
     */
    protected $entityModuleMap;

    /**
     * @var Translator
     */
    protected $translator;

    /**
     * @var RouterInterface
     */
    protected $router;

    public function __construct(EntityModuleMap $entityModuleMap, Translator $translator, RouterInterface $router)
    {
        $this->entityModuleMap = $entityModuleMap;
        $this->translator = $translator;
        $this->router = $router;
        parent::__construct();
    }

    protected function getEntityModuleMap() : EntityModuleMap
    {
        return $this->entityModuleMap;
    }

    /**
     * @return string|null
     */
    protected function supportedResultType()
    {
        return Entity::class;
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
        /** @var Entity $result */
        $class = get_class($result);

        return $result->getId() && $this->getEntityModuleMap()->hasModuleFor($class)
        && !($action instanceof IObjectAction && $action->getName() === ICrudModule::REMOVE_ACTION)
        && !($action instanceof EditAction)
        && !($action instanceof ViewDetailsAction)
        && !($action instanceof CreateAction);
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
        $module = $this->getEntityModuleMap()->loadModuleFor(get_class($result));

        if (!$module->getDetailsAction()->isAuthorized()) {
            return (new NullResultHandler())->handle($moduleContext, $action, null);
        }

        /** @var Entity $result */
        $url = $this->router->generateUri('dms::package.module.action.show', ['package' => $module->getPackageName(), 'module' => $module->getName(), 'action' => $module->getDetailsAction()->getName(), 'object_id' => $result->getId()]);

        return new JsonResponse([
            'message'  => $translator->trans('action.generic-response', [], 'dms'),
            'redirect' => $url,
        ]);
    }
}
