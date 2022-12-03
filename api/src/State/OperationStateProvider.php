<?php

namespace App\State;

use App\ApiResource\Operation as OperationApi;
use App\Entity\Operation as OperationEntity;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use ApiPlatform\Metadata\CollectionOperationInterface;

use Doctrine\ORM\EntityManagerInterface;

use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\DateTime;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Validator\Validation;

final class OperationStateProvider implements ProviderInterface
{
    private $validator;

    public function __construct(
        EntityManagerInterface $entityManager,
        ValidatorInterface $validator
    ) {
        $this->_entityManager = $entityManager;
        $this->validator = $validator;
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        $repo = $this->_entityManager->getRepository(OperationEntity::class);

        if ($operation instanceof CollectionOperationInterface) {

            $maxItemPerPage = 30;
            $offset = 0;

            $criteria = [];

            if (isset($context['filters']['page']))
            {
                $page = $context['filters']['page'];
                
                $offset = ($page - 1) * $maxItemPerPage;
            }
            
            if (isset($context['filters']['tags']))
            {
                $tags = explode(',' , $context['filters']['tags']);
                
                $criteria['tags.name'] = $tags;
            }

            if (isset($context['filters']['from']))
            {
                $from = $context['filters']['from'];
                try
                {
                    $from_date = new \DateTime($from);
                } catch (\Exeption $e) {
                    throw new \LogicException('from parameter is not a valid datetime. See php function new \DateTime(input)');
                }
                
                $criteria['from'] = $from_date;
            }

            if (isset($context['filters']['to']))
            {
                $to = $context['filters']['to'];
                try
                {
                    $to_date = new \DateTime($to);
                } catch (\Exeption $e) {
                    throw new \LogicException('to parameter is not a valid datetime. See php function new \DateTime(input)');
                }
                
                $criteria['to'] = $to_date;
            }

            $entities = $repo->findBy($criteria, [], $maxItemPerPage, $offset);

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