<?php

namespace App\Entity;

use App\Repository\ProductRepository;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: ProductRepository::class)]
#[ApiResource(
        operations: [
            new Get(normalizationContext: ['groups' => 'product:item']),
            new GetCollection(normalizationContext: ['groups' => 'product:list'])
        ],
        paginationEnabled: false,
    )]
class Product
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['product:list', 'product:item'])]
    private ?string $name = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['product:list', 'product:item'])]
    private ?string $description = null;

    #[ORM\Column]
    #[Groups(['product:list', 'product:item'])]
    private ?float $price = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['product:list', 'product:item'])]
    private ?string $img = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['product:list', 'product:item'])]
    private ?int $stock = null;

    #[ORM\Column]
    #[Groups(['product:list', 'product:item'])]
    private ?bool $promotion = null;

    #[ORM\Column]
    #[Groups(['product:list', 'product:item'])]
    private ?int $discount = null;

    #[ORM\ManyToOne(inversedBy: 'products')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['product:list', 'product:item'])]
    private ?Category $category_id = null;

    #[ORM\ManyToOne(inversedBy: 'products')]
    #[Groups(['product:list', 'product:item'])]
    private ?SousCategory $souscate = null;

    #[ORM\ManyToOne(inversedBy: 'product')]
    #[Groups(['product:list', 'product:item'])]
    private ?Orders $orders = null;

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

    public function setDescription(?string $description): static
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

    public function getImg(): ?string
    {
        return $this->img;
    }

    public function setImg(?string $img): static
    {
        $this->img = $img;

        return $this;
    }

    public function getStock(): ?int
    {
        return $this->stock;
    }

    public function setStock(?int $stock): static
    {
        $this->stock = $stock;

        return $this;
    }

    public function isPromotion(): ?bool
    {
        return $this->promotion;
    }

    public function setPromotion(bool $promotion): static
    {
        $this->promotion = $promotion;

        return $this;
    }

    public function getDiscount(): ?int
    {
        return $this->discount;
    }

    public function setDiscount(int $discount): static
    {
        $this->discount = $discount;

        return $this;
    }

    public function getCategoryId(): ?Category
    {
        return $this->category_id;
    }

    public function setCategoryId(?Category $category_id): static
    {
        $this->category_id = $category_id;

        return $this;
    }

    public function getSouscate(): ?SousCategory
    {
        return $this->souscate;
    }

    public function setSouscate(?SousCategory $souscate): static
    {
        $this->souscate = $souscate;

        return $this;
    }

    public function getOrders(): ?Orders
    {
        return $this->orders;
    }

    public function setOrders(?Orders $orders): static
    {
        $this->orders = $orders;

        return $this;
    }
}
