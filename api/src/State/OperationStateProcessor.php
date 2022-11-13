<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use ApiPlatform\Metadata\DeleteOperationInterface;

//use App\Entity\Operation2;
use App\ApiResource\Operation as OperationApi;
use App\Entity\Operation as OperationEntity;
use App\Entity\Tag;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RequestStack;

class OperationStateProcessor implements ProcessorInterface
{

    public function __construct(
        EntityManagerInterface $entityManager,
        RequestStack $request
    ) {
        $this->_entityManager = $entityManager;
        $this->_request = $request->getCurrentRequest();
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): ?object
    {

        if ($operation instanceof DeleteOperationInterface) {
            $repo = $this->_entityManager->getRepository(OperationEntity::class);
            $entity = $repo->find($uriVariables);
            $this->_entityManager->remove($entity);
            $this->_entityManager->flush();
            return null;
        }

        $entity = new OperationEntity();
        $entity->setName($data->getName());
        $entity->setDescription($data->getDescription());
        $entity->setCreatedAt($data->getCreatedAt());
        $entity->setStartedAt($data->getStartedAt());
        $entity->setEndedAt($data->getEndedAt());

        $tagRepository = $this->_entityManager->getRepository(Tag::class);

        foreach ($data->getTags() as $tagName)
        {
            $tag = $tagRepository->findOneBy(['name' => $tagName]);
            if ($tag === null)
            {
                $tag = new Tag();
                $tag->setName($tagName);   
            }

            $entity->addTag($tag);

            $tag->addOperation($entity);
            $this->_entityManager->persist($tag);
        }

        $this->_entityManager->persist($entity);
        $this->_entityManager->flush();

        $data->setId($entity->getId());

        return $data;
    }

}
