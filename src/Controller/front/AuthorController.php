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

        return $this->render('front/author/index.authors.html.twig', [
            'authors' => $authors
        ]);

    }
    //je fais une nouvelle route avec une wild card
    /**
     * @Route("author/{id}", name="author")
     */
    public function author(AuthorRepository $authorRepository, $id)
    {
        // récupérer le repository des Books, car c'est la classe Repository
        // qui me permet de sélectionner les livres en bdd
        $author = $authorRepository->find($id);

        return $this->render('front/author/author.html.twig', [
            'author' => $author
        ]);

    }


    /**
     * @Route("/search", name="author_search")
     */
    public  function searchByAuthor(AuthorRepository $authorRepository, Request $request)
    {
        $word = $request->get('word');

        $authors = $authorRepository->getByWordInBiography($word);

        return $this->render('front/author/search.author.html.twig', [
            'authors' => $authors,
            'word' => $word

        ]);
    }




}