<?php

namespace App\State;

use App\ApiResource\Operation as OperationApi;
use App\Entity\Operation as OperationEntity;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use ApiPlatform\Metadata\CollectionOperationInterface;

use Doctrine\ORM\EntityManagerInterface;

final class OperationStateProvider implements ProviderInterface
{
    public function __construct(
        EntityManagerInterface $entityManager,
    ) {
        $this->_entityManager = $entityManager;
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        $repo = $this->_entityManager->getRepository(OperationEntity::class);


        if ($operation instanceof CollectionOperationInterface) {

            $maxItemPerPage = 30;
            $offset = 0;

            if (isset($context['filters']['page']))
            {
                $page = $context['filters']['page'];
                
                $offset = ($page - 1) * $maxItemPerPage;
            }
            
            $entities = $repo->findBy([], [], $maxItemPerPage, $offset);

            $items = [];

            foreach($entities as $entity)
            {
                $item = new OperationApi();
                $item->hydrateByEntity($entity);
                $items[] = $item;
            }
            return $items;
        }

        $entity = $repo->find($uriVariables['id']);
        if ($entity !== null)
        {
            $item = new OperationApi();
            $item->hydrateByEntity($entity);
        }
        else
        {
            return null;
        }
        
        return $item;
    }

}