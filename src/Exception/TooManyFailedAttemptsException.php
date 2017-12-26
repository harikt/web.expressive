<?php declare(strict_types=1);
namespace Dms\Web\Expressive\Exception;

use Dms\Core\Exception\BaseException;

class TooManyFailedAttemptsException extends BaseException
{
    public static function defaultMessage()
    {
        return new self('Too many failed attempts');
    }
}
