<?php declare(strict_types=1);

namespace Dms\Web\Expressive\Util;

use Dms\Core\Module\IAction;
use Illuminate\Contracts\Config\Repository;

/**
 * The action safety checker class.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class ActionSafetyChecker
{
    /**
     * @var string[]
     */
    protected $safeActionClasses;

    /**
     * ActionSafetyChecker constructor.
     *
     * @param Repository $config
     */
    public function __construct(Repository $config)
    {
        $this->safeActionClasses = $config->get('dms.actions.safe', []);
    }

    /**
     * @param IAction $action
     *
     * @return bool
     */
    public function isSafeToShowActionResultViaGetRequest(IAction $action) : bool
    {
        foreach ($this->safeActionClasses as $class) {
            if ($action instanceof $class) {
                return true;
            }
        }

        return false;
    }
}
