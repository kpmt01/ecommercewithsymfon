<?php

namespace App\Repository;

use App\Entity\Imagesca;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Imagesca|null find($id, $lockMode = null, $lockVersion = null)
 * @method Imagesca|null findOneBy(array $criteria, array $orderBy = null)
 * @method Imagesca[]    findAll()
 * @method Imagesca[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ImagescaRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Imagesca::class);
    }

//    /**
//     * @return Imagesca[] Returns an array of Imagesca objects
//     */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('i')
            ->andWhere('i.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('i.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Imagesca
    {
        return $this->createQueryBuilder('i')
            ->andWhere('i.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
