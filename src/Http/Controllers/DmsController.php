<?php declare(strict_types=1);

namespace Dms\Web\Expressive\Http\Controllers;

use Dms\Core\Auth\IAuthSystem;
use Dms\Core\ICms;
use Dms\Web\Expressive\Exception\ValidationFailedException;
use Illuminate\Support\MessageBag;
use Psr\Http\Message\ServerRequestInterface;
use Symfony\Component\Validator\Validation;
use Zend\Diactoros\Response;
use Zend\Expressive\Router\RouterInterface;
use Zend\Expressive\Template\TemplateRendererInterface;

/**
 * The base dms controller.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class DmsController
{

    /**
     * @var ICms
     */
    protected $cms;

    /**
     * @var IAuthSystem
     */
    protected $auth;

    /**
     * @var TemplateRendererInterface
     */
    protected $template;

    /**
     * @var RouterInterface
     */
    protected $router;

    /**
     * @var MessageBag
     */
    protected $errors;

    /**
     * DmsController constructor.
     *
     * @param ICms                      $cms
     * @param IAuthSystem               $auth
     * @param TemplateRendererInterface $template
     * @param RouterInterface           $router
     */
    public function __construct(
        ICms $cms,
        IAuthSystem $auth,
        TemplateRendererInterface $template,
        RouterInterface $router
    ) {
        $this->cms  = $cms;
        $this->auth = $auth;
        $this->template  = $template;
        $this->router = $router;
        $this->errors = new MessageBag();
    }

    protected function abort(ServerRequestInterface $request, int $statusCode, string $message = '')
    {
        $response = new Response('php://memory', $statusCode);
        if ('XMLHttpRequest' == $request->getHeaderLine('X-Requested-With')) {
            if ($statusCode == 401) {
                $message = json_encode(['redirect' => '/dms']);
            }
            $response->getBody()->write($message);
            return $response;
        }

        $response->getBody()->write($this->renderErrorView($statusCode));

        return $response;
    }

    /**
     * @param int $statusCode
     *
     * @return mixed
     * @throws \Exception
     * @throws \Throwable
     */
    protected function renderErrorView(int $statusCode)
    {
        return $this->template->render('dms::errors.' . $statusCode, [
                'title' => $statusCode,
                'pageTitle' => $statusCode,
                'user'  => $this->auth->isAuthenticated() ? $this->auth->getAuthenticatedUser() : null,
                'finalBreadcrumb' => $statusCode,
            ]);
    }


    protected function validate($data, $constraint)
    {
        $validator = Validation::createValidator();
        $violations = $validator->validate($data, $constraint);
        if (count($violations) != 0) {
            // Add violation to message bag
            foreach ($violations as $violation) {
                $this->errors->add(str_replace(['[', ']'], [''], $violation->getPropertyPath()), $violation->getMessage());
            }

            throw ValidationFailedException::defaultMessage();
        }
    }
}
