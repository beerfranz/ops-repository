<?php

namespace App\Entity;

use App\Repository\OperationRepository;

use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;


#[ORM\Entity(repositoryClass: OperationRepository::class)]
class Operation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 255)]
    private $name;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $description = null;

    #[ORM\Column(type: 'datetime')]
    #[Assert\DateTimeInterface]
    private ?\DateTimeInterface $startedAt = null;

    #[ORM\Column(type: 'datetime')]
    #[Assert\DateTimeInterface]
    private ?\DateTimeInterface $endedAt = null;

    #[ORM\Column(type: 'datetime')]
    #[Assert\DateTimeInterface]
    private ?\DateTimeInterface $createdAt = null;

    #[ORM\ManyToMany(targetEntity: Tag::class, inversedBy: 'operations', cascade: ["persist"], orphanRemoval: true)]
    private Collection $tags;

    public function __construct()
    {
        $this->setCreatedAt();

        $this->tags = new ArrayCollection();
    }

    public function getId()
    {
        return $this->id;
    }

    public function setId(int $id) :Operation
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

    public function setDescription(?string $description): Operation
    {
        $this->description = $description;
        return $this;
    }

    public function getCreatedAt(): \DatetimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DatetimeInterface $createdAt = new \DateTime()): Operation
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    public function getStartedAt(): \DatetimeInterface
    {
        return $this->startedAt;
    }

    public function setStartedAt(\DatetimeInterface $startedAt = new \DateTime()): Operation
    {
        $this->startedAt = $startedAt;
        if ($this->endedAt === null)
        {
            $this->endedAt = $startedAt;
        }
        return $this;
    }

    public function getEndedAt(): \DatetimeInterface
    {
        return $this->endedAt;
    }

    public function setEndedAt(\DatetimeInterface $endedAt = new \DateTime()): Operation
    {
        $this->endedAt = $endedAt;
        return $this;
    }

    public function removeTags(): self
    {
        $this->tags = new ArrayCollection();
        return $this;
    }

    /**
     * @return Collection<int, Tag>
     */
    public function getTags(): Collection
    {
        return $this->tags;
    }

    public function addTag(Tag $tag): self
    {
        if (!$this->tags->contains($tag)) {
            $this->tags->add($tag);
        }

        return $this;
    }

    public function removeTag(Tag $tag): self
    {
        $this->tags->removeElement($tag);

        return $this;
    }

}
