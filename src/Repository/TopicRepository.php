<?php

namespace App\Repository;

use App\Entity\Link;
use App\Entity\Topic;
use DateTime;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Topic|null find($id, $lockMode = null, $lockVersion = null)
 * @method Topic|null findOneBy(array $criteria, array $orderBy = null)
 * @method Topic[]    findAll()
 * @method Topic[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TopicRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Topic::class);
    }

    /**
     * @return Topic[]
     */
    public function getTopicsFromLastWeek()
    {
        $minDate = new \DateTime('-7 days');

        return $this->createQueryBuilder('t')
            // ->select('t', 'COUNT(l)')
            // ->andWhere('t.timestamp = :val')
            // ->setParameter('val', new \DateTime('-7 days'))
            // ->from(Link::class, 'l')
            // ->leftJoin('l.topic', 'ti')
            // ->where('ti = t.id')
            // ->groupBy('t.id')
            // // ->setParameter('topic', 't.id')
            // ->orderBy('COUNT(l)', 'ASC')
            // ->getQuery()
            // ->getResult()
            ->andWhere('t.timestamp > :val')
            ->setParameter('val', new DateTime('-7 days'))
            ->orderBy('t.id', 'ASC')
            // ->setMaxResults(10)
            ->getQuery()
            ->getResult()
            ;
    }

    // /**
    //  * @return Topic[] Returns an array of Topic objects
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
    public function findOneBySomeField($value): ?Topic
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
