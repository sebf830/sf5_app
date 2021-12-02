<?php

namespace App\Controller\Admin;

use App\Entity\Blacklist;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;

class BlacklistCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Blacklist::class;
    }


    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('users[0].firstname', 'Prénom')->hideOnForm(),
            TextField::new('users[0].lastname', 'Nom')->hideOnForm(),
            TextField::new('users[0].email', 'Email')->hideOnForm(),
            TextField::new('violation', 'Motif'),
            DateField::new('createdAt', 'Date de bannissement'),
            AssociationField::new('users', 'utilisateur')->hideOnIndex(),
        ];
    }
}
