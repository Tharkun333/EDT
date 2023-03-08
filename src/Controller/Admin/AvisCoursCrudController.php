<?php

namespace App\Controller\Admin;

use App\Entity\AvisCours;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;

class AvisCoursCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return AvisCours::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            ChoiceField::new('note')
                ->setChoices(fn () => [0 => 0, 1 => 1, 2 => 2, 3 => 3, 4 => 4, 5 => 5])
                ->renderAsNativeWidget(),
            'commentaire',
            'emailEtudiant',
            AssociationField::new('cours'),
        ];
    }
}
