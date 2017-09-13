<?php declare(strict_types=1);

namespace Dms\Web\Laravel\Tests\Integration\Scaffold\Fixture\ToManyRelation\Cms\Modules;

use Dms\Core\Auth\IAuthSystem;
use Dms\Core\Common\Crud\CrudModule;
use Dms\Core\Common\Crud\Definition\CrudModuleDefinition;
use Dms\Core\Common\Crud\Definition\Form\CrudFormDefinition;
use Dms\Core\Common\Crud\Definition\Table\SummaryTableDefinition;
use Dms\Web\Laravel\Tests\Integration\Scaffold\Fixture\ToManyRelation\Persistence\Services\ITestEntityRepository;
use Dms\Web\Laravel\Tests\Integration\Scaffold\Fixture\ToManyRelation\Domain\TestEntity;
use Dms\Common\Structure\Field;
use Dms\Web\Laravel\Tests\Integration\Scaffold\Fixture\ToManyRelation\Persistence\Services\ITestRelatedEntityRepository;
use Dms\Web\Laravel\Tests\Integration\Scaffold\Fixture\ToManyRelation\Domain\TestRelatedEntity;

/**
 * The test-entity module.
 */
class TestEntityModule extends CrudModule
{
    /**
     * @var ITestRelatedEntityRepository
     */
    protected $testRelatedEntityRepository;


    public function __construct(ITestEntityRepository $dataSource, IAuthSystem $authSystem, ITestRelatedEntityRepository $testRelatedEntityRepository)
    {
        $this->testRelatedEntityRepository = $testRelatedEntityRepository;
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
                    Field::create('related', 'Related')
                        ->entitiesFrom($this->testRelatedEntityRepository)
                        ->labelledBy(/* FIXME: */ TestRelatedEntity::ID)
                        ->mapToCollection(TestRelatedEntity::collectionType())
                )->bindToProperty(TestEntity::RELATED),
                //
            ]);

        });

        $module->removeAction()->deleteFromDataSource();

        $module->summaryTable(function (SummaryTableDefinition $table) {
            $table->mapProperty(TestEntity::RELATED)->to(Field::create('related', 'Related')
                ->entitiesFrom($this->testRelatedEntityRepository)
                ->labelledBy(/* FIXME: */ TestRelatedEntity::ID)
                ->mapToCollection(TestRelatedEntity::collectionType()));


            $table->view('all', 'All')
                ->loadAll()
                ->asDefault();
        });
    }
}