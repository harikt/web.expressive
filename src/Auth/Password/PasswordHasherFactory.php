<?php declare(strict_types=1);

namespace Dms\Web\Expressive\Auth\Password;

use Dms\Core\Auth\IHashedPassword;
use Dms\Core\Exception;
use Dms\Core\Util\Debug;

/**
 * The password hasher factory.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class PasswordHasherFactory implements IPasswordHasherFactory
{
    /**
     * @var IPasswordHasher[]
     */
    protected $hasherFactories = [];

    /**
     * @var string
     */
    protected $defaultAlgorithm;

    /**
     * @var int
     */
    protected $defaultCostFactor;

    /**
     * PasswordHasherFactory constructor.
     *
     * @param callable[] $hasherFactories
     * @param string     $defaultAlgorithm
     * @param int        $defaultCostFactor
     *
     * @internal param IPasswordHasher $defaultHasher
     */
    public function __construct(array $hasherFactories, string $defaultAlgorithm, int $defaultCostFactor)
    {
        $this->defaultAlgorithm = $defaultAlgorithm;
        $this->defaultCostFactor = $defaultCostFactor;

        foreach ($hasherFactories as $algorithm => $hasherFactory) {
            $this->hasherFactories[$algorithm] = $hasherFactory;
        }
    }


    /**
     * Builds the default password hasher.
     *
     * @return IPasswordHasher
     */
    public function buildDefault() : IPasswordHasher
    {
        return $this->build($this->defaultAlgorithm, $this->defaultCostFactor);
    }

    /**
     * Builds a password hasher with the supplied settings
     *
     * @param string $algorithm
     * @param int    $costFactor
     *
     * @return IPasswordHasher
     * @throws HashAlgorithmNotFoundException
     */
    public function build(string $algorithm, int $costFactor) : IPasswordHasher
    {
        if (!isset($this->hasherFactories[$algorithm])) {
            throw HashAlgorithmNotFoundException::format(
                'Invalid algorithm supplied to %s: expecting one of (%s), \'%s\' given',
                __METHOD__,
                Debug::formatValues(array_keys($this->hasherFactories)),
                $algorithm
            );
        }

        return $this->hasherFactories[$algorithm]($costFactor);
    }

    /**
     * Builds a password hasher matching the supplied hashed password
     *
     * @param IHashedPassword $hashedPassword
     *
     * @return IPasswordHasher
     * @throws Exception\InvalidArgumentException
     */
    public function buildFor(IHashedPassword $hashedPassword) : IPasswordHasher
    {
        return $this->build($hashedPassword->getAlgorithm(), $hashedPassword->getCostFactor());
    }
}
