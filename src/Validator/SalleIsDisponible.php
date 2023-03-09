<?php
namespace App\Validator;

use App\Entity\Cours;
use Symfony\Component\Validator\Attribute\HasNamedArguments;
use Symfony\Component\Validator\Constraint;

#[\Attribute]
class SalleIsDisponible extends Constraint
{
    public $message = 'Le cours ne peut être créer car la salle est occupée par le cours :  {{Cours}}';

    public string $mode;

    public function getTargets(): string
    {
        return self::CLASS_CONSTRAINT;
    }
}
?>