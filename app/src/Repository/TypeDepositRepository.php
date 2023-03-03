<?php

namespace App\Repository;

use App\Entity\TypeDeposit;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method TypeDeposit|null find($id, $lockMode = null, $lockVersion = null)
 * @method TypeDeposit|null findOneBy(array $criteria, array $orderBy = null)
 * @method TypeDeposit[]    findAll()
 * @method TypeDeposit[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TypeDepositRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TypeDeposit::class);
    }

    // /**
    //  * @return TypeDeposit[] Returns an array of TypeDeposit objects
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
    public function findOneBySomeField($value): ?TypeDeposit
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
