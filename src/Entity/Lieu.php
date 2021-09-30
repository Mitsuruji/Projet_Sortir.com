<?php

namespace App\Entity;

use App\Repository\LieuRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=LieuRepository::class)
 */
class Lieu
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
     * @ORM\Column(type="string", length=250)
     */
    private $rue;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $latitude;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $longitude;

    /**
     * @ORM\OneToMany(targetEntity=Sortie::class, mappedBy="sortieLieu")
     */
    private $lieuSorties;

    /**
     * @var Ville
     * @Assert\Valid()
     * @Assert\Type(type="App\Entity\Ville")
     *
     * --lisaison unidirectionnelle de Lieu vers Ville
     *
     * @ORM\ManyToOne(targetEntity=Ville::class, inversedBy="villeLieux")
     * @ORM\JoinColumn(nullable=false)
     */
    private $lieuVille;

    public function __construct()
    {
        $this->lieuSorties = new ArrayCollection();
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

    public function getRue(): ?string
    {
        return $this->rue;
    }

    public function setRue(string $rue): self
    {
        $this->rue = $rue;

        return $this;
    }

    public function getLatitude(): ?float
    {
        return $this->latitude;
    }

    public function setLatitude(?float $latitude): self
    {
        $this->latitude = $latitude;

        return $this;
    }

    public function getLongitude(): ?float
    {
        return $this->longitude;
    }

    public function setLongitude(?float $longitude): self
    {
        $this->longitude = $longitude;

        return $this;
    }

    /**
     * @return Collection|Sortie[]
     */
    public function getLieuSorties(): Collection
    {
        return $this->lieuSorties;
    }

    public function addLieuSorties(Sortie $lieuSorties): self
    {
        if (!$this->lieuSorties->contains($lieuSorties)) {
            $this->lieuSorties[] = $lieuSorties;
            $lieuSorties->setSortieLieu($this);
        }

        return $this;
    }

    public function removeLieuSorties(Sortie $lieuSorties): self
    {
        if ($this->lieuSorties->removeElement($lieuSorties)) {
            // set the owning side to null (unless already changed)
            if ($lieuSorties->getSortieLieu() === $this) {
                $lieuSorties->setSortieLieu(null);
            }
        }

        return $this;
    }

    public function getLieuVille(): ?Ville
    {
        return $this->lieuVille;
    }

    public function setLieuVille(?Ville $lieuVille): self
    {
        $this->lieuVille = $lieuVille;

        return $this;
    }
}
