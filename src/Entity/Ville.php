<?php

namespace App\Entity;

use App\Repository\VilleRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=VilleRepository::class)
 */
class Ville
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $nom;

    /**
     * @ORM\Column(type="integer")
     */
    private $codePostal;

    /**
     * @ORM\OneToMany(targetEntity=Lieu::class, mappedBy="lieuVille", orphanRemoval=true)
     */
    private $villeLieux;

    public function __construct()
    {
        $this->villeLieux = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): self
    {
        $this->nom = $nom;

        return $this;
    }

    public function getCodePostal(): ?int
    {
        return $this->codePostal;
    }

    public function setCodePostal(int $codePostal): self
    {
        $this->codePostal = $codePostal;

        return $this;
    }

    /**
     * @return Collection|Lieu[]
     */
    public function getVilleLieux(): Collection
    {
        return $this->villeLieux;
    }

    public function addVilleLieux(Lieu $villeLieux): self
    {
        if (!$this->villeLieux->contains($villeLieux)) {
            $this->villeLieux[] = $villeLieux;
            $villeLieux->setLieuVille($this);
        }

        return $this;
    }

    public function removeVilleLieux(Lieu $villeLieux): self
    {
        if ($this->villeLieux->removeElement($villeLieux)) {
            // set the owning side to null (unless already changed)
            if ($villeLieux->getLieuVille() === $this) {
                $villeLieux->setLieuVille(null);
            }
        }

        return $this;
    }
}
