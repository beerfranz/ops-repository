<?php

namespace App\Repository;

use App\Entity\Operation;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

class OperationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Operation::class);
    }

    public function findBy(array $criteria, ?array $orderBy = null, $limit = null, $offset = null): ?array
    {
        $query = $this->createQueryBuilder('operation')
                      ->join('operation.tags', 'tags')
                      ->setMaxResults($limit)
                      ->setFirstResult($offset);

        foreach($criteria as $key => $value)
        {
            if ($key === 'from')
            {
                $query = $query->andWhere('operation.startedAt >= :from OR operation.endedAt >= :from')
                               ->setParameter('from', $value);

            }
            elseif ($key === 'to')
            {
                $query = $query->andWhere('operation.startedAt <= :to OR operation.endedAt <= :to')
                               ->setParameter('to', $value);

            }
            elseif (is_array($value))
            {
                $safe_key = str_replace('.', '_', $key);
                $query->andWhere($key . ' IN (:' . $safe_key . ')');
                $query = $query->setParameter($safe_key, $value);
            } else {
                $safe_key = str_replace('.', '_', $key);
                $query = $query->andWhere($key . ' = :' . $safe_key);
                $query = $query->setParameter($safe_key, $value);
            }
        }

        if (null === $orderBy or count($orderBy) === 0)
        {
            $query = $query->orderBy('operation.startedAt', 'DESC');
        }
        else
        {
            foreach ($orderBy as $field => $sens)
            {
                $query = $query->orderBy($field, $sens);
            }
        }

        return $query->getQuery()->getResult();
    }
}
