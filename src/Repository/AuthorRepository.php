<?php

namespace App\Repository;

use App\Entity\Author;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Author|null find($id, $lockMode = null, $lockVersion = null)
 * @method Author|null findOneBy(array $criteria, array $orderBy = null)
 * @method Author[]    findAll()
 * @method Author[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AuthorRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Author::class);
    }


    public function getByWordInBiography($word)
    {
        //je defini le mot a rechercher dans la colonne résume


        //jutilise le queryBuilder qui me permet de créer mes requete select en base de donnée
        //je place en parametre une letttre ou un mot qui fera office d'alias pour ma table
        $queryBuilder = $this->createQueryBuilder('author');
        //je défini une clause WHERE avec un like dans la column résume
        $query = $queryBuilder->select('author')
            ->where('author.biography LIKE :word')
            //j'utilise set parameter pour que la variable soit sécurisé
            ->setParameter('word', '%'.$word.'%')
            ->getQuery();

        //j'execute puis je retourne le résultat
        $results = $query->getResult();
        return $results;
    }
    // /**
    //  * @return Author[] Returns an array of Author objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('a.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Author
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
