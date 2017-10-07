<?php declare(strict_types=1);

namespace Dms\Web\Expressive\Tests\Integration\Scaffold\Fixture\ToManyRelation\Persistence\Services;

use Dms\Core\Model\ICriteria;
use Dms\Core\Model\ISpecification;
use Dms\Core\Persistence\IRepository;
use Dms\Web\Expressive\Tests\Integration\Scaffold\Fixture\ToManyRelation\Domain\TestEntity;

/**
 * The repository for the Dms\Web\Expressive\Tests\Integration\Scaffold\Fixture\ToManyRelation\Domain\TestEntity entity.
 */
interface ITestEntityRepository extends IRepository
{
    /**
     * {@inheritDoc}
     *
     * @return TestEntity[]
     */
    public function getAll() : array;

    /**
     * {@inheritDoc}
     *
     * @return TestEntity
     */
    public function get($id);

    /**
     * {@inheritDoc}
     *
     * @return TestEntity[]
     */
    public function getAllById(array $ids) : array;

    /**
     * {@inheritDoc}
     *
     * @return TestEntity|null
     */
    public function tryGet($id);

    /**
     * {@inheritDoc}
     *
     * @return TestEntity[]
     */
    public function tryGetAll(array $ids) : array;

    /**
     * {@inheritDoc}
     *
     * @return TestEntity[]
     */
    public function matching(ICriteria $criteria) : array;

    /**
     * {@inheritDoc}
     *
     * @return TestEntity[]
     */
    public function satisfying(ISpecification $specification) : array;
}
