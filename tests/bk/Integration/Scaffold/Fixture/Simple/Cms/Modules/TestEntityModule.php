<?php declare(strict_types=1);

namespace Dms\Web\Expressive\Tests\Integration\Scaffold\Fixture\Simple\Cms\Modules;

use Dms\Core\Auth\IAuthSystem;
use Dms\Core\Common\Crud\CrudModule;
use Dms\Core\Common\Crud\Definition\CrudModuleDefinition;
use Dms\Core\Common\Crud\Definition\Form\CrudFormDefinition;
use Dms\Core\Common\Crud\Definition\Table\SummaryTableDefinition;
use Dms\Web\Expressive\Tests\Integration\Scaffold\Fixture\Simple\Persistence\Services\ITestEntityRepository;
use Dms\Web\Expressive\Tests\Integration\Scaffold\Fixture\Simple\Domain\TestEntity;
use Dms\Common\Structure\Field;

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

        $module->labelObjects()->fromProperty(TestEntity::STRING);

        $module->metadata([
            'icon' => ''
        ]);

        $module->crudForm(function (CrudFormDefinition $form) {
            $form->section('Details', [
                $form->field(
                    Field::create('string', 'String')->string()->required()
                )->bindToProperty(TestEntity::STRING),
                //
                $form->field(
                    Field::create('int', 'Int')->int()->required()
                )->bindToProperty(TestEntity::INT),
                //
                $form->field(
                    Field::create('float', 'Float')->decimal()->required()
                )->bindToProperty(TestEntity::FLOAT),
                //
                $form->field(
                    Field::create('bool', 'Bool')->bool()
                )->bindToProperty(TestEntity::BOOL),
                //
            ]);
        });

        $module->removeAction()->deleteFromDataSource();

        $module->summaryTable(function (SummaryTableDefinition $table) {
            $table->mapProperty(TestEntity::STRING)->to(Field::create('string', 'String')->string()->required());
            $table->mapProperty(TestEntity::INT)->to(Field::create('int', 'Int')->int()->required());
            $table->mapProperty(TestEntity::FLOAT)->to(Field::create('float', 'Float')->decimal()->required());
            $table->mapProperty(TestEntity::BOOL)->to(Field::create('bool', 'Bool')->bool());


            $table->view('all', 'All')
                ->loadAll()
                ->asDefault();
        });
    }
}
