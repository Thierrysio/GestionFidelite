<?php

namespace App\Repository;

use App\Entity\Utiliser;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Utiliser>
 *
 * @method Utiliser|null find($id, $lockMode = null, $lockVersion = null)
 * @method Utiliser|null findOneBy(array $criteria, array $orderBy = null)
 * @method Utiliser[]    findAll()
 * @method Utiliser[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UtiliserRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Utiliser::class);
    }

    //    /**
    //     * @return Utiliser[] Returns an array of Utiliser objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('u')
    //            ->andWhere('u.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('u.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Utiliser
    //    {
    //        return $this->createQueryBuilder('u')
    //            ->andWhere('u.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
