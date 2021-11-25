<?php

namespace App\Controller\Admin;

use App\Entity\Article;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;

class ArticleCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Article::class;
    }


    public function configureFields(string $pageName): iterable
    {
        return [
            AssociationField::new('images', 'photo'),
            TextField::new('title', 'Titre'),
            TextField::new('slug', 'Slug'),
            TextEditorField::new('content', 'Contenu'),
            DateField::new('date', 'Création'),
            DateField::new('updated_at', 'Modifié le'),
            AssociationField::new('category'),
            BooleanField::new('publicationStatus', 'publié')
        ];
    }
}
