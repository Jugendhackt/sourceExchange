<?php

namespace App\Repository;

use App\Entity\TopicUser;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method TopicUser|null find($id, $lockMode = null, $lockVersion = null)
 * @method TopicUser|null findOneBy(array $criteria, array $orderBy = null)
 * @method TopicUser[]    findAll()
 * @method TopicUser[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TopicUserRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TopicUser::class);
    }

    // /**
    //  * @return TopicUser[] Returns an array of TopicUser objects
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
    public function findOneBySomeField($value): ?TopicUser
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
