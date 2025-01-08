<?php

namespace App\Entity;

use App\Repository\CartsRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CartsRepository::class)]
class Carts
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\OneToOne(inversedBy: 'carts', cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?Users $user = null;

    /**
     * @var Collection<int, Items>
     */
    #[ORM\ManyToMany(targetEntity: Items::class, mappedBy: 'item')]
    private Collection $items;

    public function __construct()
    {
        $this->items = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?Users
    {
        return $this->user;
    }

    public function setUser(Users $user): static
    {
        $this->user = $user;

        return $this;
    }

    /**
     * @return Collection<int, Items>
     */
    public function getItems(): Collection
    {
        return $this->items;
    }

    public function addItem(Items $item): static
    {
        if (!$this->items->contains($item)) {
            $this->items->add($item);
            $item->addItem($this);
        }

        return $this;
    }

    public function removeItem(Items $item): static
    {
        if ($this->items->removeElement($item)) {
            $item->removeItem($this);
        }

        return $this;
    }
}
