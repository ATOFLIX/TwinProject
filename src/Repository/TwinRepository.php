<?php

namespace App\Repository;

use App\Entity\Twin;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Twin|null find($id, $lockMode = null, $lockVersion = null)
 * @method Twin|null findOneBy(array $criteria, array $orderBy = null)
 * @method Twin[]    findAll()
 * @method Twin[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TwinRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Twin::class);
    }

    // /**
    //  * @return Twin[] Returns an array of Twin objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('t.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Twin
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
