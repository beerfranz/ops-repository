<?php

namespace App\Entity;

use App\Repository\TagRepository;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\ApiProperty;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TagRepository::class)]
#[ORM\UniqueConstraint(columns:["name"])]
#[UniqueEntity(fields: ['name'])]
#[ApiResource(
    description: 'Tag',
    normalizationContext: ['groups' => ['tag:read']],
    denormalizationContext: ['groups' => ['tag:write']],
)]
class Tag
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    #[ApiProperty(identifier: false)]
    private $id;

    #[ORM\Column(type: 'string', length: 255)]
    #[Groups(['tag:read', 'tag:write', 'operation:read', 'operation:write'])]
    #[ApiProperty(identifier: true)]
    private $name;

    #[ORM\ManyToMany(targetEntity: Operation::class, mappedBy: 'tags', cascade: ["persist"])]
    #[Groups(['tag:read'])]
    #[ApiSubresource]
    private Collection $operations;

    public function __construct()
    {
        $this->operations = new ArrayCollection();
    }

    public function getId()
    {
        return $this->id;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setName(string $name): Tag
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return Collection<int, Operation>
     */
    public function getOperations(): Collection
    {
        return $this->operations;
    }

    public function addOperation(Operation $operation): self
    {
        if (!$this->operations->contains($operation)) {
            $this->operations->add($operation);
            $operation->addTag($this);
        }

        return $this;
    }

    public function removeOperation(Operation $operation): self
    {
        if ($this->operations->removeElement($operation)) {
            $operation->removeTag($this);
        }

        return $this;
    }

}
