<?php

namespace Dms\Web\Laravel\Tests\Unit\Auth\Password;

use Dms\Web\Laravel\Auth\Password\BcryptPasswordHasher;
use Dms\Web\Laravel\Tests\Unit\UnitTest;

/**
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class BcryptPasswordHasherTest extends UnitTest
{
    /**
     * @var BcryptPasswordHasher
     */
    protected $hasher;

    public function setUp()
    {
        parent::setUp();

        $this->hasher = new BcryptPasswordHasher(10);
    }

    public function testGetters()
    {
        $this->assertSame('bcrypt', $this->hasher->getAlgorithm());
        $this->assertSame(10, $this->hasher->getCostFactor());
    }

    public function testHashing()
    {
        $hash = $this->hasher->hash('password');

        $this->assertNotEquals('password', $hash);
        $this->assertSame(false, $this->hasher->verify('abc', $hash));
        $this->assertSame(false, $this->hasher->verify('password1', $hash));

        $this->assertSame(true, $this->hasher->verify('password', $hash));
    }
}