<?php
namespace Dms\Web\Expressive\Tests\Unit\Language;

use Dms\Core\Language\Message;
use Dms\Web\Expressive\Language\SymfonyLanguageProvider;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Translation\Translator;
use Symfony\Component\Translation\Loader\ArrayLoader;
use Symfony\Component\Translation\MessageSelector;

/**
 * @author Hari KT <kthari85@gmail.com>
 */
class SymfonyLanguageProviderTest extends TestCase
{
    protected $languageProvider;

    protected $transArray;

    public function setUp()
    {
        $translator = new Translator('en_US', new MessageSelector());
        $translator->addLoader('array', new ArrayLoader());
        $this->transArray = require dirname(dirname(dirname(dirname(__DIR__)))) . '/resources/lang/en_US.php';
        $translator->addResource('array', $this->transArray, 'en_US', 'dms');
        $this->languageProvider = new SymfonyLanguageProvider($translator);
    }

    public function testFormatWithNoParams()
    {
        $key = 'auth.failed';
        $message = new Message($key);
        $actual = $this->languageProvider->format($message);
        $this->assertSame($this->transArray[$key], $actual);
    }

    public function testFormatWithParams()
    {
        $message = new Message('validation.accepted', ['field' => 'phone']);
        $actual = $this->languageProvider->format($message);
        $this->assertSame('The phone must be accepted.', $actual);
    }

    public function testFormatAll()
    {
        $messages = [
            new Message('passwords.reset'),
            new Message('validation.accepted', ['field' => 'phone'])
        ];

        $actual = $this->languageProvider->formatAll($messages);
        $expected = [
            'Your password has been reset!',
            'The phone must be accepted.'
        ];
        $this->assertSame($expected, $actual);
    }
}
