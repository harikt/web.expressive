<?php

namespace Dms\Web\Laravel\Tests\Unit\Auth\Password;

use Dms\Web\Laravel\Auth\Password\BcryptPasswordHasher;
use Dms\Web\Laravel\Auth\Password\HashAlgorithmNotFoundException;
use Dms\Web\Laravel\Auth\Password\HashedPassword;
use Dms\Web\Laravel\Auth\Password\PasswordHasherFactory;
use Dms\Web\Laravel\Tests\Unit\UnitTest;

/**
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class PasswordHasherFactoryTest extends UnitTest
{
    /**
     * @var PasswordHasherFactory
     */
    protected $factory;

    public function setUp()
    {
        parent::setUp();

        $this->factory = new PasswordHasherFactory(
            [
                'bcrypt' => function (int $costFactor) {
                    return new BcryptPasswordHasher($costFactor);
                },
            ],
            'bcrypt',
            10
        );
    }

    public function testDefault()
    {
        $hasher = $this->factory->buildDefault();

        $this->assertInstanceOf(BcryptPasswordHasher::class, $hasher);
        $this->assertSame(10, $hasher->getCostFactor());
    }

    public function testBuildCustom()
    {
        $hasher = $this->factory->build('bcrypt', 5);

        $this->assertInstanceOf(BcryptPasswordHasher::class, $hasher);
        $this->assertSame(5, $hasher->getCostFactor());

        $this->assertThrows(function () {
            $this->factory->build('some-invalid-algorithm', 10);
        }, HashAlgorithmNotFoundException::class);
    }

    public function testBuildForExistingPassword()
    {
        $password = new HashedPassword('--some-hash--', 'bcrypt', 8);

        $hasher = $this->factory->buildFor($password);

        $this->assertInstanceOf(BcryptPasswordHasher::class, $hasher);
        $this->assertSame(8, $hasher->getCostFactor());

        $this->assertThrows(function () {
            $this->factory->buildFor(new HashedPassword('--some-hash--', 'some-invalid-algorithm', 8));
        }, HashAlgorithmNotFoundException::class);
    }
}