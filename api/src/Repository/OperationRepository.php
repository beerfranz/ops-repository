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

    public function findBy(array $criteria, ?array $orderBy = null, $limit = null, $offset = null)
    {
        $query = $this->createQueryBuilder('operation')
                      ->join('operation.tags', 'tags')
                      ->setMaxResults($limit)
                      ->setFirstResult($offset);

        foreach($criteria as $key => $value)
        {
            if (is_array($value))
            {
                if (isset($value['type']))
                {
                    if ($value['type'] === 'BETWEEN')
                    {
                        $safe_key_from = 'from_' . str_replace('.', '_', $key);
                        $safe_key_to   = 'to_' . str_replace('.', '_', $key);
                        $query = $query->andWhere($key . ' BETWEEN :' . $safe_key_from . ' AND :' . $safe_key_to);
                        $query = $query->setParameter($safe_key_from, $value['from']);
                        $query = $query->setParameter($safe_key_to, $value['to']);
                    }
                } else {
                    $safe_key = str_replace('.', '_', $key);
                    $query->andWhere($key . ' IN (:' . $safe_key . ')');
                    $query = $query->setParameter($safe_key, $value);
                }
            } else {
                $safe_key = str_replace('.', '_', $key);
                $query = $query->andWhere($key . ' = :' . $safe_key);
                $query = $query->setParameter($safe_key, $value);
            }
        }

        if (null !== $orderBy)
        {
            foreach ($orderBy as $field => $sens)
            {
                $query = $query->orderBy($field, $sens);
            }
        }

        return $query->getQuery()->getResult();
    }
}
