<?php

namespace App\Repository;

use App\Entity\StavkaFakture;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method StavkaFakture|null find($id, $lockMode = null, $lockVersion = null)
 * @method StavkaFakture|null findOneBy(array $criteria, array $orderBy = null)
 * @method StavkaFakture[]    findAll()
 * @method StavkaFakture[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class StavkaFaktureRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, StavkaFakture::class);
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(StavkaFakture $entity, bool $flush = true): void
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
    public function remove(StavkaFakture $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    // /**
    //  * @return StavkaFakture[] Returns an array of StavkaFakture objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('s.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?StavkaFakture
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
