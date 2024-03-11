<?php

namespace App\Repository;

use App\Entity\Blason;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Blason>
 *
 * @method Blason|null find($id, $lockMode = null, $lockVersion = null)
 * @method Blason|null findOneBy(array $criteria, array $orderBy = null)
 * @method Blason[]    findAll()
 * @method Blason[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BlasonRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Blason::class);
    }

    //    /**
    //     * @return Blason[] Returns an array of Blason objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('b')
    //            ->andWhere('b.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('b.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Blason
    //    {
    //        return $this->createQueryBuilder('b')
    //            ->andWhere('b.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
