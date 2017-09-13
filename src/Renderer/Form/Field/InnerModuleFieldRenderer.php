<?php declare(strict_types=1);

namespace Dms\Web\Expressive\Renderer\Form\Field;

use Dms\Core\Common\Crud\ICrudModule;
use Dms\Core\Common\Crud\IReadModule;
use Dms\Core\Form\Field\Type\FieldType;
use Dms\Core\Form\Field\Type\InnerCrudModuleType;
use Dms\Core\Form\IField;
use Dms\Core\Form\IFieldType;
use Dms\Web\Expressive\Http\ModuleRequestRouter;
use Dms\Web\Expressive\Renderer\Action\ActionButton;
use Dms\Web\Expressive\Renderer\Form\FormRenderingContext;
use Dms\Web\Expressive\Renderer\Form\IFieldRendererWithActions;
use Dms\Web\Expressive\Renderer\Module\ReadModuleRenderer;
use Dms\Web\Expressive\Renderer\Table\TableRenderer;
use Dms\Web\Expressive\Util\StringHumanizer;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Diactoros\Response\JsonResponse;

/**
 * The inner-module field renderer
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class InnerModuleFieldRenderer extends BladeFieldRendererWithActions implements IFieldRendererWithActions
{
    /**
     * Gets the expected class of the field type for the field.
     *
     * @return array
     */
    public function getFieldTypeClasses() : array
    {
        return [InnerCrudModuleType::class];
    }

    protected function canRender(FormRenderingContext $renderingContext, IField $field, IFieldType $fieldType) : bool
    {
        return !$fieldType->has(FieldType::ATTR_OPTIONS);
    }

    protected function renderField(FormRenderingContext $renderingContext, IField $field, IFieldType $fieldType) : string
    {
        $innerModuleContext = $this->loadInnerModuleContext($field, $renderingContext);

        /** @var ReadModuleRenderer $renderer */
        $renderer = app(ReadModuleRenderer::class);
        return $this->renderView(
            $field,
            'dms::components.field.inner-module.input',
            [],
            [
                'rootUrl'       => $innerModuleContext->getRootUrl(),
                'moduleContent' => $renderer->render($innerModuleContext),
            ]
        );
    }

    protected function renderFieldValue(FormRenderingContext $renderingContext, IField $field, $value, IFieldType $fieldType) : string
    {
        /** @var InnerCrudModuleType $fieldType */
        /** @var IReadModule $innerModule */
        $innerModule        = $fieldType->getModule();
        $innerModuleContext = $this->loadInnerModuleContext($field, $renderingContext);

        /** @var TableRenderer $tableRenderer */
        $tableRenderer = app(TableRenderer::class);

        $actionButtons = [];
        if ($innerModule->allowsDetails()) {
            $detailsAction = $innerModule->getDetailsAction();
            $detailsUrl    = $innerModuleContext->getUrl('action.show', ['action' => $detailsAction->getName(), 'object_id' => '__object__']);

            $actionButtons[] = new ActionButton(
                false,
                $detailsAction->getName(),
                StringHumanizer::title($detailsAction->getName()),
                function (string $objectId) use ($detailsUrl) {
                    return str_replace('__object__', $objectId, $detailsUrl);
                }
            );
        }

        $tableData = $tableRenderer->renderTableData(
            $innerModuleContext,
            $innerModule->getSummaryTable(),
            $innerModule->getSummaryTable()->getDataSource()->load(),
            null,
            false,
            $actionButtons
        );

        return $this->renderView(
            $field,
            'dms::components.field.inner-module.value',
            [],
            [
                'rootUrl'       => $innerModuleContext->getRootUrl(),
                'moduleContent' => $tableData,
            ]
        );
    }

    protected function handleFieldAction(FormRenderingContext $renderingContext, IField $field, IFieldType $fieldType, ServerRequestInterface $request, string $actionName = null, array $data)
    {
        /** @var InnerCrudModuleType $fieldType */
        if ($actionName === 'state') {
            return new JsonResponse([
                'state' => $field->unprocess($fieldType->getModule()->getDataSource()),
            ]);
        }

        $currentState      = json_decode(($data['current_state'] ?? false) ?: '[]', true);
        $requestUrl        = $data['request']['url'];
        $requestMethod     = $data['request']['method'];
        $requestParameters = $data['request']['parameters'] ?? [];

        $moduleContext = $this->loadInnerModuleContext($field, $renderingContext, $currentState);

		// @todo
        /** @var ModuleRequestRouter $moduleRequestRouter */
        $moduleRequestRouter = app(ModuleRequestRouter::class);

        /** @var Request $innerModuleRequest */
        $innerModuleRequest = Request::create($request->root() . $requestUrl, $requestMethod, $requestParameters);

        $this->emulateAjaxRequest($innerModuleRequest);

        $innerModuleResponse = $moduleRequestRouter->dispatch($moduleContext, $innerModuleRequest);

        return new JsonResponse([
            'new_state' => $field->unprocess($moduleContext->getModule()->getDataSource()),
            'response'  => $innerModuleResponse->getContent(),
        ], $innerModuleResponse->getStatusCode());
    }

    protected function emulateAjaxRequest(Request $innerModuleRequest)
    {
        $innerModuleRequest->headers->set('X-Requested-With', 'XMLHttpRequest');
    }

    protected function loadInnerModuleContext(IField $field, FormRenderingContext $renderingContext, array $moduleState = null)
    {
        /** @var InnerCrudModuleType $fieldType */
        $fieldType = $field->getType();

        /** @var IReadModule $module */
        $innerModule = $fieldType->getModule();

        if ($moduleState) {
            $innerModule = $innerModule->withDataSource($field->process($moduleState));
        }

        $moduleContext = $renderingContext->getModuleContext();

        if ($renderingContext->getObjectId() !== null) {
            /** @var ICrudModule|IReadModule $currentModule */
            $currentModule = $moduleContext->getModule();

            $moduleContext = $moduleContext->withBreadcrumb(
                $currentModule->getLabelFor($renderingContext->getObject()),
                $moduleContext->getUrl('action.form', [
                    'package' => $currentModule->getPackageName(),
                    'module' =>  $currentModule->getName(),
                    'action' => $renderingContext->getAction()->getName(),
                    'object_id' => $renderingContext->getObjectId(),
                ])
            );
        }

        $subModulePath = $renderingContext->getFieldActionUrl($field);

        return $moduleContext
            ->inSubModuleContext($innerModule, $subModulePath)
            ->withBreadcrumb(StringHumanizer::title($innerModule->getName()), $subModulePath);
    }
}
