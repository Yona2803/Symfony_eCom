<?php

namespace App\Entity;

use App\Repository\StateRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: StateRepository::class)]
class State
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
    #[ORM\OneToMany(targetEntity: OrderState::class, mappedBy: 'State')]
    private Collection $State;

    public function __construct()
    {
        $this->State = new ArrayCollection();
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
     * @return Collection<int, OrderState>
     */
    public function getState(): Collection
    {
        return $this->State;
    }

    public function addState(OrderState $State): static
    {
        if (!$this->State->contains($State)) {
            $this->State->add($State);
            $State->setState($this);
        }

        return $this;
    }

    public function removeState(OrderState $State): static
    {
        if ($this->State->removeElement($State)) {
            // set the owning side to null (unless already changed)
            if ($State->getState() === $this) {
                $State->setState(null);
            }
        }

        return $this;
    }

}
