<?php

namespace App\Entity;

use App\Repository\EtatRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=EtatRepository::class)
 */
class Etat
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $libelle;

    /**
     * @ORM\OneToMany(targetEntity=Sortie::class, mappedBy="etat")
     */
    private $sortiesEtat;

    public function __construct()
    {
        $this->sortiesEtat = new ArrayCollection();
    }


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLibelle(): ?string
    {
        return $this->libelle;
    }

    public function setLibelle(string $libelle): self
    {
        $this->libelle = $libelle;

        return $this;
    }

    /**
     * @return Collection|Sortie[]
     */
    public function getSortiesEtat(): Collection
    {
        return $this->sortiesEtat;
    }

    public function addSortiesEtat(Sortie $sortiesEtat): self
    {
        if (!$this->sortiesEtat->contains($sortiesEtat)) {
            $this->sortiesEtat[] = $sortiesEtat;
            $sortiesEtat->setEtat($this);
        }

        return $this;
    }

    public function removeSortiesEtat(Sortie $sortiesEtat): self
    {
        if ($this->sortiesEtat->removeElement($sortiesEtat)) {
            // set the owning side to null (unless already changed)
            if ($sortiesEtat->getEtat() === $this) {
                $sortiesEtat->setEtat(null);
            }
        }

        return $this;
    }

}
