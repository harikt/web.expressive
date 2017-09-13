<?php declare(strict_types=1);

namespace Dms\Web\Expressive\Auth\Persistence;

use Dms\Core\Persistence\Db\Connection\IConnection;
use Dms\Core\Persistence\Db\Mapping\IOrm;
use Dms\Core\Persistence\DbRepository;
use Dms\Web\Expressive\Auth\Password\PasswordResetToken;

/**
 * The laravel password reset token repository.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class PasswordResetTokenRepository extends DbRepository
{
    public function __construct(IConnection $connection, IOrm $orm)
    {
        parent::__construct($connection, $orm->getEntityMapper(PasswordResetToken::class));
    }
}
