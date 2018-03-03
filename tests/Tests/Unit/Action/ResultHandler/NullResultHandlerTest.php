<?php

namespace Dms\Web\Expressive\Tests\Unit\Action\ResultHandler;

use Dms\Web\Expressive\Action\IActionResultHandler;
use Dms\Web\Expressive\Action\ResultHandler\NullResultHandler;
use Symfony\Component\Translation\Translator;
use Symfony\Component\Translation\Loader\ArrayLoader;
use Symfony\Component\Translation\MessageSelector;
use Zend\Diactoros\Response\JsonResponse;

/**
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class NullResultHandlerTest extends ResultHandlerTest
{
    protected function buildHandler() : IActionResultHandler
    {
        $translator = new Translator('en_US', new MessageSelector());
        $translator->addLoader('array', new ArrayLoader());
        $kv = require dirname(dirname(dirname(dirname(dirname(__DIR__))))) . '/resources/lang/en_US.php';
        $translator->addResource('array', $kv, 'en_US', 'dms');

        return new NullResultHandler($translator);
    }

    public function resultHandlingTests() : array
    {
        return [
            [$this->mockAction(), null, new JsonResponse(['message' => 'The action was successfully executed'])],
        ];
    }

    public function unhandleableResultTests() : array
    {
        return [
            [$this->mockAction(\stdClass::class), new \stdClass()],
        ];
    }
}
