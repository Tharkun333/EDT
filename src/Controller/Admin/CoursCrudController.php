<?php

namespace App\Controller\Admin;

use App\Entity\Cours;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;

class CoursCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Cours::class;
    }

    
    public function configureFields(string $pageName): iterable
    {
        return [
            'dateHeureDebut',
            'dateHeureFin',
            AssociationField::new('type'),
            AssociationField::new('professeur'),
            AssociationField::new('matiere'),
            AssociationField::new('salle')
        ];
    }
    
}
