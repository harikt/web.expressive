<?php declare(strict_types=1);

namespace Dms\Web\Expressive\Renderer\Form\Field;

use Dms\Core\Exception\InvalidArgumentException;
use Dms\Core\Form\IField;
use Dms\Core\Form\IFieldType;
use Dms\Core\Module\IModule;
use Dms\Web\Expressive\Http\ModuleContext;
use Dms\Web\Expressive\Renderer\Form\FormRenderingContext;
use Dms\Web\Expressive\Renderer\Form\IFieldRendererWithActions;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Diactoros\Response;

/**
 * The blade field renderer with actions
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
abstract class BladeFieldRendererWithActions extends BladeFieldRenderer implements IFieldRendererWithActions
{
    /**
     * @param FormRenderingContext $renderingContext
     * @param IField               $field
     * @param Request              $request
     * @param string               $actionName
     * @param array                $data
     *
     * @return Response
     * @throws InvalidArgumentException
     */
    final public function handleAction(FormRenderingContext $renderingContext, IField $field, ServerRequestInterface $request, string $actionName = null, array $data)
    {
        if (!$this->accepts($renderingContext, $field)) {
            throw InvalidArgumentException::format(
                'Field \'%s\' cannot be rendered in renderer of type %s',
                $field->getName(),
                get_class($this)
            );
        }

        return $this->handleFieldAction($renderingContext, $field, $field->getType(), $request, $actionName, $data);
    }

    /**
     * @param FormRenderingContext $renderingContext
     * @param IField               $field
     * @param IFieldType           $fieldType
     * @param Request              $request
     * @param string               $actionName
     * @param array                $data
     *
     * @return Response
     */
    abstract protected function handleFieldAction(FormRenderingContext $renderingContext, IField $field, IFieldType $fieldType, ServerRequestInterface $request, string $actionName = null, array $data);
}
