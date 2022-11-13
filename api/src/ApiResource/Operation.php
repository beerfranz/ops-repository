<?php

namespace App\ApiResource;

use App\Repository\OperationRepository;
use App\State\OperationStateProcessor;
use App\State\OperationStateProvider;
use App\Entity\Operation as OperationEntity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiFilter;

use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

use Symfony\Component\Serializer\Annotation\Context;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;

#[ApiResource(
    description: 'Operation',
    provider: OperationStateProvider::class,
    processor: OperationStateProcessor::class,
    normalizationContext: ['groups' => ['operation:read']],
    denormalizationContext: ['groups' => ['operation:write']],
)]
class Operation
{
    #[ApiProperty(identifier: true)]
    #[Groups(['operation:read'])]
    private $id;

    #[Assert\Length(max: 255)]
    #[Groups(['operation:read', 'operation:write',])]
    private $name;

    #[Assert\Length(max: 255)]
    #[Groups(['operation:read', 'operation:write',])]
    private $description;

    #[Assert\DateTimeInterface]
    #[Groups(['operation:read', 'operation:write'])]
//    #[Context([DateTimeNormalizer::FORMAT_KEY => \DateTime::RFC3339_EXTENDED])]
    private \DateTimeInterface $startedAt;

    #[Assert\DateTimeInterface]
    #[Groups(['operation:read', 'operation:write'])]
//    #[Context([DateTimeNormalizer::FORMAT_KEY => \DateTime::RFC3339_EXTENDED])]
    private ?\DateTimeInterface $endedAt = null;

    #[Assert\DateTimeInterface]
    #[Groups(['operation:read'])]
//    #[Context([DateTimeNormalizer::FORMAT_KEY => \DateTime::RFC3339_EXTENDED])]
    private ?\DateTimeInterface $createdAt = null;

    #[Groups(['operation:read', 'operation:write'])]
    private ?array $tags = null;

    public function __construct()
    {
        $this->setCreatedAt();
        $this->setStartedAt();
        $this->setEndedAt($this->getStartedAt());

        $this->tags = [];
    }

    public function getId()
    {
        return $this->id;
    }

    public function setId(string $id): Operation
    {
        $this->id = $id;
        return $this;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setName(string $name): Operation
    {
        $this->name = $name;
        return $this;
    }

    public function getDescription()
    {
        return $this->description;
    }

    public function setDescription(string $description): Operation
    {
        $this->description = $description;
        return $this;
    }

    public function getCreatedAt(): \DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt = new \DateTime()): Operation
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    public function getStartedAt(): \DateTimeInterface
    {
        return $this->startedAt;
    }

    public function setStartedAt(\DateTimeInterface $startedAt = new \DateTime()): Operation
    {
        $this->startedAt = $startedAt;
        return $this;
    }

    public function getEndedAt(): \DateTimeInterface
    {
        return $this->endedAt;
    }

    public function setEndedAt(\DateTimeInterface $endedAt = new \DateTime()): Operation
    {
        $this->endedAt = $endedAt;
        return $this;
    }

    public function getTags(): ?array
    {
        return $this->tags;
    }

    public function setTags(?array $tags = []): self
    {
        $this->tags = $tags;
        return $this;
    }

    #[Groups(['operation:read'])]
    public function getTagsString(): ?string
    {
        return implode(',', $this->tags);
    }

    public function hydrateByEntity(OperationEntity $entity): self
    {
        $this->setId($entity->getId());
        $this->setName($entity->getName());
        $this->setDescription($entity->getDescription());
        $this->setCreatedAt($entity->getCreatedAt());
        $this->setStartedAt($entity->getStartedAt());
        $this->setEndedAt($entity->getEndedAt());

        $tags = [];
        foreach($entity->getTags() as $tag)
        {
            $tags[] = $tag->getName();
        }
        $this->setTags($tags);

        return $this;
    }

}
