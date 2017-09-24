<?php declare(strict_types=1);

namespace Dms\Web\Expressive\Auth\Password;

use Dms\Core\Auth\IHashedPassword;

interface IPasswordHasherFactory
{
    /**
     * Builds the default password hasher.
     *
     * @return IPasswordHasher
     */
    public function buildDefault() : IPasswordHasher;

    /**
     * Builds a password hasher with the supplied settings
     *
     * @param string $algorithm
     * @param int    $costFactor
     *
     * @return IPasswordHasher
     * @throws HashAlgorithmNotFoundException
     */
    public function build(string $algorithm, int $costFactor) : IPasswordHasher;

    /**
     * Builds a password hasher matching the supplied hashed password
     *
     * @param IHashedPassword $hashedPassword
     *
     * @return IPasswordHasher
     * @throws HashAlgorithmNotFoundException
     */
    public function buildFor(IHashedPassword $hashedPassword) : IPasswordHasher;
}
