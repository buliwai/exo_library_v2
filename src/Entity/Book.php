<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

use phpDocumentor\Reflection\Types\Integer;


/**
 * @ORM\Entity(repositoryClass="App\Repository\BookRepository")
 */
class Book
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;






    /**
     * @ORM\Column(type="string", length=255)
     */

    private $title;




    /**
     * @ORM\Column(type="string", length=255)
     */

    private $resume;


    /**
     * @ORM\Column(type="integer")
     */
    private $nbPages;
/*ceci permet de creer foreign key. manytoone veut dire que plusieurs livres peuvent avoir un auteur*/
/*il faut également supprimer get author et set author et author string car en doublon*/

    /**
     * @ORM\ManyToOne(targetEntity="Author", inversedBy="books")
     */
    private $author;

    public function getId(): ?int
    {
        return $this->id;
    }



    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;


        return $this;
    }



    public function getResume(): ?string
    {
        return $this->resume;
    }

    public function setResume(string $resume): self
    {
        $this->resume = $resume;


        return $this;
    }




    public function getNbPages(): ?Integer

    {
        return $this->nbPages;
    }


    public function setNbPages(string $nbPages): self

    {
        $this->nbPages = $nbPages;

        return $this;
    }
/*pour obtenir ceci faire click droit generate getter and setter et selectionner author*/
    /**
     * @return mixed
     */
    public function getAuthor()
    {
        return $this->author;
    }

    /**
     * @param mixed $author
     */
    public function setAuthor($author): void
    {
        $this->author = $author;
    }





}
