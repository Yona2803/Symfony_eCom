<?php

namespace App\Entity;

use App\Repository\StateStatusRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: StateStatusRepository::class)]
class StateStatus
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 30, nullable: true)]
    private ?string $name = null;

    /**
     * @var Collection<int, OrderState>
     */
    #[ORM\OneToMany(targetEntity: OrderState::class, mappedBy: 'StateStatus')]
    private Collection $StateStatus;

    public function __construct()
    {
        $this->StateStatus = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): static
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return Collection<int, StateStatus>
     */
    public function getStateStatus(): Collection
    {
        return $this->StateStatus;
    }

    public function setStateStatus(?StateStatus $StateStatus): void
    {
        $this->StateStatus = $StateStatus;
    }

    public function addStateStatus(StateStatus $StateStatus): static
    {
        if (!$this->StateStatus->contains($StateStatus)) {
            $this->StateStatus->add($StateStatus);
            $StateStatus->setStateStatus($this);
        }

        return $this;
    }

    public function removeStateStatus(StateStatus $StateStatus): static
    {
        if ($this->StateStatus->removeElement($StateStatus)) {
            // set the owning side to null (unless already changed)
            if ($StateStatus->getStateStatus() === $this) {
                $StateStatus->setStateStatus(null);
            }
        }

        return $this;
    }
}
