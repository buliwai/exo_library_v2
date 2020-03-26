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
use App\Repository\BookRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

// je créé ma classe HomeController et je la nomme de la même manière que mon fichier
class HomeController extends AbstractController
{
    /**
     * @Route("/accueil", name="accueil")
     */
    public function accueil(BookRepository $bookRepository)
    {
        $books = $bookRepository->findAll();

        return $this->render('index.html.twig', [
            'books' => $books
        ]);

    }
    /**
     * @Route("/book/{id}", name="book")
     */
    public function book(BookRepository $bookRepository, $id)
    {
        $book = $bookRepository->find($id);

        return $this->render('book.html.twig', [
            'book' => $book
        ]);

    }
    /**
     * @Route("/book/task/insert", name="book_insert")
     */

    public function insertBook(EntityManagerInterface $entityManager)
    {

        // Pour créer un enregistrement de Book en bdd, j'utilise une instance de l'entité Book
        // Doctrine va faire le lien et transformer mon entité en nouvel enregistrement
        $book = new Book();

        // j'utilise les setters de mon entité pour donner les valeurs à chaque propriétés (donc à chaque
        // colonne en BDD)
        $book->setTitle('UN ETE AU JAPON');
        $book->setAuthor('Jeremy LIN');
        $book->setResume('Livre retracant la vie de...');
        $book->setNbPages(180);

        // j'utilise l'EntityManager avec la méthode persist pour sauvegarder mon entité (similaire à un commit
        // Attention ça n'enregistre pas encore en BDD
        $entityManager->persist($book);

        // j'utilise la méthode flush pour enregistrer en bdd (execute la requête SQL)
        $entityManager->flush();

        return new Response('livre enregistré');

    }
    /**
     * @Route("/book/delete/{id}", name="book_delete")
     */
    public function deleteBook(
        BookRepository $bookRepository,
        EntityManagerInterface $entityManager,
        $id)

    {

        // Avant de supprimer un élément en bdd, je récupère cet élément
        // qui sera une entité
        // et je le stocke dans une variable
        $book = $bookRepository->find($id);

        // j'utilise l'entityManager pour supprimer mon entité
        $entityManager->remove($book);

        // je "valide" la suppression en bdd
        $entityManager->flush();

        return new Response('le livre a bien été supprimé');

    }
    /**
     * @route("book/update/{id}", name="book_update")
     */
    public function updateBook(BookRepository $bookRepository, $id, EntityManagerInterface $entityManager)
    {
        //recuperer un livre en bdd
        $book = $bookRepository->find($id);
        //avec l'entite recupéré on utilise les setteur pour modifier les champs souhaiter
        $book->setTitle('titre modifié');

        //on reenregistre le livre
        $entityManager->persist($book);
        $entityManager->flush();

        return new Response('le livre a bien été modifié');
    }
    /**
     * @Route("/search", name="book_search")
     */
    Public function searchByResume(BookRepository $bookRepository, Request $request)
    {
        $word = $request->query->get('word');
        $books = $bookRepository->getByWordInResume($word);

        return $this->render('search.html.twig',[
            'books' => $books,
            'word' => $word
        ]);
    }




}