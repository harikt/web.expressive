<?php declare(strict_types=1);

namespace Dms\Web\Expressive\Renderer\Form;

use Dms\Core\Form\IForm;
use Psr\Http\Message\ResponseInterface; 
use Psr\Http\Message\ServerRequestInterface;
use Zend\Diactoros\Response;

/**
 * The form renderer with actions interface.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
interface IFormRendererWithActions
{
    /**
     * @param FormRenderingContext $renderingContext
     * @param IForm                $form
     * @param Request              $request
     * @param string               $actionName
     * @param array                $data
     *
     * @return Response
     */
    public function handleAction(FormRenderingContext $renderingContext, IForm $form, ServerRequestInterface $request, string $actionName = null, array $data);
}
