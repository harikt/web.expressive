<?php declare(strict_types=1);

namespace Dms\Web\Expressive\Action\ResultHandler;

use Aura\Intl\TranslatorLocator;
use Dms\Core\Module\IAction;
use Dms\Web\Expressive\Action\ActionResultHandler;
use Dms\Web\Expressive\Http\ModuleContext;
use Zend\Diactoros\Response\JsonResponse;

/**
 * The null action result handler.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class NullResultHandler extends ActionResultHandler
{

    /**
     * @var TranslatorLocator
     */
    protected $translocator;

    public function __construct(TranslatorLocator $translocator)
    {
        $this->translocator = $translocator;
        parent::__construct();
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
        return $action->getReturnTypeClass() === null;
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
        $translator = $this->translocator->get('dms');

        return new JsonResponse([
            'message' => $translator->translate('dms::action.generic-response'),
        ]);
    }
}
