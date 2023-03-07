<?php

namespace App\Entity;

use App\Repository\CoursRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

use Symfony\Component\HttpClient\HttpClient;


#[ORM\Entity(repositoryClass: CoursRepository::class)]
class Cours implements \JsonSerializable 
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[Assert\Expression('this.debutDateNotInThePast()==true',message:'Vous ne pouvez creer de cours dans le passe Marty')]
    #[Assert\Expression('this.coursCantGoThroughLunchBreak()==true',message:'')]
    #[Assert\Expression('this.startDateOutOfBounds()==true',message:'Vous ne pouvez creer de cours depassant des limites horaires imposées (8H minimum)')]
    private ?\DateTimeInterface $dateHeureDebut = null;
    
    #[Assert\Expression('this.endDateNotBeforeStart()==true',message:'Vous ne pouvez creer de cours qui se termine avant commencement')]
    #[Assert\Expression('this.coursLengthValid()==true',message:'Vous ne pouvez creer de cours durant moins de 1H ou plus de 2H')]
    #[Assert\Expression('this.coursCantGoThroughLunchBreak()==true',message:'Vous ne pouvez creer de cours enpiétant sur la pause repas')]
    #[Assert\Expression('this.endDateOutOfBounds()==true',message:'Vous ne pouvez creer de cours depassant des limites horaires imposées (18H maximum)')]
    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $dateHeureFin = null;

    #[ORM\ManyToOne(inversedBy: 'cours')]
    #[Assert\Expression('this.validateProfesseur()==true',message:'Vous ne pouvez attribuer un professeur à un cours d\'une matiere qu\'il n\'enseigne pas')]
   // #[Assert\Expression('this.professeurIsDisponible()==true',message:'Ce professeur est deja affecté à un autre cours à cette période')]

    private ?Professeur $professeur = null;

    #[ORM\ManyToOne(inversedBy: 'cours')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Matiere $matiere = null;

    #[ORM\ManyToOne(inversedBy: 'cours')]
    #[ORM\JoinColumn(nullable: false)]
    //#[Assert\Expression("this.salleIsDisponible()==true",message:'Cette salle est deja occupée à cette période')]
    private ?Salle $salle = null;

    #[ORM\ManyToOne(inversedBy: 'cours')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Type $type = null;

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

    /* OPERATIONS DE VERIFICATION DE PARAMETRE */
    public function validateProfesseur(): bool
    {
       foreach( $this->getProfesseur()->getMatieres() as $matiere)
        {
            if($matiere->getTitre() == $this->getMatiere()->getTitre())
            {return true;};
        };
        return false;
    }

    public function debutDateNotInThePast(): bool
    {
       return \DateTime::createFromFormat('Y-m-d H-i-s',$this->getDateHeureDebut()->format('Y-m-d H-i-s')) > new \DateTime();
    }

    public function endDateNotBeforeStart(): bool
    {
       return \DateTime::createFromFormat('Y-m-d H-i-s',$this->getDateHeureDebut()->format('Y-m-d H-i-s')) < \DateTime::createFromFormat('Y-m-d H-i-s',$this->getDateHeureFin()->format('Y-m-d H-i-s'));
    }

    public function coursLengthValid(): bool
    {
        $interval = \DateTime::createFromFormat('Y-m-d H-i-s',$this->getDateHeureDebut()->format('Y-m-d H-i-s'))->diff(\DateTime::createFromFormat('Y-m-d H-i-s',$this->getDateHeureFin()->format('Y-m-d H-i-s')));
        $length = $interval->i + $interval->h*60;
        return $length < 60 || $length > 120 ? false:true;
    }

    public function coursCantGoThroughLunchBreak(): bool
    {
        $intervalDebut = \DateTime::createFromFormat('Y-m-d H-i-s',$this->getDateHeureDebut()->format('Y-m-d H-i-s'))->diff((\DateTime::createFromFormat('Y-m-d H-i-s',$this->getDateHeureDebut()->format('Y-m-d H-i-s')))->setTime(0,0,0));
        $intervalFin = \DateTime::createFromFormat('Y-m-d H-i-s',$this->getDateHeureFin()->format('Y-m-d H-i-s'))->diff((\DateTime::createFromFormat('Y-m-d H-i-s',$this->getDateHeureFin()->format('Y-m-d H-i-s')))->setTime(0,0,0));

        $dureeDebut = $intervalDebut->h*60+$intervalDebut->i;
        $dureeFin = $intervalFin->h*60+$intervalFin->i;

        return ($dureeDebut < 750 && $dureeFin > 750 ) || ($dureeDebut > 750 && $dureeDebut < 840)? false:true;
    }

    public function endDateOutOfBounds(): bool
    {
        return (\DateTime::createFromFormat('Y-m-d H-i-s',$this->getDateHeureFin()->format('Y-m-d H-i-s'))->diff((\DateTime::createFromFormat('Y-m-d H-i-s',$this->getDateHeureFin()->format('Y-m-d H-i-s')))->setTime(0,0,0)))->h > 18 ? false:true;
    }

    public function startDateOutOfBounds(): bool
    {
        return (\DateTime::createFromFormat('Y-m-d H-i-s',$this->getDateHeureDebut()->format('Y-m-d H-i-s'))->diff((\DateTime::createFromFormat('Y-m-d H-i-s',$this->getDateHeureDebut()->format('Y-m-d H-i-s')))->setTime(0,0,0)))->h < 8 ? false:true;
    }

    public function professeurIsDisponible(CoursRepository $rep): bool
    {

        return true;
    } 

}
