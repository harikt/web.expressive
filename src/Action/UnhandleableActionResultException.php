<?php declare(strict_types=1);

namespace Dms\Web\Expressive\Action;

use Dms\Core\Exception\BaseException;

/**
 * The exception for action results which have no specified handler.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class UnhandleableActionResultException extends BaseException
{
}
