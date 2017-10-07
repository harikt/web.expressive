<?php

namespace Dms\Web\Expressive\Tests\Mock\Language;

use Dms\Core\Exception\InvalidArgumentException;
use Dms\Core\Exception\NotImplementedException;
use Dms\Core\Language\ILanguageProvider;
use Dms\Core\Language\Message;
use Dms\Core\Language\MessageNotFoundException;

/**
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class MockLanguageProvider implements ILanguageProvider
{
    /**
     * Gets the fully formed message string from the supplied message id
     * and parameters
     *
     * @param Message $message
     *
     * @return string
     * @throws MessageNotFoundException
     */
    public function format(Message $message) : string
    {
        return $message->getId() . ($message->getParameters() ? ':[' . $this->formatParams($message->getParameters()) . ']' : '');
    }

    /**
     * Gets the fully formed message strings from the supplied message ids
     * and parameters
     *
     * @param Message[] $messages
     *
     * @return string[]
     * @throws InvalidArgumentException
     * @throws MessageNotFoundException
     */
    public function formatAll(array $messages) : array
    {
        InvalidArgumentException::verifyAllInstanceOf(__METHOD__, 'messages', $messages, Message::class);

        return array_map([$this, 'format'], $messages);
    }

    private function formatParams(array $params) : string
    {
        $elements = [];

        foreach ($params as $name => $value) {
            $elements[] = $name . '=' . $value;
        }

        return implode(',', $elements);
    }

    public function addResourceDirectory(string $namespace, string $directory)
    {
        throw NotImplementedException::method(__METHOD__);
    }
}
