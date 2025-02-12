<?php

namespace App\Entity;

use App\Repository\OrderStatusRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: OrderStatusRepository::class)]
class OrderStatus
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    private ?string $statusName = null;

    /**
     * @var Collection<int, Orders>
     */
    #[ORM\OneToMany(targetEntity: Orders::class, mappedBy: 'orderStatus')]
    private Collection $orderId;

    public function __construct()
    {
        $this->orderId = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getStatusName(): ?string
    {
        return $this->statusName;
    }

    public function setStatusName(string $statusName): static
    {
        $this->statusName = $statusName;

        return $this;
    }

    /**
     * @return Collection<int, Orders>
     */
    public function getOrderId(): Collection
    {
        return $this->orderId;
    }

    public function addOrderId(Orders $orderId): static
    {
        if (!$this->orderId->contains($orderId)) {
            $this->orderId->add($orderId);
            $orderId->setOrderStatus($this);
        }

        return $this;
    }

    public function removeOrderId(Orders $orderId): static
    {
        if ($this->orderId->removeElement($orderId)) {
            // set the owning side to null (unless already changed)
            if ($orderId->getOrderStatus() === $this) {
                $orderId->setOrderStatus(null);
            }
        }

        return $this;
    }
}
