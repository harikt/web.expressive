<?php declare(strict_types=1);

namespace Dms\Web\Expressive\Auth\Password;

use Dms\Core\Auth\IHashedPassword;

/**
 * The bcrypt password hasher
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class BcryptPasswordHasher implements IPasswordHasher
{
    const ALGORITHM = 'bcrypt';

    /**
     * @var int
     */
    protected $cost;

    /**
     * BcryptPasswordHasher constructor.
     *
     * @param int $cost
     */
    public function __construct(int $cost)
    {
        $this->cost = $cost;
    }

    /**
     * Gets the hashing algorithm name.
     *
     * @return string
     */
    public function getAlgorithm() : string
    {
        return self::ALGORITHM;
    }

    /**
     * Gets the cost factor of the hashing algorithm.
     *
     * @return int
     */
    public function getCostFactor() : int
    {
        return $this->cost;
    }

    /**
     * Hashes the supplied password.
     *
     * @param string $password
     *
     * @return IHashedPassword
     */
    public function hash(string $password) : IHashedPassword
    {
        return new HashedPassword(
            password_hash($password, PASSWORD_BCRYPT, ['cost' => $this->cost]),
            self::ALGORITHM,
            $this->cost
        );
    }

    /**
     * Verifies the password string against the supplied hashed password.
     *
     * @param string          $password
     * @param IHashedPassword $hashedPassword
     *
     * @return boolean
     */
    public function verify(string $password, IHashedPassword $hashedPassword) : bool
    {
        return password_verify($password, $hashedPassword->getHash());
    }
}
