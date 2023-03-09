<?php
namespace App\Validator;

use App\Repository\CoursRepository;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Exception\UnexpectedValueException;

class ProfesseurIsDisponibleValidator extends ConstraintValidator
{

    public ?CoursRepository $repository = null;

    public function validate($value, Constraint $constraint): void
    {
        $coursAtThisDateForThisProf = $this->repository->getByDateAndProfessor($value->getDateHeureDebut(),$value->getProfesseur());


        foreach($coursAtThisDateForThisProf as $cours)
        {
            if(($value->getDateHeureDebut() >= $cours->getDateHeureDebut() && $value->getDateHeureDebut() <= $cours->getDateHeureFin()) || ($value->getDateHeureDebut() <= $cours->getDateHeureDebut() && $value->getDateHeureFin() > $cours->getDateHeureDebut()))
            { 
                $this->context->buildViolation($constraint->message)
                ->setParameter('{{Cours}}',$cours->__toString())
                ->addViolation();
            }
        }
    }

    public function __construct(CoursRepository $CoursRepository)
    {
        $this->repository = $CoursRepository;
    }
}

?>