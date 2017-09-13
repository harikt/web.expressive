<?php declare(strict_types=1);

namespace Dms\Web\Expressive\Auth\Password;

use Dms\Core\Exception\BaseException;

/**
 * Exception for a non existent hash algorithm
 *
 * @author Elliot Levin <elliot@aanet.com.au>
 */
class HashAlgorithmNotFoundException extends BaseException
{
}
