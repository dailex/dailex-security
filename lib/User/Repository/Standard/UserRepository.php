<?php

namespace Dlx\Security\User\Repository\Standard;

use Daikon\ReadModel\Projection\ProjectionInterface;
use Daikon\ReadModel\Projection\ProjectionMap;
use Daikon\ReadModel\Query\QueryInterface;
use Daikon\ReadModel\Repository\RepositoryInterface;
use Daikon\ReadModel\Storage\StorageAdapterInterface;

final class UserRepository implements RepositoryInterface
{
    private $storageAdapter;

    public function __construct(StorageAdapterInterface $storageAdapter)
    {
        $this->storageAdapter = $storageAdapter;
    }

    public function findById(string $identifier): ProjectionInterface
    {
        return $this->storageAdapter->read($identifier);
    }

    public function findByIds(array $identifiers): ProjectionMap
    {
    }

    public function search(QueryInterface $query, int $from = null, int $size = null): ProjectionMap
    {
        return $this->storageAdapter->search($query, $from ?? 0, $size ?? 10);
    }

    public function persist(ProjectionInterface $projection): bool
    {
        return $this->storageAdapter->write($projection->getAggregateId(), $projection->toArray());
    }

    public function makeProjection(): ProjectionInterface
    {
        return User::fromArray([
            '@type' => User::class,
            '@parent' => null
        ]);
    }
}
