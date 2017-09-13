<?php declare(strict_types=1);

namespace Dms\Web\Expressive\Action\ExceptionHandler;

use Dms\Core\Form\InvalidFormSubmissionException;
use Dms\Core\Language\ILanguageProvider;
use Dms\Core\Module\IAction;
use Dms\Web\Expressive\Action\ActionExceptionHandler;
use Dms\Web\Expressive\Http\ModuleContext;
use Zend\Diactoros\Response\JsonResponse;

/**
 * The invalid form submission exception handler.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class InvalidFormSubmissionExceptionHandler extends ActionExceptionHandler
{
    /**
     * @var ILanguageProvider
     */
    protected $lang;

    /**
     * InvalidFormSubmissionExceptionHandler constructor.
     *
     * @param ILanguageProvider $lang
     */
    public function __construct(ILanguageProvider $lang)
    {
        parent::__construct();
        $this->lang = $lang;
    }

    /**
     * @return string|null
     */
    protected function supportedExceptionType()
    {
        return InvalidFormSubmissionException::class;
    }

    /**
     * @param ModuleContext $moduleContext
     * @param IAction       $action
     * @param \Exception    $exception
     *
     * @return bool
     */
    protected function canHandleException(ModuleContext $moduleContext, IAction $action, \Exception $exception) : bool
    {
        /** @var InvalidFormSubmissionException $exception */
        return true;
    }

    /**
     * @param ModuleContext $moduleContext
     * @param IAction       $action
     * @param \Exception    $exception
     *
     * @return Response|mixed
     */
    protected function handleException(ModuleContext $moduleContext, IAction $action, \Exception $exception)
    {
        /** @var InvalidFormSubmissionException $exception */
        return new JsonResponse([
            'messages' => $this->transformInvalidFormSubmissionToArray($exception),
        ], 422);
    }

    /**
     * @param InvalidFormSubmissionException $exception
     *
     * @return array
     */
    private function transformInvalidFormSubmissionToArray(InvalidFormSubmissionException $exception) : array
    {
        $validation = [
            'fields'      => [],
            'constraints' => [],
        ];

        foreach ($exception->getFieldMessageMap() as $field => $messages) {
            if (!empty($messages) && is_array(reset($messages))) {
                continue;
            }

            $validation['fields'][$field] = $this->lang->formatAll($messages);
        }

        foreach ($exception->getInvalidInnerFormSubmissionExceptions() as $field => $innerException) {
            $validation['fields'][$field] = $this->transformInvalidFormSubmissionToArray($innerException);
        }

        $validation['constraints'] = $this->lang->formatAll($exception->getAllConstraintMessages());

        return $validation;
    }
}
