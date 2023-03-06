<?php

namespace App\Entity;

use App\Repository\ProfesseurRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[ORM\Entity(repositoryClass: ProfesseurRepository::class)]
#[UniqueEntity('email')]
class Professeur implements \JsonSerializable 
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    private ?string $nom = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    private ?string $prenom = null;

    #[ORM\Column(length: 255, unique: true)]
    #[Assert\Email(message: "Le mail n'est pas bon")]
    private ?string $email = null;

    #[ORM\OneToMany(mappedBy: 'professeur', targetEntity: Avis::class, orphanRemoval: true)]
    private Collection $avis;

    #[ORM\ManyToMany(targetEntity: Matiere::class, inversedBy: 'professeurs')]
    private Collection $matieres;

    public function __construct()
    {
        $this->avis = new ArrayCollection();
        $this->matieres = new ArrayCollection();
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

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setPrenom(string $prenom): self
    {
        $this->prenom = $prenom;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * @return Collection<int, Avis>
     */
    public function getAvis(): Collection
    {
        return $this->avis;
    }

    public function addAvis(Avis $avi): self
    {
        if (!$this->avis->contains($avi)) {
            $this->avis->add($avi);
            $avi->setProfesseur($this);
        }

        return $this;
    }

    public function removeAvis(Avis $avi): self
    {
        if ($this->avis->removeElement($avi)) {
            // set the owning side to null (unless already changed)
            if ($avi->getProfesseur() === $this) {
                $avi->setProfesseur(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, matiere>
     */
    public function getMatieres(): Collection
    {
        return $this->matieres;
    }

    public function addMatiere(matiere $matiere): self
    {
        if (!$this->matieres->contains($matiere)) {
            $this->matieres->add($matiere);
        }

        return $this;
    }

    public function removeMatiere(matiere $matiere): self
    {
        $this->matieres->removeElement($matiere);

        return $this;
    }

    public function __toString()
    {
        return sprintf('%s %s (%s)', $this->prenom, $this->nom, $this->email);
    }

    public function jsonSerialize(): mixed
    {
        return [
            'id' => $this->getId(),
            'nom' => $this->getNom(),
            'prenom'=> $this->getPrenom(),
            'email'=> $this->getEmail(),
            'matieres' => $this->getMatieres()->toArray()
        ];
    }
}
