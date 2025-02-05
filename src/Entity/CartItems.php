<?php

namespace App\Entity;

use App\Repository\CartItemsRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CartItemsRepository::class)]
class CartItems
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Carts::class, inversedBy: 'cartItems')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Carts $cart = null;

    #[ORM\ManyToOne(targetEntity: Items::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?Items $item = null;

    #[ORM\Column(type: 'integer')]
    private ?int $quantity = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCart(): ?Carts
    {
        return $this->cart;
    }

    public function setCart(?Carts $cart): self
    {
        $this->cart = $cart;
        return $this;
    }

    public function getItem(): ?Items
    {
        return $this->item;
    }

    public function setItem(Items $item): static
    {
        $this->item = $item;
        return $this;
    }

    public function getQuantity(): ?int
    {
        return $this->quantity;
    }

    public function setQuantity(int $quantity): static
    {
        $this->quantity = $quantity;
        return $this;
    }
}
