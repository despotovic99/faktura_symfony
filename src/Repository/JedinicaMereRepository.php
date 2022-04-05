<?php

namespace App\Repository;

use App\Entity\JedinicaMere;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method JedinicaMere|null find($id, $lockMode = null, $lockVersion = null)
 * @method JedinicaMere|null findOneBy(array $criteria, array $orderBy = null)
 * @method JedinicaMere[]    findAll()
 * @method JedinicaMere[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class JedinicaMereRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, JedinicaMere::class);
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(JedinicaMere $entity, bool $flush = true): void
    {
        $this->_em->persist($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function remove(JedinicaMere $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    // /**
    //  * @return JedinicaMere[] Returns an array of JedinicaMere objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('j')
            ->andWhere('j.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('j.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?JedinicaMere
    {
        return $this->createQueryBuilder('j')
            ->andWhere('j.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
