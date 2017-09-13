<?php declare(strict_types=1);

namespace Dms\Web\Laravel\Tests\Integration\Scaffold\Fixture\ValueObjectRelation\Cms\Modules;

use Dms\Core\Auth\IAuthSystem;
use Dms\Core\Common\Crud\CrudModule;
use Dms\Core\Common\Crud\Definition\CrudModuleDefinition;
use Dms\Core\Common\Crud\Definition\Form\CrudFormDefinition;
use Dms\Core\Common\Crud\Definition\Table\SummaryTableDefinition;
use Dms\Web\Laravel\Tests\Integration\Scaffold\Fixture\ValueObjectRelation\Persistence\Services\ITestEntityRepository;
use Dms\Web\Laravel\Tests\Integration\Scaffold\Fixture\ValueObjectRelation\Domain\TestEntity;
use Dms\Common\Structure\Field;
use Dms\Web\Laravel\Tests\Integration\Scaffold\Fixture\ValueObjectRelation\Cms\Modules\Fields\TestValueObjectField;
use Dms\Web\Laravel\Tests\Integration\Scaffold\Fixture\ValueObjectRelation\Domain\TestValueObject;

/**
 * The test-entity module.
 */
class TestEntityModule extends CrudModule
{


    public function __construct(ITestEntityRepository $dataSource, IAuthSystem $authSystem)
    {

        parent::__construct($dataSource, $authSystem);
    }

    /**
     * Defines the structure of this module.
     *
     * @param CrudModuleDefinition $module
     */
    protected function defineCrudModule(CrudModuleDefinition $module)
    {
        $module->name('test-entity');

        $module->labelObjects()->fromProperty(/* FIXME: */ TestEntity::ID);

        $module->metadata([
            'icon' => ''
        ]);

        $module->crudForm(function (CrudFormDefinition $form) {
            $form->section('Details', [
                $form->field(
                    (new TestValueObjectField('value_object', 'Value Object'))->required()
                )->bindToProperty(TestEntity::VALUE_OBJECT),
                //
                $form->field(
                    new TestValueObjectField('nullable_value_object', 'Nullable Value Object')
                )->bindToProperty(TestEntity::NULLABLE_VALUE_OBJECT),
                //
                $form->field(
                    Field::create('value_object_collection', 'Value Object Collection')->arrayOfField(
                        new TestValueObjectField('value_object_collection', 'Value Object Collection')
                    )->mapToCollection(TestValueObject::collectionType())
                )->bindToProperty(TestEntity::VALUE_OBJECT_COLLECTION),
                //
            ]);

        });

        $module->removeAction()->deleteFromDataSource();

        $module->summaryTable(function (SummaryTableDefinition $table) {
            $table->mapProperty(TestEntity::VALUE_OBJECT)->to((new TestValueObjectField('value_object', 'Value Object'))->required());
            $table->mapProperty(TestEntity::NULLABLE_VALUE_OBJECT)->to(new TestValueObjectField('nullable_value_object', 'Nullable Value Object'));
            $table->mapProperty(TestEntity::VALUE_OBJECT_COLLECTION)->to(Field::create('value_object_collection', 'Value Object Collection')->arrayOfField(
                new TestValueObjectField('value_object_collection', 'Value Object Collection')
            )->mapToCollection(TestValueObject::collectionType()));


            $table->view('all', 'All')
                ->loadAll()
                ->asDefault();
        });
    }
}