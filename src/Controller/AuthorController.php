<?php

// je créé un namespace qui correspond au chemin vers cette classe
// (en gardant en tête que "App" = "src")
// et qui permet à Symfony d'"autoloader" ma classe
// sans que j'ai besoin de faire d'import ou de require à la main
namespace App\Controller;

// je fais un "use" vers le namespace (qui correspond au chemin) de la classe "Route"
// ça correspond à un import ou un require en PHP
// pour pouvoir utiliser cette classe dans mon code
use App\Entity\Book;
use App\Repository\AuthorRepository;
use App\Repository\BookRepository;
use Doctrine\ORM\EntityManagerInterface;
use phpDocumentor\Reflection\DocBlock\Tags\Author;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

// je créé ma classe HomeController et je la nomme de la même manière que mon fichier
class AuthorController extends AbstractController
{
    /**
     * @Route("/accueil/authors", name="authors")
     */
    public function authors(AuthorRepository $authorRepository)
    {
        $authors = $authorRepository->findAll();

        return $this->render('index.authors.html.twig', [
            'authors' => $authors
        ]);

    }
    //je fais une nouvelle route avec une wild card
    /**
     * @Route("/author/{id}", name="author")
     */
    public function author(AuthorRepository $authorRepository, $id)
    {
        // récupérer le repository des Books, car c'est la classe Repository
        // qui me permet de sélectionner les livres en bdd
        $author = $authorRepository->find($id);

        return $this->render('author.html.twig', [
            'author' => $author
        ]);

    }
    /**
     * @Route("/author/task/insert", name="author_insert")
     */

    public function insertAuthor(EntityManagerInterface $entityManager)
    {

        // Pour créer un enregistrement de Book en bdd, j'utilise une instance de l'entité Book
        // Doctrine va faire le lien et transformer mon entité en nouvel enregistrement
        $author = new Author();

        // j'utilise les setters de mon entité pour donner les valeurs à chaque propriétés (donc à chaque
        // colonne en BDD)
        $author->setName('PAYTON');
        $author->setFirstName('Gary');
        $author->setBirthDate(new \DateTime('1909-08-29'));
        $author->setDeathDate(new \DateTime('2000-01-01'));
        $author->setBiography('Né en Russie du Sud');

        // j'utilise l'EntityManager avec la méthode persist pour sauvegarder mon entité (similaire à un commit
        // Attention ça n'enregistre pas encore en BDD
        $entityManager->persist($author);

        // j'utilise la méthode flush pour enregistrer en bdd (execute la requête SQL)
        $entityManager->flush();

        return new Response('auteur enregistré');

    }


    /**
     * @Route("/author/delete/{id}", name="author_delete")
     */
    public function deleteAuthor(
        AuthorRepository $authorRepository,
        EntityManagerInterface $entityManager,
        $id)

    {

        // Avant de supprimer un élément en bdd, je récupère cet élément
        // qui sera une entité
        // et je le stocke dans une variable
        $author = $authorRepository->find($id);

        // j'utilise l'entityManager pour supprimer mon entité
        $entityManager->remove($author);

        // je "valide" la suppression en bdd
        $entityManager->flush();

        return new Response('l/auteur a bien été supprimé');

    }
    /**
     * @route("author/update/{id}", name="author_update")
     */
    public function updateAuthor(AuthorRepository $authorRepository, $id, EntityManagerInterface $entityManager)
    {
        //recuperer un livre en bdd
        $author = $authorRepository->find($id);
        //avec l'entite recupéré on utilise les setteur pour modifier les champs souhaiter
        $author->setName('nom modifié');

        //on reenregistre le livre
        $entityManager->persist($author);
        $entityManager->flush();

        return new Response('l/auteur a bien été modifié');
    }

    /**
     * @Route("/search", name="author_search")
     */
    public  function searchByAuthor(AuthorRepository $authorRepository, Request $request)
    {
        $word = $request->get('word');

        $authors = $authorRepository->getByWordInBiography($word);

        return $this->render('search.author.html.twig', [
            'authors' => $authors,
            'word' => $word

        ]);
    }




}