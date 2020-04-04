<?php

namespace App\Form;

use App\Entity\Author;
use App\Entity\Book;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BookType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            //INSERTION IMAGE
            ->add('bookCover', FileType::class, [
                'label' => 'BookCover',

                // unmapped means that this field is not associated to any entity property
                'mapped' => false,

                // make it optional so you don't have to re-upload the PDF file
                // every time you edit the Product details
                'required' => false,
            ])
            //INSERTION IMAGE
            ->add('title')
            ->add('resume')
            ->add('nbPages')
            // on créé un champs author, qui permettra de choisir un auteur pour un livre
            // contrairement aux autres champs, author est une relation vers une autre entité
            // donc il faut utiliser en type de champs "EntityType"
            ->add('author', EntityType::class, [
                // je choisis ici vers quelle entité on relie notre champs
                'class' => Author::class,
                // je choisis ici la propriété de l'entité Author à afficher dans l'input
                'choice_label' => 'name'
            ])
            //permet de creer le bouton submit
            ->add('submit', SubmitType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Book::class,
        ]);
    }
}
