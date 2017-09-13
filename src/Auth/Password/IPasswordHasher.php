<?php declare(strict_types=1);

namespace Dms\Web\Expressive\Auth\Password;

use Dms\Core\Auth\IHashedPassword;

interface IPasswordHasher
{
    /**
     * Gets the hashing algorithm name.
     *
     * @return string
     */
    public function getAlgorithm() : string;

    /**
     * Gets the cost factor of the hashing algorithm.
     *
     * @return int
     */
    public function getCostFactor() : int;

    /**
     * Hashes the supplied password.
     *
     * @param string $password
     *
     * @return IHashedPassword
     */
    public function hash(string $password) : \Dms\Core\Auth\IHashedPassword;

    /**
     * Verifies the password string against the supplied hashed password.
     *
     * @param string          $password
     * @param IHashedPassword $hashedPassword
     *
     * @return boolean
     */
    public function verify(string $password, IHashedPassword $hashedPassword) : bool;
}
