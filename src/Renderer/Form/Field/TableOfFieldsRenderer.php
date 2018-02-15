<?php declare(strict_types=1);

namespace Dms\Web\Expressive\Renderer\Form\Field;

use Dms\Common\Structure\Table\Form\Processor\TableDataProcessor;
use Dms\Common\Structure\Table\Form\TableType;
use Dms\Common\Structure\Table\TableData;
use Dms\Core\Form\Field\Builder\Field;
use Dms\Core\Form\Field\Type\ArrayOfType;
use Dms\Core\Form\Field\Type\FieldType;
use Dms\Core\Form\IField;
use Dms\Core\Form\IFieldType;
use Dms\Web\Expressive\Renderer\Form\FormRenderingContext;

/**
 * The table of fields renderer
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class TableOfFieldsRenderer extends BladeFieldRenderer
{
    /**
     * Gets the expected class of the field type for the field.
     *
     * @return array
     */
    public function getFieldTypeClasses() : array
    {
        return [TableType::class];
    }

    protected function canRender(FormRenderingContext $renderingContext, IField $field, IFieldType $fieldType) : bool
    {
        /**
 * @var ArrayOfType $fieldType
*/
        return !$fieldType->has(FieldType::ATTR_OPTIONS);
    }

    protected function renderField(FormRenderingContext $renderingContext, IField $field, IFieldType $fieldType) : string
    {
        /**
 * @var TableType $fieldType
*/
        $columnField = $fieldType->getColumnField();
        $rowField    = $fieldType->getRowField();
        $cellField   = $fieldType->getCellField();

        $renderers = $this->fieldRendererCollection;

        return $this->renderView(
            $field,
            'dms::components.field.table-of-fields.input',
            [
                TableType::ATTR_MIN_COLUMNS        => 'minColumns',
                TableType::ATTR_MAX_COLUMNS        => 'maxColumns',
                TableType::ATTR_EXACT_COLUMNS      => 'exactColumns',
                TableType::ATTR_MIN_ROWS           => 'minRows',
                TableType::ATTR_MAX_ROWS           => 'maxRows',
                TableType::ATTR_EXACT_ROWS         => 'exactRows',
                TableType::ATTR_PREDEFINED_COLUMNS => 'predefinedColumns',
                TableType::ATTR_PREDEFINED_ROWS    => 'predefinedRows',
            ],
            [
                'renderingContext'    => $renderingContext,
                'columnField'         => $columnField,
                'rowField'            => $rowField,
                'cellField'           => $cellField,
                'columnFieldRenderer' => $renderers->findRendererFor($renderingContext, $columnField),
                'rowFieldRenderer'    => $rowField ? $renderers->findRendererFor($renderingContext, $rowField) : null,
                'cellFieldRenderer'   => $renderers->findRendererFor($renderingContext, $cellField),
                'value'               => $this->makeDefaultValueIfHasPredefinedRowsAndColumns($fieldType, $field->getInitialValue()),
            ]
        );
    }

    private function makeDefaultValueIfHasPredefinedRowsAndColumns(TableType $fieldType, TableData $processedValue = null)
    {
        $value = null;

        if ($processedValue) {
            $value = (new TableDataProcessor($fieldType->getTableDataCellClass()))->unprocess($processedValue);
        }

        $hasColumns = $fieldType->has(TableType::ATTR_PREDEFINED_COLUMNS);
        $hasRows    = $fieldType->has(TableType::ATTR_PREDEFINED_ROWS);

        if ($hasColumns && $hasRows && empty($value[TableType::CELLS_FIELD])) {
            $value = [];

            $value[TableType::COLUMNS_FIELD] = $fieldType->get(TableType::ATTR_PREDEFINED_COLUMNS);
            $value[TableType::ROWS_FIELD]    = $fieldType->get(TableType::ATTR_PREDEFINED_ROWS);

            foreach ($value[TableType::ROWS_FIELD] as $rowKey => $rowValue) {
                foreach ($value[TableType::COLUMNS_FIELD] as $columnKey => $columnValue) {
                    $value[TableType::CELLS_FIELD][$rowKey][$columnKey] = null;
                }
            }
        } elseif (!$hasColumns && $hasRows && empty($value[TableType::CELLS_FIELD])) {
            $value = [];

            $value[TableType::COLUMNS_FIELD] = [0 => null];
            $value[TableType::ROWS_FIELD]    = $fieldType->get(TableType::ATTR_PREDEFINED_ROWS);

            foreach ($value[TableType::ROWS_FIELD] as $rowKey => $rowValue) {
                $value[TableType::CELLS_FIELD][$rowKey][0] = null;
            }
        } elseif (!$hasColumns && !$hasRows && empty($value[TableType::CELLS_FIELD])) {
            $value = [];

            $value[TableType::COLUMNS_FIELD]     = [0 => null];
            $value[TableType::ROWS_FIELD]        = [0 => null];
            $value[TableType::CELLS_FIELD][0][0] = null;
        }

        return $value;
    }

    public function renderFieldValue(FormRenderingContext $renderingContext, IField $field, $value, IFieldType $fieldType) : string
    {
        /**
 * @var TableType $fieldType
*/
        $columnField = $fieldType->getColumnField();
        $rowField    = $fieldType->getRowField();
        $cellField   = $fieldType->getCellField();

        $renderers = $this->fieldRendererCollection;

        return $this->renderValueViewWithNullDefault(
            $field,
            $value,
            'dms::components.field.table-of-fields.value',
            [
                'renderingContext'    => $renderingContext,
                'columnField'         => $columnField,
                'rowField'            => $rowField,
                'cellField'           => $cellField,
                'columnFieldRenderer' => $renderers->findRendererFor($renderingContext, $columnField),
                'rowFieldRenderer'    => $rowField ? $renderers->findRendererFor($renderingContext, $rowField) : null,
                'cellFieldRenderer'   => $renderers->findRendererFor($renderingContext, $cellField),
            ]
        );
    }

    protected function makeElementField(ArrayOfType $fieldType) : IField
    {
        return $fieldType->getElementField();
    }
}
