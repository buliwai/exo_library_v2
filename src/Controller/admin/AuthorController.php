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
use App\Form\AuthorType;
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
     * @Route("admin/accueil/authors", name="admin_authors")
     */
    public function authors(AuthorRepository $authorRepository)
    {
        $authors = $authorRepository->findAll();

        return $this->render('admin/author/index.authors.html.twig', [
            'authors' => $authors
        ]);

    }
    //je fais une nouvelle route avec une wild card
    /**
     * @Route("admin/author/{id}", name="admin_author")
     */
    public function author(AuthorRepository $authorRepository, $id)
    {
        // récupérer le repository des Books, car c'est la classe Repository
        // qui me permet de sélectionner les livres en bdd
        $author = $authorRepository->find($id);

        return $this->render('admin/author/author.html.twig', [
            'author' => $author
        ]);

    }
    /**
     * @Route("admin/author/task/insert", name="admin_author_insert")
     */

    public function insertAuthor(Request $request, EntityManagerInterface $entityManager)
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
        $author = new \App\Entity\Author();

        // je créé mon formulaire, et je le lie à mon nouveau livre
        $formAuthor = $this->createForm(AuthorType::class, $author);

        // je demande à mon formulaire $formBook de gérer les données
        // de la requête POST
        $formAuthor->handleRequest($request);

        // si le formulaire a été envoyé, et que les données sont valides
        if ($formAuthor->isSubmitted() && $formAuthor->isValid()) {
            // je persiste le book
            $entityManager->persist($author);
            $entityManager->flush();

        // j'ajoute un message "flash"
        $this->addFlash('success', 'Votre auteur a bien été créé !');
        }
        return $this->render('admin/author/insert.author.html.twig', [
            'formAuthor' => $formAuthor->createView()
        ]);

    }
    /*VERSION1
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
*/

    /**
     * @Route("admin/author/delete/{id}", name="admin_author_delete")
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
     * @route("admin/author/update/{id}", name="admin_author_update")
     */
    public function updateAuthor(AuthorRepository $authorRepository, $id, EntityManagerInterface $entityManager, Request $request)
    {
        //recuperer un l'auteur en bdd
        $author = $authorRepository->find($id);
        $formAuthor = $this->createForm(AuthorType::class,$author);
        $formAuthor->handleRequest($request);
        //on reenregistre l'auteur
        if($formAuthor->isSubmitted() && $formAuthor->isValid()){
            //je persist et flush
            //un peu comme commit et push
            $entityManager->persist($author);
            $entityManager->flush();
        }

        return $this->render('admin/author/modify.author.html.twig',[
            'formAuthor' =>$formAuthor->createView()]);
    }
    /*VERSION1
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
*/
    /**
     * @Route("admin/search", name="admin_author_search")
     */
    public  function searchByAuthor(AuthorRepository $authorRepository, Request $request)
    {
        $word = $request->get('word');

        $authors = $authorRepository->getByWordInBiography($word);

        return $this->render('admin/author/search.author.html.twig', [
            'authors' => $authors,
            'word' => $word

        ]);
    }




}