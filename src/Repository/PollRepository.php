<?php

namespace App\Repository;

use App\Entity\Poll;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\ORM\Query\ResultSetMapping;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Poll|null find($id, $lockMode = null, $lockVersion = null)
 * @method Poll|null findOneBy(array $criteria, array $orderBy = null)
 * @method Poll[]    findAll()
 * @method Poll[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PollRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Poll::class);
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(Poll $entity, bool $flush = true): void
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
    public function remove(Poll $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    // /**
    //  * @return Poll[] Returns an array of Poll objects
    //  */

    public function findByExampleField()
    {
        return $this->createQueryBuilder('p')
            ->select(['p.id','p.question'])
            ->getQuery()
            ->getResult()
        ;
    }

    public function getOnePollById(int $activity_id)
    {
        $entityManager = $this->getEntityManager();
        $rsm = new ResultSetMapping();

        $rsm->addScalarResult('id','id');
        $rsm->addScalarResult('user_id','user_id');
        $rsm->addScalarResult('profile_image_path','profile_image_path');
        $rsm->addScalarResult('first_name','first_name');
        $rsm->addScalarResult('last_name','last_name');
        $rsm->addScalarResult('roles','roles');
        $rsm->addScalarResult('activity_id','activity_id');
        $rsm->addScalarResult('question','question');
        $rsm->addScalarResult('created_at','created_at');
        $rsm->addScalarResult('count_comments','count_comments');
        $rsm->addScalarResult('count_likes','count_likes');
        $result=$entityManager->createNativeQuery('
        select p.id,p.question,p.activity_id,DATE_FORMAT(p.created_at,"%H:%i %d %M %Y") as created_at
        ,u.id as user_id,u.profile_image_path,u.last_name,u.first_name,u.roles
        ,IFNULL(usr_comments.cmpt,0) as count_comments ,IFNULL(usr_likes.cmpt,0) as count_likes
        from poll p inner join user u on p.user_id=u.id
        LEFT JOIN
            (SELECT comment.comment_on_id,COUNT(comment.id) cmpt FROM comment GROUP BY comment.comment_on_id ) 
                AS usr_comments ON p.activity_id=usr_comments.comment_on_id 
            LEFT JOIN 
            ( SELECT `like`.activity_id,COUNT(`like`.id) cmpt FROM `like` GROUP BY `like`.activity_id ) 
                AS usr_likes ON p.activity_id=usr_likes.activity_id
        where p.activity_id=:id 
        order by p.created_at desc 
        ',$rsm)
            ->setParameter("id",$activity_id)
            ->getResult();


        return $result;
    }
    public function getUserPolls($id,$maxResult,$pageNum)
    {
        $entityManager = $this->getEntityManager();
        $rsm = new ResultSetMapping();

        $rsm->addScalarResult('id','id');
        $rsm->addScalarResult('question','question');
        $rsm->addScalarResult('activity_id','activity_id');
        $rsm->addScalarResult('created_at','created_at');
        $rsm->addScalarResult('count_comments','count_comments');
        $rsm->addScalarResult('count_likes','count_likes');

        $result=$entityManager->createNativeQuery('
        select p.id,p.question,p.activity_id,DATE_FORMAT(p.created_at,"%H:%i %d %M %Y") as created_at
        ,IFNULL(usr_comments.cmpt,0) as count_comments ,IFNULL(usr_likes.cmpt,0) as count_likes
        from poll p
            LEFT JOIN
            (SELECT comment.comment_on_id,COUNT(comment.id) cmpt FROM comment GROUP BY comment.comment_on_id ) 
                AS usr_comments ON p.activity_id=usr_comments.comment_on_id 
            LEFT JOIN 
            ( SELECT `like`.activity_id,COUNT(`like`.id) cmpt FROM `like` GROUP BY `like`.activity_id ) 
                AS usr_likes ON p.activity_id=usr_likes.activity_id
        where p.user_id=:id order by p.created_at desc limit :maxResult offset :pageNum
        ',$rsm)
            ->setParameter("id",$id)
            ->setParameter("maxResult",$maxResult)
            ->setParameter("pageNum",($pageNum-1)*$maxResult)
            ->getResult();

        return $result;
    }

    public function getAllPolls($maxResult,$pageNum){
        $entityManager = $this->getEntityManager();
        $rsm = new ResultSetMapping();

        $rsm->addScalarResult('id','id');
        $rsm->addScalarResult('user_id','user_id');
        $rsm->addScalarResult('profile_image_path','profile_image_path');
        $rsm->addScalarResult('first_name','first_name');
        $rsm->addScalarResult('last_name','last_name');
        $rsm->addScalarResult('roles','roles');
        $rsm->addScalarResult('activity_id','activity_id');
        $rsm->addScalarResult('question','question');
        $rsm->addScalarResult('created_at','created_at');
        $rsm->addScalarResult('count_comments','count_comments');
        $rsm->addScalarResult('count_likes','count_likes');
        $result=$entityManager->createNativeQuery('
        select p.id,p.question,p.activity_id,DATE_FORMAT(p.created_at,"%H:%i %d %M %Y") as created_at
        ,u.id as user_id,u.profile_image_path,u.last_name,u.first_name,u.roles
        ,IFNULL(usr_comments.cmpt,0) as count_comments ,IFNULL(usr_likes.cmpt,0) as count_likes
        from poll p inner join user u on p.user_id=u.id
        LEFT JOIN
            (SELECT comment.comment_on_id,COUNT(comment.id) cmpt FROM comment GROUP BY comment.comment_on_id ) 
                AS usr_comments ON p.activity_id=usr_comments.comment_on_id 
            LEFT JOIN 
            ( SELECT `like`.activity_id,COUNT(`like`.id) cmpt FROM `like` GROUP BY `like`.activity_id ) 
                AS usr_likes ON p.activity_id=usr_likes.activity_id
        order by p.created_at desc limit :maxResult offset :pageNum
        ',$rsm)
            ->setParameter("maxResult",$maxResult)
            ->setParameter("pageNum",($pageNum-1)*$maxResult)->getResult();


        return $result;

    }


    public function vote($user_id,$poll_id,$poll_answer){

        $entityManager = $this->getEntityManager();
        $rsm = new ResultSetMapping();

        $rsm->addScalarResult('poll_answer_id','id');
        $result=$entityManager->createNativeQuery("
        select poll_answer_id from user_poll_votes where user_id=:uid and poll_id=:pid
        ",$rsm)->setParameter("uid",$user_id)
            ->setParameter('pid',$poll_id)
            ->getOneOrNullResult();

        return $result;


    }
}
