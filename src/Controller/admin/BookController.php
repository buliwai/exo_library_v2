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
use App\Form\BookType;
use App\Repository\BookRepository;
use App\Repository\AuthorRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

// je créé ma classe HomeController et je la nomme de la même manière que mon fichier
class BookController extends AbstractController
{
    /**
     * @Route("admin/accueil", name="admin_accueil")
     */
    public function accueil(BookRepository $bookRepository)
    {
        $books = $bookRepository->findAll();

        return $this->render('admin/book/index.html.twig', [
            'books' => $books
        ]);

    }
    /**
     * @Route("admin/book/{id}", name="admin_book")
     */
    public function book(BookRepository $bookRepository, $id)
    {
        $book = $bookRepository->find($id);

        return $this->render('admin/book/book.html.twig', [
            'book' => $book
        ]);

    }

    /**
     * @Route("admin/book/task/insert", name="admin_book_insert")
     */
    public function insertBook(Request $request, EntityManagerInterface $entityManager, SluggerInterface $slugger)
    {

        // 1 : En ligne de commandes, tapez : php bin/console make:form afin de
        // créer le BookType (le template du formulaire)
        // 2 : dans le contrôleur, générer le formulaire avec $this->createView
        // 3 : afficher dans Twig le formulaire avec la fonction form ({{ form(formBook) }})
        // J'ai généré avec symfony un template de formulaire (BookType)
        // qui contient déjà tous les inputs à créer en HTML
        // Je vais pouvoir utiliser ce gabarit de formulaire pour générer mon
        // formulaire HTML (donc tous mes champs inputs etc)

        // Je créé un nouveau livre, pour le lier à mon formulaire
        $book = new Book();

        // je créé mon formulaire, et je le lie à mon nouveau livre
        $formBook = $this->createForm(BookType::class, $book);

        // je demande à mon formulaire $formBook de gérer les données
        // de la requête POST
        $formBook->handleRequest($request);

        // si le formulaire a été envoyé, et que les données sont valides
        if ($formBook->isSubmitted() && $formBook->isValid()) {
            //INSERTION IMAGE
            $bookCover = $formBook->get('bookCover')->getData();

            // this condition is needed because the 'brochure' field is not required
            // so the PDF file must be processed only when a file is uploaded
            if ($bookCover) {
                $originalFilename = pathinfo($bookCover->getClientOriginalName(), PATHINFO_FILENAME);
                // this is needed to safely include the file name as part of the URL
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$bookCover->guessExtension();

                // Move the file to the directory where brochures are stored
                try {
                    $bookCover->move(
                        $this->getParameter('brochures_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                }
                $book->setBookCover($newFilename);

                }
            //INSERTION IMAGE
            // je persiste le book
            $entityManager->persist($book);
            $entityManager->flush();

            // j'ajoute un message "flash"
            $this->addFlash('success', 'Votre livre a bien été créé !');
        }

        return $this->render('admin/book/insert.book.html.twig', [
            'formBook' => $formBook->createView()
        ]);

    }

    /* VERSION 1 EN DUR
      public function insertBook(
        EntityManagerInterface $entityManager,
        Request $request,
        AuthorRepository $authorRepository
    )
    {

        // Pour créer un enregistrement de Book en bdd, j'utilise une instance de l'entité Book
        // Doctrine va faire le lien et transformer mon entité en nouvel enregistrement
        $book = new Book();

        $title = $request->query->get('title');

        // j'utilise les setters de mon entité pour donner les valeurs à chaque propriétés (donc à chaque
        // colonne en BDD)
        $book->setTitle($title);

        // je récupère un auteur en BDD grâce à l'authorRepository
        $author = $authorRepository->find(8);

        // Je viens relier l'auteur récupéré au livre que je suis en train de créer
        $book->setAuthor($author);

        $book->setNbPages(200);
        $book->setResume('Un groupe de ....');

        // j'utilise l'EntityManager avec la méthode persist pour sauvegarder mon entité (similaire à un commit
        // Attention ça n'enregistre pas encore en BDD
        $entityManager->persist($book);

        // j'utilise la méthode flush pour enregistrer en bdd (execute la requête SQL)
        $entityManager->flush();

        return new Response('le livre a bien été ajouté');

    }*/

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
    public function updateBook(BookRepository $bookRepository, $id, Request $request, EntityManagerInterface $entityManager)
    {
        //recuperer un livre en bdd
        $book = $bookRepository->find($id);
        //je crée un formulaire qui est relié a mon nouveau livre
        $formBook = $this->createForm(BookType::class, $book);

        $formBook->handleRequest($request);
        //je demande a mon formulaire $formBook de gerer les données
        //de ma requete post
        if($formBook->isSubmitted() && $formBook->isValid()){
            //je persist le book
            $entityManager->persist($book);
            $entityManager->flush();
        }
        return $this->render('admin/book/modify.book.html.twig', [
            'formBook' => $formBook->createView()
        ]);
    }
    /*VERSION 1
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
    */
    /**
     * @Route("admin/search", name="admin_book_search")
     */
    Public function searchByResume(BookRepository $bookRepository, Request $request)
    {
        $word = $request->query->get('word');
        $books = $bookRepository->getByWordInResume($word);

        return $this->render('admin/book/search.html.twig',[
            'books' => $books,
            'word' => $word
        ]);
    }




}