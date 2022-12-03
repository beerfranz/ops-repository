<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use ApiPlatform\Metadata\DeleteOperationInterface;

use Psr\Log\LoggerInterface;

use App\ApiResource\Operation as OperationApi;
use App\Entity\Operation as OperationEntity;
use App\Entity\Tag;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RequestStack;

class OperationStateProcessor implements ProcessorInterface
{

    private $logger;

    public function __construct(
        EntityManagerInterface $entityManager,
        RequestStack $request,
        LoggerInterface $logger
    ) {
        $this->_entityManager = $entityManager;
        $this->_request = $request->getCurrentRequest();
        $this->logger = $logger;
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): ?object
    {

        $repo = $this->_entityManager->getRepository(OperationEntity::class);

        if ($operation instanceof DeleteOperationInterface) {
            $entity = $repo->find($uriVariables);
            $this->_entityManager->remove($entity);
            $this->_entityManager->flush();

            $this->logger->info('Delete operation');
            return null;
        }

        if ($data->getId() !== null)
        {
            $entity = $repo->find($uriVariables);
            $entity->removeTags();
            $this->logger->info('Put or patch operation');
        }
        else
        {
            $entity = new OperationEntity();
            $this->logger->info('Create operation');
        }

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
        }

        $this->_entityManager->persist($entity);
        $this->_entityManager->flush();

        $data->setId($entity->getId());
        return $data;
    }

}
