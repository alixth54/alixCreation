<?php

namespace App\Entity;

use App\Repository\CategoryRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CategoryRepository::class)]
class Category
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $category_name = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $sous_category_name = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $colour = null;

    #[ORM\OneToMany(mappedBy: 'category_id', targetEntity: Product::class, orphanRemoval: true)]
    private Collection $products;

    #[ORM\OneToMany(mappedBy: 'souscategory_id', targetEntity: Product::class)]
    private Collection $productcopy;

    #[ORM\OneToMany(mappedBy: 'id_souscategory', targetEntity: SousCategory::class)]
    private Collection $sousCategories;

    public function __construct()
    {
        $this->products = new ArrayCollection();
        $this->productcopy = new ArrayCollection();
        $this->sousCategories = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCategoryName(): ?string
    {
        return $this->category_name;
    }

    public function setCategoryName(string $category_name): static
    {
        $this->category_name = $category_name;

        return $this;
    }

    public function getSousCategoryName(): ?string
    {
        return $this->sous_category_name;
    }

    public function setSousCategoryName(?string $sous_category_name): static
    {
        $this->sous_category_name = $sous_category_name;

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
            $product->setCategoryId($this);
        }

        return $this;
    }

    public function removeProduct(Product $product): static
    {
        if ($this->products->removeElement($product)) {
            // set the owning side to null (unless already changed)
            if ($product->getCategoryId() === $this) {
                $product->setCategoryId(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Product>
     */
    public function getProductcopy(): Collection
    {
        return $this->productcopy;
    }

    public function addProductcopy(Product $productcopy): static
    {
        if (!$this->productcopy->contains($productcopy)) {
            $this->productcopy->add($productcopy);
            $productcopy->setSouscategoryId($this);
        }

        return $this;
    }

    public function removeProductcopy(Product $productcopy): static
    {
        if ($this->productcopy->removeElement($productcopy)) {
            // set the owning side to null (unless already changed)
            if ($productcopy->getSouscategoryId() === $this) {
                $productcopy->setSouscategoryId(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, SousCategory>
     */
    public function getSousCategories(): Collection
    {
        return $this->sousCategories;
    }

    public function addSousCategory(SousCategory $sousCategory): static
    {
        if (!$this->sousCategories->contains($sousCategory)) {
            $this->sousCategories->add($sousCategory);
            $sousCategory->setIdSouscategory($this);
        }

        return $this;
    }

    public function removeSousCategory(SousCategory $sousCategory): static
    {
        if ($this->sousCategories->removeElement($sousCategory)) {
            // set the owning side to null (unless already changed)
            if ($sousCategory->getIdSouscategory() === $this) {
                $sousCategory->setIdSouscategory(null);
            }
        }

        return $this;
    }
}
