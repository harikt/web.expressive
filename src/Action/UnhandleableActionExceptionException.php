<?php declare(strict_types=1);

namespace Dms\Web\Expressive\Action;

use Dms\Core\Exception\BaseException;

/**
 * The exception for action exceptions which have no specified handler.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class UnhandleableActionExceptionException extends BaseException
{
}
