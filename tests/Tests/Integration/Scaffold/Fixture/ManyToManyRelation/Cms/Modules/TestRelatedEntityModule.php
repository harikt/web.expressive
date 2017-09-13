<?php declare(strict_types=1);

namespace Dms\Web\Laravel\Tests\Integration\Scaffold\Fixture\ManyToManyRelation\Cms\Modules;

use Dms\Core\Auth\IAuthSystem;
use Dms\Core\Common\Crud\CrudModule;
use Dms\Core\Common\Crud\Definition\CrudModuleDefinition;
use Dms\Core\Common\Crud\Definition\Form\CrudFormDefinition;
use Dms\Core\Common\Crud\Definition\Table\SummaryTableDefinition;
use Dms\Web\Laravel\Tests\Integration\Scaffold\Fixture\ManyToManyRelation\Persistence\Services\ITestRelatedEntityRepository;
use Dms\Web\Laravel\Tests\Integration\Scaffold\Fixture\ManyToManyRelation\Domain\TestRelatedEntity;
use Dms\Common\Structure\Field;
use Dms\Web\Laravel\Tests\Integration\Scaffold\Fixture\ManyToManyRelation\Persistence\Services\ITestEntityRepository;
use Dms\Web\Laravel\Tests\Integration\Scaffold\Fixture\ManyToManyRelation\Domain\TestEntity;

/**
 * The test-related-entity module.
 */
class TestRelatedEntityModule extends CrudModule
{
    /**
     * @var ITestEntityRepository
     */
    protected $testEntityRepository;


    public function __construct(ITestRelatedEntityRepository $dataSource, IAuthSystem $authSystem, ITestEntityRepository $testEntityRepository)
    {
        $this->testEntityRepository = $testEntityRepository;
        parent::__construct($dataSource, $authSystem);
    }

    /**
     * Defines the structure of this module.
     *
     * @param CrudModuleDefinition $module
     */
    protected function defineCrudModule(CrudModuleDefinition $module)
    {
        $module->name('test-related-entity');

        $module->labelObjects()->fromProperty(/* FIXME: */ TestRelatedEntity::ID);

        $module->metadata([
            'icon' => ''
        ]);

        $module->crudForm(function (CrudFormDefinition $form) {
            $form->section('Details', [
                $form->field(
                    Field::create('parent', 'Parent')
                        ->entitiesFrom($this->testEntityRepository)
                        ->labelledBy(/* FIXME: */ TestEntity::ID)
                        ->mapToCollection(TestEntity::collectionType())
                )->bindToProperty(TestRelatedEntity::PARENT),
                //
            ]);

        });

        $module->removeAction()->deleteFromDataSource();

        $module->summaryTable(function (SummaryTableDefinition $table) {
            $table->mapProperty(TestRelatedEntity::PARENT)->to(Field::create('parent', 'Parent')
                ->entitiesFrom($this->testEntityRepository)
                ->labelledBy(/* FIXME: */ TestEntity::ID)
                ->mapToCollection(TestEntity::collectionType()));


            $table->view('all', 'All')
                ->loadAll()
                ->asDefault();
        });
    }
}