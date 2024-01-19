<?php

namespace App\Entity;

use App\Repository\SousCategoryRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SousCategoryRepository::class)]
class SousCategory
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $souscategory_name = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $colour = null;

    #[ORM\ManyToOne(inversedBy: 'sousCategories')]
    private ?Category $id_souscategory = null;

    #[ORM\OneToMany(mappedBy: 'souscate', targetEntity: Product::class)]
    private Collection $products;

    public function __construct()
    {
        $this->products = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSouscategoryName(): ?string
    {
        return $this->souscategory_name;
    }

    public function setSouscategoryName(?string $souscategory_name): static
    {
        $this->souscategory_name = $souscategory_name;

        return $this;
    }

    public function getColour(): ?string
    {
        return $this->colour;
    }

    public function setColour(?string $colour): static
    {
        $this->colour = $colour;

        return $this;
    }

    public function getIdSouscategory(): ?Category
    {
        return $this->id_souscategory;
    }

    public function setIdSouscategory(?Category $id_souscategory): static
    {
        $this->id_souscategory = $id_souscategory;

        return $this;
    }

    /**
     * @return Collection<int, Product>
     */
    public function getProducts(): Collection
    {
        return $this->products;
    }

    public function addProduct(Product $product): static
    {
        if (!$this->products->contains($product)) {
            $this->products->add($product);
            $product->setSouscate($this);
        }

        return $this;
    }

    public function removeProduct(Product $product): static
    {
        if ($this->products->removeElement($product)) {
            // set the owning side to null (unless already changed)
            if ($product->getSouscate() === $this) {
                $product->setSouscate(null);
            }
        }

        return $this;
    }
}
