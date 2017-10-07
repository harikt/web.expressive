<?php

namespace Dms\Web\Expressive\Tests\Unit\Action\ExceptionHandler;

use Dms\Common\Structure\Field;
use Dms\Core\Form\Builder\Form;
use Dms\Core\Form\InvalidFormSubmissionException;
use Dms\Web\Expressive\Action\ExceptionHandler\InvalidFormSubmissionExceptionHandler;
use Dms\Web\Expressive\Action\IActionExceptionHandler;
use Dms\Web\Expressive\Tests\Mock\Language\MockLanguageProvider;
use Zend\Diactoros\Response\JsonResponse;

/**
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class InvalidFormSubmissionExceptionHandlerTest extends ExceptionHandlerTest
{
    protected function buildHandler() : IActionExceptionHandler
    {
        return new InvalidFormSubmissionExceptionHandler(new MockLanguageProvider());
    }

    protected function simpleInvalidFormSubmission()
    {
        try {
            Form::create()->section('Section', [
                Field::create('string', 'String')->string()->minLength(5)->required(),
            ])->build()->process([
                'string' => 'abc',
            ]);
        } catch (InvalidFormSubmissionException $e) {
            return $e;
        }
    }

    protected function simpleSubmissionResponse()
    {
        return new JsonResponse([
            'messages' => ['fields' => ['string' => ['validation.min-length:[field=String,input=abc,min_length=5]']], 'constraints' => []],
        ], 422);
    }

    protected function complexInvalidFormSubmission()
    {
        try {
            Form::create()->section('Section', [
                Field::create('password', 'Password')->string()->minLength(5)->required(),
                Field::create('password_confirm', 'Confirm Password')->string()->required(),
                Field::create('inner', 'Inner')->form(
                    Form::create()->section('Inner', [
                        Field::create('string', 'String')->string()->required(),
                    ])->build()
                )->required(),
            ])
                ->fieldsMatch('password', 'password_confirm')
                ->build()
                ->process([
                    'password'         => 'abc',
                    'password_confirm' => '123ffddf',
                ]);
        } catch (InvalidFormSubmissionException $e) {
            return $e;
        }
    }

    protected function complexSubmissionResponse()
    {
        return new JsonResponse([
            'messages' => [
                'fields'      => [
                    'password'         => ['validation.min-length:[field=Password,input=abc,min_length=5]'],
                    'password_confirm' => [],
                    'inner'            => [
                        'validation.required:[field=Inner,input=]',
                    ],
                ],
                'constraints' => [
                    'validation.matching-fields:[field1=Password,field2=Confirm Password]',
                ],
            ],
        ], 422);
    }

    public function exceptionsHandlingTests() : array
    {
        return [
            [$this->mockAction(), $this->simpleInvalidFormSubmission(), $this->simpleSubmissionResponse()],
            [$this->mockAction(), $this->complexInvalidFormSubmission(), $this->complexSubmissionResponse()],
        ];
    }

    public function unhandleableExceptionTests() : array
    {
        return [
            [$this->mockAction(), new \Exception()],
        ];
    }

    protected function assertResponsesMatch($expected, $actual)
    {
        /** @var JsonResponse $expected */
        /** @var JsonResponse $actual */
        $this->assertEquals(json_decode($expected->getBody(), true), json_decode($actual->getBody(), true));
    }
}
