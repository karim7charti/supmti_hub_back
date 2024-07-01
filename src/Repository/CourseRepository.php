<?php

namespace App\Repository;

use App\Entity\Course;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\ORM\Query\ResultSetMapping;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Course|null find($id, $lockMode = null, $lockVersion = null)
 * @method Course|null findOneBy(array $criteria, array $orderBy = null)
 * @method Course[]    findAll()
 * @method Course[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CourseRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Course::class);
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(Course $entity, bool $flush = true): void
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
    public function remove(Course $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    // /**
    //  * @return Course[] Returns an array of Course objects
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

    /*
    public function findOneBySomeField($value): ?Course
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */

    public function getOneCourseById($activity_id){
        $entityManager = $this->getEntityManager();
        $rsm = new ResultSetMapping();

        $rsm->addScalarResult('id','id');
        $rsm->addScalarResult('user_id','user_id');
        $rsm->addScalarResult('profile_image_path','profile_image_path');
        $rsm->addScalarResult('first_name','first_name');
        $rsm->addScalarResult('last_name','last_name');
        $rsm->addScalarResult('roles','roles');
        $rsm->addScalarResult('activity_id','activity_id');
        $rsm->addScalarResult('title','title');
        $rsm->addScalarResult('description','description');
        $rsm->addScalarResult('created_at','created_at');
        $rsm->addScalarResult('count_comments','count_comments');
        $rsm->addScalarResult('count_likes','count_likes');
        $result=$entityManager->createNativeQuery('
        select c.id,c.title,c.description,c.activity_id,DATE_FORMAT(c.created_at,"%H:%i %d %M %Y") as created_at
        ,u.id as user_id,u.profile_image_path,u.last_name,u.first_name,u.roles
         ,IFNULL(usr_comments.cmpt,0) as count_comments ,IFNULL(usr_likes.cmpt,0) as count_likes
        from course c inner join user u on c.user_id=u.id
             LEFT JOIN
            (SELECT comment.comment_on_id,COUNT(comment.id) cmpt FROM comment GROUP BY comment.comment_on_id ) 
                AS usr_comments ON c.activity_id=usr_comments.comment_on_id 
            LEFT JOIN 
            ( SELECT `like`.activity_id,COUNT(`like`.id) cmpt FROM `like` GROUP BY `like`.activity_id ) 
                AS usr_likes ON c.activity_id=usr_likes.activity_id
        where c.activity_id=:id
        order by c.created_at desc 
        ',$rsm)
            ->setParameter("id",$activity_id)
            ->getResult();


        return $result;
    }
    public function getUserCourses($user_id,$maxResult,$pageNum)
    {
        $entityManager = $this->getEntityManager();
        $rsm = new ResultSetMapping();

        $rsm->addScalarResult('id','id');
        $rsm->addScalarResult('title','title');
        $rsm->addScalarResult('description','description');
        $rsm->addScalarResult('activity_id','activity_id');
        $rsm->addScalarResult('created_at','created_at');
        $rsm->addScalarResult('count_comments','count_comments');
        $rsm->addScalarResult('count_likes','count_likes');
        $result=$entityManager->createNativeQuery('
        select c.id,c.title,c.description,c.activity_id,DATE_FORMAT(c.created_at,"%H:%i %d %M %Y") as created_at
         ,IFNULL(usr_comments.cmpt,0) as count_comments ,IFNULL(usr_likes.cmpt,0) as count_likes
        from course c
            LEFT JOIN
            (SELECT comment.comment_on_id,COUNT(comment.id) cmpt FROM comment GROUP BY comment.comment_on_id ) 
                AS usr_comments ON c.activity_id=usr_comments.comment_on_id 
            LEFT JOIN 
            ( SELECT `like`.activity_id,COUNT(`like`.id) cmpt FROM `like` GROUP BY `like`.activity_id ) 
                AS usr_likes ON c.activity_id=usr_likes.activity_id
        where c.user_id=:id order by c.created_at desc limit :maxResult offset :pageNum
        ',$rsm)
            ->setParameter("id",$user_id)
            ->setParameter("maxResult",$maxResult)
            ->setParameter("pageNum",($pageNum-1)*$maxResult)
            ->getResult();

        return $result;
    }
    public function getAllCourses($maxResult,$pageNum){
        $entityManager = $this->getEntityManager();
        $rsm = new ResultSetMapping();

        $rsm->addScalarResult('id','id');
        $rsm->addScalarResult('user_id','user_id');
        $rsm->addScalarResult('profile_image_path','profile_image_path');
        $rsm->addScalarResult('first_name','first_name');
        $rsm->addScalarResult('last_name','last_name');
        $rsm->addScalarResult('roles','roles');
        $rsm->addScalarResult('activity_id','activity_id');
        $rsm->addScalarResult('title','title');
        $rsm->addScalarResult('description','description');
        $rsm->addScalarResult('created_at','created_at');
        $rsm->addScalarResult('count_comments','count_comments');
        $rsm->addScalarResult('count_likes','count_likes');
        $result=$entityManager->createNativeQuery('
        select c.id,c.title,c.description,c.activity_id,DATE_FORMAT(c.created_at,"%H:%i %d %M %Y") as created_at
        ,u.id as user_id,u.profile_image_path,u.last_name,u.first_name,u.roles
         ,IFNULL(usr_comments.cmpt,0) as count_comments ,IFNULL(usr_likes.cmpt,0) as count_likes
        from course c inner join user u on c.user_id=u.id
             LEFT JOIN
            (SELECT comment.comment_on_id,COUNT(comment.id) cmpt FROM comment GROUP BY comment.comment_on_id ) 
                AS usr_comments ON c.activity_id=usr_comments.comment_on_id 
            LEFT JOIN 
            ( SELECT `like`.activity_id,COUNT(`like`.id) cmpt FROM `like` GROUP BY `like`.activity_id ) 
                AS usr_likes ON c.activity_id=usr_likes.activity_id
        order by c.created_at desc limit :maxResult offset :pageNum
        ',$rsm)
            ->setParameter("maxResult",$maxResult)
            ->setParameter("pageNum",($pageNum-1)*$maxResult)->getResult();


        return $result;

    }
}
