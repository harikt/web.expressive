<?php declare(strict_types=1);

namespace Dms\Web\Expressive\Auth\Password;

use Dms\Core\Auth\IHashedPassword;
use Dms\Core\Model\Object\ClassDefinition;
use Dms\Core\Model\Object\ValueObject;

/**
 * The hashed password value object.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class HashedPassword extends ValueObject implements IHashedPassword
{
    const HASH = 'hash';
    const ALGORITHM = 'algorithm';
    const COST_FACTOR = 'costFactor';

    /**
     * @var string
     */
    private $hash;

    /**
     * @var string
     */
    private $algorithm;

    /**
     * @var int
     */
    private $costFactor;

    /**
     * HashedPassword constructor.
     *
     * @param string $hash
     * @param string $algorithm
     * @param int    $costFactor
     */
    public function __construct(string $hash, string $algorithm, int $costFactor)
    {
        parent::__construct();
        $this->hash = $hash;
        $this->algorithm = $algorithm;
        $this->costFactor = $costFactor;
    }

    /**
     * @param IHashedPassword $password
     *
     * @return self
     */
    public static function from(IHashedPassword $password) : self
    {
        if ($password instanceof self) {
            return $password;
        }

        return new self($password->getHash(), $password->getAlgorithm(), $password->getCostFactor());
    }

    /**
     * Defines the structure of this class.
     *
     * @param ClassDefinition $class
     */
    protected function define(ClassDefinition $class)
    {
        $class->property($this->hash)->asString();
        $class->property($this->algorithm)->asString();
        $class->property($this->costFactor)->asInt();
    }

    /**
     * {@inheritDoc}
     */
    public function getHash() : string
    {
        return $this->hash;
    }

    /**
     * {@inheritDoc}
     */
    public function getAlgorithm() : string
    {
        return $this->algorithm;
    }

    /**
     * {@inheritDoc}
     */
    public function getCostFactor() : int
    {
        return $this->costFactor;
    }
}
