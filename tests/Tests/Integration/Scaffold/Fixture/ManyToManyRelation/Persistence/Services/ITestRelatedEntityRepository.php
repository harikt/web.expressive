<?php declare(strict_types=1);

namespace Dms\Web\Laravel\Tests\Integration\Scaffold\Fixture\ManyToManyRelation\Persistence\Services;

use Dms\Core\Model\ICriteria;
use Dms\Core\Model\ISpecification;
use Dms\Core\Persistence\IRepository;
use Dms\Web\Laravel\Tests\Integration\Scaffold\Fixture\ManyToManyRelation\Domain\TestRelatedEntity;

/**
 * The repository for the Dms\Web\Laravel\Tests\Integration\Scaffold\Fixture\ManyToManyRelation\Domain\TestRelatedEntity entity.
 */
interface ITestRelatedEntityRepository extends IRepository
{
    /**
     * {@inheritDoc}
     *
     * @return TestRelatedEntity[]
     */
    public function getAll() : array;

    /**
     * {@inheritDoc}
     *
     * @return TestRelatedEntity
     */
    public function get($id);

    /**
     * {@inheritDoc}
     *
     * @return TestRelatedEntity[]
     */
    public function getAllById(array $ids) : array;

    /**
     * {@inheritDoc}
     *
     * @return TestRelatedEntity|null
     */
    public function tryGet($id);

    /**
     * {@inheritDoc}
     *
     * @return TestRelatedEntity[]
     */
    public function tryGetAll(array $ids) : array;

    /**
     * {@inheritDoc}
     *
     * @return TestRelatedEntity[]
     */
    public function matching(ICriteria $criteria) : array;

    /**
     * {@inheritDoc}
     *
     * @return TestRelatedEntity[]
     */
    public function satisfying(ISpecification $specification) : array;
}
