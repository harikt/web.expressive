<?php declare(strict_types=1);
namespace Dms\Web\Expressive\Exception;

use Dms\Core\Exception\BaseException;

class ValidationFailedException extends BaseException
{
    public static function defaultMessage()
    {
        return new self('Failed to validate constraints');
    }
}
