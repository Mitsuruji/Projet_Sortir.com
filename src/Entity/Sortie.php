<?php

namespace App\Entity;

use App\Entity\Lieu;
use App\Repository\SortieRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use PhpParser\Node\Expr\Cast\Object_;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=SortieRepository::class)
 */
class Sortie
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $nom;

    /**
     * @ORM\Column(type="datetime")
     * @Assert\NotBlank
     * @Assert\GreaterThan("now")
     */
    protected $dateHeureDebut;

    /**
     * @ORM\Column(type="time")
     */
    private $duree;

    /**
     * @ORM\Column(type="datetime")
     * @Assert\NotBlank
     * @Assert\LessThan(propertyPath="dateHeureDebut")
     * @Assert\GreaterThan("now")
     */

    private $dateLimiteInscription;

    /**
     * @ORM\Column(type="integer")
     * @Assert\NotBlank
     */
    private $nbInscriptionsMax;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $infosSortie;

    /**
     * @ORM\ManyToOne(targetEntity=Etat::class, inversedBy="sortiesEtat")
     */
    private $etat;

    /**
     *
     * @var Lieu
     * @Assert\Valid()
     * @Assert\Type(type="App\Entity\Lieu")
     *
     * --lisaison unidirectionnelle de Sortie vers Lieu
     *
     * @ORM\ManyToOne(targetEntity=Lieu::class, inversedBy="lieuSorties", cascade={"persist"})
     * @ORM\JoinColumn(nullable=false)
     *
     *
     */
    private $sortieLieu;

    /**
     * @ORM\ManyToOne(targetEntity=Participant::class, inversedBy="sortiesOrganisees")
     * @ORM\JoinColumn(nullable=false)
     */
    private $participantOrganisateur;

    /**
     * @ORM\ManyToOne(targetEntity=Campus::class, inversedBy="sortiesCampus")
     * @ORM\JoinColumn(nullable=false)
     */
    private $campusOrganisateur;

    /**
     * @ORM\ManyToMany(targetEntity=Participant::class, mappedBy="sortiesParticipations")
     */
    private $participantInscrit;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $motifAnnulation;

    public function __construct()
    {
        $this->participantInscrit = new ArrayCollection();
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

    public function getDateHeureDebut(): ?\DateTimeInterface
    {
        return $this->dateHeureDebut;
    }

    public function setDateHeureDebut(?\DateTimeInterface $dateHeureDebut): self
    {
        $this->dateHeureDebut = $dateHeureDebut;

        return $this;
    }

    public function getDuree(): ?\DateTimeInterface
    {
        return $this->duree;
    }

    public function setDuree(?\DateTimeInterface $duree): self
    {
        $this->duree = $duree;

        return $this;
    }

    public function getDateLimiteInscription(): ?\DateTimeInterface
    {
        return $this->dateLimiteInscription;
    }

    public function setDateLimiteInscription(?\DateTimeInterface $dateLimiteInscription): self
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

    public function getEtat(): ?Etat
    {
        return $this->etat;
    }

    public function setEtat(?Etat $etat): self
    {
        $this->etat = $etat;

        return $this;
    }

    public function getSortieLieu(): ?Lieu
    {
        return $this->sortieLieu;
    }

    public function setSortieLieu(?Lieu $sortieLieu): self
    {
        $this->sortieLieu = $sortieLieu;

        return $this;
    }

    public function getParticipantOrganisateur(): ?Participant
    {
        return $this->participantOrganisateur;
    }

    public function setParticipantOrganisateur(?Participant $participantOrganisateur): self
    {
        $this->participantOrganisateur = $participantOrganisateur;

        return $this;
    }

    public function getCampusOrganisateur(): ?Campus
    {
        return $this->campusOrganisateur;
    }

    public function setCampusOrganisateur(?Campus $campusOrganisateur): self
    {
        $this->campusOrganisateur = $campusOrganisateur;

        return $this;
    }

    /**
     * @return Collection|Participant[]
     */
    public function getParticipantInscrit(): Collection
    {
        return $this->participantInscrit;
    }

    public function addParticipantInscrit(Participant $participantInscrit): self
    {
        if (!$this->participantInscrit->contains($participantInscrit)) {
            $this->participantInscrit[] = $participantInscrit;
            $participantInscrit->addSortiesParticipation($this);
        }

        return $this;
    }

    public function removeParticipantInscrit(Participant $participantInscrit): self
    {
        if ($this->participantInscrit->removeElement($participantInscrit)) {
            $participantInscrit->removeSortiesParticipation($this);
        }

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

}
