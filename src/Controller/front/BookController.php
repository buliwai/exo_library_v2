<?php

// je créé un namespace qui correspond au chemin vers cette classe
// (en gardant en tête que "App" = "src")
// et qui permet à Symfony d'"autoloader" ma classe
// sans que j'ai besoin de faire d'import ou de require à la main
namespace App\Controller\front;

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
class BookController extends AbstractController
{
    /**
     * @Route("accueil", name="accueil")
     */
    public function accueil(BookRepository $bookRepository)
    {
        $books = $bookRepository->findAll();

        return $this->render('front/book/index.html.twig', [
            'books' => $books
        ]);

    }
    /**
     * @Route("/book/{id}", name="book")
     */
    public function book(BookRepository $bookRepository, $id)
    {
        $book = $bookRepository->find($id);

        return $this->render('front/book/book.html.twig', [
            'book' => $book
        ]);

    }

    /**
     * @Route("/search", name="book_search")
     */
    Public function searchByResume(BookRepository $bookRepository, Request $request)
    {
        $word = $request->query->get('word');
        $books = $bookRepository->getByWordInResume($word);

        return $this->render('front/book/search.html.twig',[
            'books' => $books,
            'word' => $word
        ]);
    }




}