<?php

namespace Dms\Web\Expressive\Tests\Unit\Auth\Password;

use Dms\Web\Expressive\Auth\Password\BcryptPasswordHasher;
use Dms\Web\Expressive\Auth\Password\HashAlgorithmNotFoundException;
use Dms\Web\Expressive\Auth\Password\HashedPassword;
use Dms\Web\Expressive\Auth\Password\PasswordHasherFactory;
use PHPUnit\Framework\TestCase;

/**
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class PasswordHasherFactoryTest extends TestCase
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

        $this->expectException(HashAlgorithmNotFoundException::class);

        $this->factory->build('some-invalid-algorithm', 10);

        // $this->assertThrows(function () {
        //     $this->factory->build('some-invalid-algorithm', 10);
        // }, HashAlgorithmNotFoundException::class);
    }

    public function testBuildForExistingPassword()
    {
        $password = new HashedPassword('--some-hash--', 'bcrypt', 8);

        $hasher = $this->factory->buildFor($password);

        $this->assertInstanceOf(BcryptPasswordHasher::class, $hasher);
        $this->assertSame(8, $hasher->getCostFactor());

        $this->expectException(HashAlgorithmNotFoundException::class);

        $this->factory->buildFor(new HashedPassword('--some-hash--', 'some-invalid-algorithm', 8));
    }
}
