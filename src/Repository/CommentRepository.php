<?php

namespace App\Repository;

use App\Entity\Comment;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\ORM\Query\ResultSetMapping;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Comment|null find($id, $lockMode = null, $lockVersion = null)
 * @method Comment|null findOneBy(array $criteria, array $orderBy = null)
 * @method Comment[]    findAll()
 * @method Comment[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CommentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Comment::class);
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(Comment $entity, bool $flush = true): void
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
    public function remove(Comment $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    // /**
    //  * @return Comment[] Returns an array of Comment objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('c.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    public function getActivityComments(int $activity_id,int $maxResult,int $pageNum,int $user_id){
        $entityManager = $this->getEntityManager();
        $rsm = new ResultSetMapping();

        $rsm->addScalarResult('id','id');
        $rsm->addScalarResult('user_id','user_id');
        $rsm->addScalarResult('profile_image_path','profile_image_path');
        $rsm->addScalarResult('first_name','first_name');
        $rsm->addScalarResult('last_name','last_name');
        $rsm->addScalarResult('roles','roles');
        $rsm->addScalarResult('activity_id','activity_id');
        $rsm->addScalarResult('comment_on_id','comment_on_id');
        $rsm->addScalarResult('body','body');
        $rsm->addScalarResult('type','type');
        $rsm->addScalarResult('created_at','created_at');
        $rsm->addScalarResult('count_comments','count_comments');
        $rsm->addScalarResult('count_likes','count_likes');
        $rsm->addScalarResult('liked','liked');
        $result=$entityManager->createNativeQuery('
        select c.id,c.comment_on_id,c.body,c.type,c.activity_id,
               DATE_FORMAT(c.created_at,"%H:%i %d %M %Y") as created_at
             ,u.id as user_id,u.profile_image_path,u.last_name,u.first_name,u.roles ,
               IFNULL(usr_did_like.cmpt,0) as liked,
               IFNULL(usr_likes.cmpt,0) as count_likes from comment c 
                   inner join user u on c.user_id=u.id 
                   LEFT JOIN ( SELECT `like`.activity_id,COUNT(`like`.id) cmpt FROM `like` GROUP BY `like`.activity_id )
                       AS usr_likes ON c.activity_id=usr_likes.activity_id
         LEFT JOIN
( SELECT `like`.activity_id,COUNT(`like`.id) cmpt FROM `like` where `like`.`user_id`=:user_id GROUP BY `like`.activity_id )
    AS usr_did_like ON c.activity_id=usr_did_like.activity_id
        WHERE c.comment_on_id=:id
        order by c.created_at desc
         limit :maxResult offset :pageNum
        ',$rsm)
            ->setParameter("id",$activity_id)
            ->setParameter("user_id",$user_id)
            ->setParameter("maxResult",$maxResult)
            ->setParameter("pageNum",($pageNum-1)*$maxResult)->getResult();
        return $result;


    }

    /*
    public function findOneBySomeField($value): ?Comment
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
