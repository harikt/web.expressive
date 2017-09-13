<?php declare(strict_types=1);

namespace Dms\Web\Expressive\Util;

use Dms\Core\Module\IAction;

/**
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class ActionLabeler
{
    /**
     * @param IAction $action
     *
     * @return string
     */
    public static function getActionButtonLabel(IAction $action) : string
    {
        $metadata = $action->getMetadata();

        if (isset($metadata['label'])) {
            return $metadata['label'];
        }

        return StringHumanizer::title($action->getName());
    }

    /**
     * @param IAction $action
     *
     * @return string
     */
    public static function getSubmitButtonLabel(IAction $action) : string
    {
        $metadata = $action->getMetadata();

        if (isset($metadata['submit-button-text'])) {
            return $metadata['submit-button-text'];
        }

        return StringHumanizer::title($action->getName());
    }
}
