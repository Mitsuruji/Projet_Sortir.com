<?php

namespace App\Entity;

use App\Repository\CampusRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=CampusRepository::class)
 */
class Campus
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
     * @ORM\OneToMany(targetEntity=Participant::class, mappedBy="campus")
     */
    private $participants;

    /**
     * @ORM\OneToMany(targetEntity=Sortie::class, mappedBy="campusOrganisateur", orphanRemoval=true)
     */
    private $sortiesCampus;

    public function __construct()
    {
        $this->participants = new ArrayCollection();
        $this->sortiesCampus = new ArrayCollection();
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

    /**
     * @return Collection|Participant[]
     */
    public function getParticipants(): Collection
    {
        return $this->participants;
    }

    public function addParticipant(Participant $participant): self
    {
        if (!$this->participants->contains($participant)) {
            $this->participants[] = $participant;
            $participant->setCampus($this);
        }

        return $this;
    }

    public function removeParticipant(Participant $participant): self
    {
        if ($this->participants->removeElement($participant)) {
            // set the owning side to null (unless already changed)
            if ($participant->getCampus() === $this) {
                $participant->setCampus(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Sortie[]
     */
    public function getSortiesCampus(): Collection
    {
        return $this->sortiesCampus;
    }

    public function addSortiesCampus(Sortie $sortiesCampus): self
    {
        if (!$this->sortiesCampus->contains($sortiesCampus)) {
            $this->sortiesCampus[] = $sortiesCampus;
            $sortiesCampus->setCampusOrganisateur($this);
        }

        return $this;
    }

    public function removeSortiesCampus(Sortie $sortiesCampus): self
    {
        if ($this->sortiesCampus->removeElement($sortiesCampus)) {
            // set the owning side to null (unless already changed)
            if ($sortiesCampus->getCampusOrganisateur() === $this) {
                $sortiesCampus->setCampusOrganisateur(null);
            }
        }

        return $this;
    }
//
//    public function __toString() {
//        return $this->nom;
//    }
}
