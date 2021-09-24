<?php

namespace App\Data;

use App\DataRepository\SearchOptionsRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=SearchOptionsRepository::class)
 */
class SearchOptions
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="object", nullable=true)
     */
    private $choixCampus;

    /**
     * @ORM\Column(type="object", nullable=true)
     */
    private $currentUser;

    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    private $filterNomSortie;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $filterDateMin;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $filterDateMax;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $filterIsOrganisateur;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $filterIsInscris;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $filterIsPasInscris;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $filterSortiesPassees;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getChoixCampus(): ?object
    {
        return $this->choixCampus;
    }

    public function setChoixCampus(?object $choixCampus): self
    {
        $this->choixCampus = $choixCampus;

        return $this;
    }

    public function getCurrentUser(): ?object
    {
        return $this->currentUser;
    }

    public function setCurrentUser(?object $currentUser): self
    {
        $this->currentUser = $currentUser;

        return $this;
    }

    public function getFilterNomSortie(): ?string
    {
        return $this->filterNomSortie;
    }

    public function setFilterNomSortie(?string $filterNomSortie): self
    {
        $this->filterNomSortie = $filterNomSortie;

        return $this;
    }

    public function getFilterDateMin(): ?\DateTimeInterface
    {
        return $this->filterDateMin;
    }

    public function setFilterDateMin(?\DateTimeInterface $filterDateMin): self
    {
        $this->filterDateMin = $filterDateMin;

        return $this;
    }

    public function getFilterDateMax(): ?\DateTimeInterface
    {
        return $this->filterDateMax;
    }

    public function setFilterDateMax(?\DateTimeInterface $filterDateMax): self
    {
        $this->filterDateMax = $filterDateMax;

        return $this;
    }

    public function getFilterIsOrganisateur(): ?bool
    {
        return $this->filterIsOrganisateur;
    }

    public function setFilterIsOrganisateur(?bool $filterIsOrganisateur): self
    {
        $this->filterIsOrganisateur = $filterIsOrganisateur;

        return $this;
    }

    public function getFilterIsInscris(): ?bool
    {
        return $this->filterIsInscris;
    }

    public function setFilterIsInscris(?bool $filterIsInscris): self
    {
        $this->filterIsInscris = $filterIsInscris;

        return $this;
    }

    public function getFilterIsPasInscris(): ?bool
    {
        return $this->filterIsPasInscris;
    }

    public function setFilterIsPasInscris(?bool $filterIsPasInscris): self
    {
        $this->filterIsPasInscris = $filterIsPasInscris;

        return $this;
    }

    public function getFilterSortiesPassees(): ?bool
    {
        return $this->filterSortiesPassees;
    }

    public function setFilterSortiesPassees(?bool $filterSortiesPassees): self
    {
        $this->filterSortiesPassees = $filterSortiesPassees;

        return $this;
    }
}
