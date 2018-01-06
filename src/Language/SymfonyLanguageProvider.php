<?php declare(strict_types=1);

namespace Dms\Web\Expressive\Language;

use Dms\Core\Exception\InvalidArgumentException;
use Dms\Core\Language\ILanguageProvider;
use Dms\Core\Language\Message;
use Dms\Core\Language\MessageNotFoundException;
use Symfony\Component\Translation\Translator;

/**
 * The laravel language provider.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class SymfonyLanguageProvider implements ILanguageProvider
{
    /**
     * @var Translator
     */
    protected $translator;

    /**
     * LaravelLanguageProvider constructor.
     *
     * @param Translator $translator
     */
    public function __construct(Translator $translator)
    {
        $this->translator = $translator;
    }

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
        $domain = 'dms';

        if ($message->hasNamespace()) {
            $domain .= '.' . $message->getNamespace();
        }

        $response = $this->translator->trans(
            $message->getId(),
            $params = $this->processParameters($message->getParameters()),
            $domain
        );

        return $response;
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

    private function processParameters(array $parameters) : array
    {
        $processedParams = [];

        foreach ($parameters as $key => $value) {
            $symbol = '%' . $key . '%';
            if (is_array($value)) {
                $processedParams[$symbol] = implode(', ', $this->processParameters($value));
            } elseif (is_object($value) && method_exists($value, '__toString')) {
                $processedParams[$symbol] = (string)$value;
            } elseif (is_object($value)) {
                $processedParams[$symbol] = get_class($value);
            } else {
                $processedParams[$symbol] = (string)$value;
            }
        }

        return $processedParams;
    }

    private function debugFormatParams(array $parameters) : string
    {
        $elements = [];

        foreach ($parameters as $name => $value) {
            $elements[] = $name . ': ' . $value;
        }

        return '[' . implode(', ', $elements) . ']';
    }

    /**
     * @inheritdoc
     */
    public function addResourceDirectory(string $namespace, string $directory)
    {
        // do nothing
    }
}
