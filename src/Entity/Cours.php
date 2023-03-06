<?php

namespace App\Entity;

use App\Repository\CoursRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[ORM\Entity(repositoryClass: CoursRepository::class)]
class Cours implements \JsonSerializable 
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $dateHeureDebut = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $dateHeureFin = null;

    #[ORM\ManyToOne(inversedBy: 'cours')]
    #[Assert\Expression('this.validateProfesseur()==true')]
    private ?Professeur $professeur = null;

    #[ORM\ManyToOne(inversedBy: 'cours')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Matiere $matiere = null;

    #[ORM\ManyToOne(inversedBy: 'cours')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Salle $salle = null;

    #[ORM\ManyToOne(inversedBy: 'cours')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Type $type = null;

    public function validateProfesseur(): bool
    {
       foreach( $this->getProfesseur()->getMatieres() as $matiere)
        {
            if($matiere->getTitre() == $this->getMatiere()->getTitre())
            {return true;};
        };
        return false;
    }
    public function getId(): ?int
    {
        return $this->id;
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

    public function getDateHeureFin(): ?\DateTimeInterface
    {
        return $this->dateHeureFin;
    }

    public function setDateHeureFin(\DateTimeInterface $dateHeureFin): self
    {
        $this->dateHeureFin = $dateHeureFin;

        return $this;
    }

    public function getProfesseur(): ?Professeur
    {
        return $this->professeur;
    }

    public function setProfesseur(?Professeur $professeur): self
    {
        $this->professeur = $professeur;

        return $this;
    }

    public function getMatiere(): ?matiere
    {
        return $this->matiere;
    }

    public function setMatiere(?matiere $matiere): self
    {
        $this->matiere = $matiere;

        return $this;
    }

    public function getSalle(): ?Salle
    {
        return $this->salle;
    }

    public function setSalle(?Salle $salle): self
    {
        $this->salle = $salle;

        return $this;
    }

    public function toStringDateDebut()
    {
        return $this->dateHeureDebut->format('Y-m-d H:i:s');
    }

    public function toStringDateFin()
    {
        return $this->dateHeureFin->format('Y-m-d H:i:s');
    }

    public function __toString()
    {
        return sprintf('%s %s %s (%s)', $this->dateHeureDebut->format('Y-m-d H:i:s'), $this->dateHeureFin->format('Y-m-d H:i:s'), $this->getType(), $this->getSalle());
    }

    public function jsonSerialize(): mixed
    {
        return [
            'id' => $this->getId(),
            'dateHeureDebut' => $this->getDateHeureDebut(),
            'dateHeureFin'=> $this->getDateHeureFin(),
            'type'=> $this->getType(),
            'professeur' => $this->getProfesseur(),
            'matiere' => $this->getMatiere(),
            'salle' => $this->getSalle(),
        ];
    }

    // TODO
    public function from($data): self {
        $this->dateHeureDebut = \DateTime::createFromFormat('Y-m-d H:i:s','2023-07-07 10:00:00') ?? $this->dateHeureDebut;
        $this->dateHeureFin = \DateTime::createFromFormat('Y-m-d H:i:s',$data['dateHeureFin']['date']) ?? $this->dateHeureFin;
        $this->type = $data['type'] ?? $this->type;
        $this->professeur = $data['professeur'] ?? $this->professeur;
        $this->matiere = $data['matiere'] ?? $this->matiere;
        $this->salle = $data['salle'] ?? $this->salle;

        return $this;
    }

    public function getType(): ?Type
    {
        return $this->type;
    }

    public function setType(?Type $type): self
    {
        $this->type = $type;

        return $this;
    }
}
