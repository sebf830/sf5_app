<?php

namespace App\Controller\Admin;

use App\Entity\Annonce;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class AnnonceCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Annonce::class;
    }


    public function configureFields(string $pageName): iterable
    {
        return [
            // IdField::new('id')
            ImageField::new('image', 'Photo')
                ->setUploadDir('public/uploads')
                ->setBasePath("uploads")
                ->setRequired(true),
            TextField::new('title', 'Titre'),
            TextEditorField::new('description'),
            TextField::new('numero', 'Numéro'),
            TextField::new('location', 'Ville'),
            DateField::new('created_at', 'Création'),
            DateField::new('updated_at', 'dernière modification'),
            DateField::new('lost_at', 'Perdu'),
            DateField::new('found_at', 'Trouvé'),
        ];
    }
}
