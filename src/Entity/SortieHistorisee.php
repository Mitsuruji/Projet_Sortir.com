<?php

namespace App\Entity;

use App\Repository\SortieHistoriseeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=SortieHistoriseeRepository::class)
 */
class SortieHistorisee
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $nom;

    /**
     * @ORM\Column(type="datetime")
     */
    private $dateHeureDebut;

    /**
     * @ORM\Column(type="time")
     */
    private $duree;

    /**
     * @ORM\Column(type="datetime")
     */
    private $dateLimiteInscription;

    /**
     * @ORM\Column(type="integer")
     */
    private $nbInscriptionsMax;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $infosSortie;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $etat;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $sortieLieu;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $sortieVille;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $participantOrganisateur;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $campusOrganisateur;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $motifAnnulation;

    /**
     * @ORM\Column(type="array", nullable=true)
     */
    private $participantInscris = [];



    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): self
    {
        $this->nom = $nom;

        return $this;
    }

    public function getDateHeureDebut(): ?\DateTimeInterface
    {
        return $this->dateHeureDebut;
    }

    public function setDateHeureDebut(\DateTimeInterface $dateHeureDebut): self
    {
        $this->dateHeureDebut = $dateHeureDebut;

        return $this;
    }

    public function getDuree(): ?\DateTimeInterface
    {
        return $this->duree;
    }

    public function setDuree(\DateTimeInterface $duree): self
    {
        $this->duree = $duree;

        return $this;
    }

    public function getDateLimiteInscription(): ?\DateTimeInterface
    {
        return $this->dateLimiteInscription;
    }

    public function setDateLimiteInscription(\DateTimeInterface $dateLimiteInscription): self
    {
        $this->dateLimiteInscription = $dateLimiteInscription;

        return $this;
    }

    public function getNbInscriptionsMax(): ?int
    {
        return $this->nbInscriptionsMax;
    }

    public function setNbInscriptionsMax(int $nbInscriptionsMax): self
    {
        $this->nbInscriptionsMax = $nbInscriptionsMax;

        return $this;
    }

    public function getInfosSortie(): ?string
    {
        return $this->infosSortie;
    }

    public function setInfosSortie(?string $infosSortie): self
    {
        $this->infosSortie = $infosSortie;

        return $this;
    }

    public function getEtat(): ?string
    {
        return $this->etat;
    }

    public function setEtat(?string $etat): self
    {
        $this->etat = $etat;

        return $this;
    }

    public function getSortieLieu(): ?string
    {
        return $this->sortieLieu;
    }

    public function setSortieLieu(?string $sortieLieu): self
    {
        $this->sortieLieu = $sortieLieu;

        return $this;
    }

    public function getSortieVille(): ?string
    {
        return $this->sortieVille;
    }

    public function setSortieVille(?string $sortieVille): self
    {
        $this->sortieVille = $sortieVille;

        return $this;
    }

    public function getParticipantOrganisateur(): ?string
    {
        return $this->participantOrganisateur;
    }

    public function setParticipantOrganisateur(?string $participantOrganisateur): self
    {
        $this->participantOrganisateur = $participantOrganisateur;

        return $this;
    }

    public function getCampusOrganisateur(): ?string
    {
        return $this->campusOrganisateur;
    }

    public function setCampusOrganisateur(?string $campusOrganisateur): self
    {
        $this->campusOrganisateur = $campusOrganisateur;

        return $this;
    }



    public function getMotifAnnulation(): ?string
    {
        return $this->motifAnnulation;
    }

    public function setMotifAnnulation(?string $motifAnnulation): self
    {
        $this->motifAnnulation = $motifAnnulation;

        return $this;
    }

    public function getParticipantInscris(): ?array
    {
        return $this->participantInscris;
    }

    public function setParticipantInscris(?array $participantInscris): self
    {
        $this->participantInscris = $participantInscris;

        return $this;
    }
}
