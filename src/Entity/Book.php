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

    private $author;




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

    public function getId(): ?int
    {
        return $this->id;
    }


    public function getAuthor(): ?string
    {
        return $this->author;
    }

    public function setAuthor(string $author): self
    {
        $this->author = $author;
    }
    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTittle(string $title): self
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
}
