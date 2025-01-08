<?php

namespace App\Entity;

use App\Repository\WishListRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: WishListRepository::class)]
class WishList
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\OneToOne(inversedBy: 'wishList', cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?Users $user = null;

    /**
     * @var Collection<int, Items>
     */
    #[ORM\ManyToMany(targetEntity: Items::class, inversedBy: 'wishlist')]
    private Collection $item;

    public function __construct()
    {
        $this->item = new ArrayCollection();
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
    public function getItem(): Collection
    {
        return $this->item;
    }

    public function addItem(Items $item): static
    {
        if (!$this->item->contains($item)) {
            $this->item->add($item);
        }

        return $this;
    }

    public function removeItem(Items $item): static
    {
        $this->item->removeElement($item);

        return $this;
    }
}
