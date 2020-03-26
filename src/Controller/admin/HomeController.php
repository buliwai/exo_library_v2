<?php

// je créé un namespace qui correspond au chemin vers cette classe
// (en gardant en tête que "App" = "src")
// et qui permet à Symfony d'"autoloader" ma classe
// sans que j'ai besoin de faire d'import ou de require à la main
namespace App\Controller\admin;

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
     * @Route("admin/accueil", name="admin_accueil")
     */
    public function accueil(BookRepository $bookRepository)
    {
        $books = $bookRepository->findAll();

        return $this->render('admin/index.html.twig', [
            'books' => $books
        ]);

    }
    /**
     * @Route("admin/book/{id}", name="admin_book")
     */
    public function book(BookRepository $bookRepository, $id)
    {
        $book = $bookRepository->find($id);

        return $this->render('admin/book.html.twig', [
            'book' => $book
        ]);

    }
    /**
     * @Route("admin/book/task/insert", name="admin_book_insert")
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
     * @Route("admin/book/delete/{id}", name="admin_book_delete")
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
     * @route("admin/book/update/{id}", name="admin_book_update")
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
     * @Route("admin/search", name="admin_book_search")
     */
    Public function searchByResume(BookRepository $bookRepository, Request $request)
    {
        $word = $request->query->get('word');
        $books = $bookRepository->getByWordInResume($word);

        return $this->render('admin/search.html.twig',[
            'books' => $books,
            'word' => $word
        ]);
    }




}