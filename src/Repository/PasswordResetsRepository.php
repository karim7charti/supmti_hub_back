<?php

namespace App\Repository;

use App\Entity\PasswordResets;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method PasswordResets|null find($id, $lockMode = null, $lockVersion = null)
 * @method PasswordResets|null findOneBy(array $criteria, array $orderBy = null)
 * @method PasswordResets[]    findAll()
 * @method PasswordResets[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PasswordResetsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PasswordResets::class);
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(PasswordResets $entity, bool $flush = true): void
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
    public function remove(PasswordResets $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    // /**
    //  * @return PasswordResets[] Returns an array of PasswordResets objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */


    public function findOneByEmailAndToken($email,$token): ?PasswordResets
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.email = :email')
            ->setParameter('email', $email)
            ->andWhere('p.token = :token')
            ->setParameter('token',$token)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

}
