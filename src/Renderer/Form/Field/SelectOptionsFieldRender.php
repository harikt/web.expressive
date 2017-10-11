<?php declare(strict_types=1);

namespace Dms\Web\Expressive\Renderer\Form\Field;

use Dms\Core\Form\Field\Type\FieldType;
use Dms\Core\Form\IField;
use Dms\Core\Form\IFieldOptions;
use Dms\Core\Form\IFieldType;
use Dms\Web\Expressive\Renderer\Form\FormRenderingContext;
use Dms\Web\Expressive\Renderer\Form\IFieldRendererWithActions;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Diactoros\Response;
use Zend\Diactoros\Response\JsonResponse;

/**
 * The select-box options field renderer
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class SelectOptionsFieldRender extends OptionsFieldRender implements IFieldRendererWithActions
{
    /**
     * Gets the expected class of the field type for the field.
     *
     * @return array
     */
    public function getFieldTypeClasses() : array
    {
        return [FieldType::class];
    }

    protected function canRender(FormRenderingContext $renderingContext, IField $field, IFieldType $fieldType) : bool
    {
        return $fieldType->has(FieldType::ATTR_OPTIONS)
        && !$fieldType->get(FieldType::ATTR_SHOW_ALL_OPTIONS);
    }

    protected function renderField(
        FormRenderingContext $renderingContext,
        IField $field,
        IFieldType $fieldType
    ) : string {
        /** @var IFieldOptions $options */
        $options = $fieldType->get(FieldType::ATTR_OPTIONS);

        if ($options->canFilterOptions()) {
            $remoteDataUrl = $renderingContext->getFieldActionUrl($field) . '/load-options';

            try {
                $initialValue = $field->getUnprocessedInitialValue();
                $option       = $initialValue === null ? null : $options->getOptionForValue($initialValue);
            } catch (\Exception $e) {
                $option = null;
            }

            return $this->renderView(
                $field,
                'dms::components.field.select.remote-data-input',
                [
                    FieldType::ATTR_OPTIONS => 'options',
                ],
                [
                    'remoteDataUrl'  => $remoteDataUrl,
                    'remoteMinChars' => min(3, max(1, (int)log10($options->count()))),
                    'option'         => $option,
                ]
            );
        } else {
            return $this->renderView(
                $field,
                'dms::components.field.select.input',
                [
                    FieldType::ATTR_OPTIONS => 'options',
                ]
            );
        }
    }

    /**
     * @param FormRenderingContext $renderingContext
     * @param IField               $field
     * @param Request              $request
     * @param string               $actionName
     * @param array                $data
     *
     * @return Response
     */
    public function handleAction(FormRenderingContext $renderingContext, IField $field, ServerRequestInterface $request, string $actionName = null, array $data)
    {
        if (ends_with($request->url(), '/load-options') && $request->has('query')) {
            /** @var IFieldOptions $options */
            $options = $field->getType()->get(FieldType::ATTR_OPTIONS);

            $data = [];

            foreach ($options->getFilteredOptions((string)$request->input('query')) as $option) {
                if (!$option->isDisabled()) {
                    $data[] = ['val' => $option->getValue(), 'label' => $option->getLabel()];
                }
            }

            return new JsonResponse($data);
        }

        $response = new Response('php://memory', 404);
        // $response->getBody()->write($message);

        return $response;
    }
}
