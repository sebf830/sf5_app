<?php

namespace App\Controller\Admin;

use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class UserCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return User::class;
    }


    public function configureFields(string $pageName): iterable
    {
        return [
            ImageField::new('avatar', 'Photo')
                ->setUploadDir('public/uploads')
                ->setBasePath("uploads")
                ->setRequired(true),
            TextField::new('firstname', 'prénom'),
            TextField::new('lastname', 'nom'),
            TextField::new('email', 'adresse mail'),
            TextField::new('city', 'ville'),
            TextField::new('phone', 'telephone'),
        ];
    }
}
