<?php declare(strict_types=1);

namespace Dms\Web\Expressive\Renderer\Form;

use Dms\Core\Form\IField;
use Psr\Http\Message\ResponseInterface; 
use Psr\Http\Message\ServerRequestInterface;
use Zend\Diactoros\Response;

/**
 * The field renderer with actions interface.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
interface IFieldRendererWithActions
{
    /**
     * @param FormRenderingContext $renderingContext
     * @param IField               $field
     * @param Request              $request
     * @param string               $actionName
     * @param array                $data
     *
     * @return Response
     */
    public function handleAction(FormRenderingContext $renderingContext, IField $field, ServerRequestInterface $request, string $actionName = null, array $data);
}
