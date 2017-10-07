<?php declare(strict_types=1);

namespace Dms\Web\Expressive\Tests\Integration\Scaffold\Fixture\Mixed\Cms\Modules;

use Dms\Core\Auth\IAuthSystem;
use Dms\Core\Common\Crud\CrudModule;
use Dms\Core\Common\Crud\Definition\CrudModuleDefinition;
use Dms\Core\Common\Crud\Definition\Form\CrudFormDefinition;
use Dms\Core\Common\Crud\Definition\Table\SummaryTableDefinition;
use Dms\Web\Expressive\Tests\Integration\Scaffold\Fixture\Mixed\Persistence\Services\ITestEntityRepository;
use Dms\Web\Expressive\Tests\Integration\Scaffold\Fixture\Mixed\Domain\TestEntity;
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

        $module->labelObjects()->fromProperty(/* FIXME: */ TestEntity::ID);

        $module->metadata([
            'icon' => ''
        ]);

        $module->crudForm(function (CrudFormDefinition $form) {
            $form->section('Details', [
                $form->field(
                    /* TODO: TestEntity::MIXED */
                )->bindToProperty(TestEntity::MIXED),
                //
            ]);
        });

        $module->removeAction()->deleteFromDataSource();

        $module->summaryTable(function (SummaryTableDefinition $table) {
            $table->mapProperty(TestEntity::MIXED)->to(/* TODO: TestEntity::MIXED */);


            $table->view('all', 'All')
                ->loadAll()
                ->asDefault();
        });
    }
}
