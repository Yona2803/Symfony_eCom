<?php

namespace App\Entity;

use App\Repository\ItemsRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ItemsRepository::class)]
class Items
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 200)]
    private ?string $name = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $description = null;

    #[ORM\Column]
    private ?float $price = null;

    #[ORM\Column]
    private ?int $stock = null;

    #[ORM\Column(type: Types::BLOB, nullable: true)]
    private $itemImage;

    #[ORM\ManyToOne(inversedBy: 'items')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Categories $category = null;

    /**
     * @var Collection<int, OrderDetails>
     */
    #[ORM\OneToMany(targetEntity: OrderDetails::class, mappedBy: 'item', orphanRemoval: true)]
    private Collection $orderDetails;

    /**
     * @var Collection<int, Carts>
     */
    #[ORM\ManyToMany(targetEntity: Carts::class, inversedBy: 'items')]
    private Collection $item;

    /**
     * @var Collection<int, WishList>
     */
    #[ORM\ManyToMany(targetEntity: WishList::class, mappedBy: 'item')]
    private Collection $wishlist;

    #[ORM\Column(length: 60)]
    private ?array $tags = [];

    public function __construct()
    {
        $this->orderDetails = new ArrayCollection();
        $this->item = new ArrayCollection();
        $this->wishlist = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function setPrice(float $price): static
    {
        $this->price = $price;

        return $this;
    }

    public function getStock(): ?int
    {
        return $this->stock;
    }

    public function setStock(int $stock): static
    {
        $this->stock = $stock;

        return $this;
    }

    public function getItemImage()
    {
        return $this->itemImage;
    }

    public function setItemImage($itemImage): static
    {
        $this->itemImage = $itemImage;

        return $this;
    }

    public function getCategory(): ?Categories
    {
        return $this->category;
    }

    public function setCategory(?Categories $category): static
    {
        $this->category = $category;

        return $this;
    }

    /**
     * @return Collection<int, OrderDetails>
     */
    public function getOrderDetails(): Collection
    {
        return $this->orderDetails;
    }

    public function addOrderDetail(OrderDetails $orderDetail): static
    {
        if (!$this->orderDetails->contains($orderDetail)) {
            $this->orderDetails->add($orderDetail);
            $orderDetail->setItem($this);
        }

        return $this;
    }

    public function removeOrderDetail(OrderDetails $orderDetail): static
    {
        if ($this->orderDetails->removeElement($orderDetail)) {
            // set the owning side to null (unless already changed)
            if ($orderDetail->getItem() === $this) {
                $orderDetail->setItem(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Carts>
     */
    public function getItem(): Collection
    {
        return $this->item;
    }

    public function addItem(Carts $item): static
    {
        if (!$this->item->contains($item)) {
            $this->item->add($item);
        }

        return $this;
    }

    public function removeItem(Carts $item): static
    {
        $this->item->removeElement($item);

        return $this;
    }

    /**
     * @return Collection<int, WishList>
     */
    public function getWishlist(): Collection
    {
        return $this->wishlist;
    }

    public function addWishlist(WishList $wishlist): static
    {
        if (!$this->wishlist->contains($wishlist)) {
            $this->wishlist->add($wishlist);
            $wishlist->addItem($this);
        }

        return $this;
    }

    public function removeWishlist(WishList $wishlist): static
    {
        if ($this->wishlist->removeElement($wishlist)) {
            $wishlist->removeItem($this);
        }

        return $this;
    }

    public function getTags(): ?array
    {
        return $this->tags;
    }

    public function setTags(array $tags): static
    {
        $this->tags = $tags;
        return $this;
    }
}
