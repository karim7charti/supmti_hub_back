<?php

namespace App\Repository;

use App\Entity\Like;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\ORM\Query\ResultSetMapping;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Like|null find($id, $lockMode = null, $lockVersion = null)
 * @method Like|null findOneBy(array $criteria, array $orderBy = null)
 * @method Like[]    findAll()
 * @method Like[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LikeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Like::class);
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(Like $entity, bool $flush = true): void
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
    public function remove(Like $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    // /**
    //  * @return Like[] Returns an array of Like objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('l')
            ->andWhere('l.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('l.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Like
    {
        return $this->createQueryBuilder('l')
            ->andWhere('l.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */

    public function did_like($user_id,$activity_id):bool{
        $entityManager = $this->getEntityManager();
        $rsm = new ResultSetMapping();

        $rsm->addScalarResult('id','id');
        $result=$entityManager->createNativeQuery('
        select l.id from `like` l where l.activity_id=:idAc and l.user_id=:uId
        ',$rsm)
            ->setParameter("idAc",$activity_id)
            ->setParameter("uId",$user_id)
            ->getOneOrNullResult();
        if($result==null)
            return  false;
        return true;

    }
    public function count_likes($activity_id){
        $entityManager = $this->getEntityManager();
        $rsm = new ResultSetMapping();

        $rsm->addScalarResult('cmpt','cmpt');
        $result=$entityManager->createNativeQuery('
        select count(l.id) cmpt from `like` l where l.activity_id=:idAc
        ',$rsm)
            ->setParameter("idAc",$activity_id)
            ->getResult();
        return $result[0]['cmpt'];
    }
}
