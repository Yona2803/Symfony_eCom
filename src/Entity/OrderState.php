<?php

namespace App\Entity;

use App\Repository\OrderStateRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: OrderStateRepository::class)]
class OrderState
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'OrdState')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Orders $OrdState = null;
    
    #[ORM\ManyToOne(inversedBy: 'StateStatus')]
    #[ORM\JoinColumn(nullable: false)]
    private ?StateStatus $StateStatus = null;

    #[ORM\ManyToOne(inversedBy: 'State')]
    #[ORM\JoinColumn(nullable: false)]
    private ?State $State = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getOrdState(): ?Orders
    {
        return $this->OrdState;
    }

    public function setOrdState(?Orders $OrdState): static
    {
        $this->OrdState = $OrdState;

        return $this;
    }

    public function getStateStatus(): ?StateStatus
    {
        return $this->StateStatus;
    }

    public function setStateStatus(?StateStatus $StateStatus): static
    {
        $this->StateStatus = $StateStatus;

        return $this;
    }
    
    public function getState(): ?State
    {
        return $this->State;
    }

    public function setState(?State $State): static
    {
        $this->State = $State;

        return $this;
    }


}
