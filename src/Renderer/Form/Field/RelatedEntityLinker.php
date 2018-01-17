<?php declare(strict_types=1);

namespace Dms\Web\Expressive\Renderer\Form\Field;

use Dms\Core\Common\Crud\ICrudModule;
use Dms\Core\Form\Field\Options\EntityIdOptions;
use Dms\Core\Form\IFieldOptions;
use Dms\Web\Expressive\Util\EntityModuleMap;
use Zend\Expressive\Router\RouterInterface;

/**
 * The related entity linker helper class.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class RelatedEntityLinker
{

    /**
     * EntityModuleMap
     *
     * @var EntityModuleMap
     */
    protected $entityModuleMap;

    /**
     *
     * @var RouterInterface
     */
    protected $router;

    /**
     * 
     * @param EntityModuleMap $entityModuleMap
     */
    public function __construct(EntityModuleMap $entityModuleMap, RouterInterface $router)
    {
        $this->router = $router;
        $this->entityModuleMap = $entityModuleMap;
    }

    /**
     * @param IFieldOptions $options
     *
     * @return callable|null
     */
    public function getUrlCallbackFor(IFieldOptions $options)
    {
        if (!($options instanceof EntityIdOptions)) {
            return null;
        }

        if ($this->entityModuleMap->hasModuleFor($options->getObjects()->getObjectType())) {
            $module = $this->entityModuleMap->loadModuleFor($options->getObjects()->getObjectType());

            if ($module instanceof ICrudModule && $module->allowsEdit() && $module->getEditAction()->isAuthorized()) {
                return function ($id) use ($module) {
                    return $this->router->generateUri('dms::package.module.action.form', ['package' => $module->getPackageName(), 'module' => $module->getName(), 'action' => $module->getEditAction()->getName(), 'object_id' => $id]);
                };
            }

            if ($module->allowsDetails() && $module->getDetailsAction()->isAuthorized()) {
                return function ($id) use ($module) {
                    return $this->router->generateUri('dms::package.module.action.show', ['package' => $module->getPackageName(), 'module' => $module->getName(), 'action' => $module->getDetailsAction()->getName(), 'object_id' => $id]);
                };
            }
        }

        return null;
    }
}
