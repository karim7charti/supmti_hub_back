<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\ORM\Query\ResultSetMapping;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;

/**
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository implements PasswordUpgraderInterface
{
    public function __construct(private ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(User $entity, bool $flush = true): void
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
    public function remove(User $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    /**
     * Used to upgrade (rehash) the user's password automatically over time.
     */
    public function upgradePassword(PasswordAuthenticatedUserInterface $user, string $newHashedPassword): void
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', \get_class($user)));
        }

        $user->setPassword($newHashedPassword);
        $this->_em->persist($user);
        $this->_em->flush();
    }


    public function findByRole($value,$maxResult,$firstResult,$lname)
    {
        $entityManager = $this->getEntityManager();

        $result=$entityManager->createQueryBuilder()
            ->select(['u.id','u.email','u.last_name','u.profile_image_path','u.first_name',"count(p.id) count_pub"])
            ->from('App:User',"u")
            ->leftJoin('App:Post',"p","with","u=p.user")
            ->andWhere('u.roles = :role')
            ->setParameter("role",'["'.$value.'"]')
            ->andWhere("u.last_name like :lname")
            ->setParameter("lname",'%'.$lname.'%')
            ->groupBy("u.id")
            ->orderBy('u.created_at', 'DESC')
            ->getQuery()
            ->setFirstResult($firstResult)
            ->setMaxResults($maxResult)
            ->getResult();
        return $result;

    }


    /*
    public function findOneBySomeField($value): ?User
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
    public function getUsersCount($role,$lname)
    {
        return $this->createQueryBuilder('u')
            ->select('count(u.id) cmpt')
            ->andWhere('u.roles = :role')
            ->setParameter("role",'["'.$role.'"]')
            ->andWhere("u.last_name like :lname")
            ->setParameter("lname",'%'.$lname.'%')
            ->getQuery()
            ->getOneOrNullResult();
    }
    public function getProfileData($email){

        return $this->createQueryBuilder('u')
            ->select(['u.id',"u.email","u.profile_image_path","u.first_name","u.last_name","u.roles"])
            ->andWhere('u.email = :val')
            ->setParameter('val', $email)
            ->getQuery()
            ->getOneOrNullResult();
    }


    public function getGeneralProfileData(int $id){
        $entityManager = $this->getEntityManager();
        $rsm = new ResultSetMapping();

        $rsm->addScalarResult('id','id');
        $rsm->addScalarResult('last_name','last_name');
        $rsm->addScalarResult('email','email');
        $rsm->addScalarResult('first_name','first_name');
        $rsm->addScalarResult('roles','roles');
        $rsm->addScalarResult('profile_image_path','profile_image_path');
        $rsm->addScalarResult('cmpt','cmpt');
        $rsm->addScalarResult('created_at','created_at');
        $result=$entityManager->createNativeQuery('
        SELECT user.id,user.first_name,user.last_name,user.email,user.roles,user.profile_image_path,
               IFNULL(usr_posts.cmpt,0)+IFNULL(usr_polls.cmpt,0) +IFNULL(usr_courses.cmpt,0) cmpt
        
        FROM user 
            LEFT JOIN 
            ( SELECT poll.user_id user_id,COUNT(poll.id) cmpt FROM poll GROUP BY poll.user_id ) 
                AS usr_polls ON user.id=usr_polls.user_id 
            LEFT JOIN 
            ( SELECT post.user_id user_id,COUNT(post.id) cmpt FROM post GROUP BY post.user_id )
                AS usr_posts ON user.id=usr_posts.user_id 
            LEFT JOIN
            ( SELECT course.user_id user_id,COUNT(course.id) cmpt FROM course GROUP BY course.user_id ) 
                AS usr_courses ON user.id=usr_courses.user_id
        WHERE user.id=:id 
        ',$rsm)
            ->setParameter("id",$id)
            ->getOneOrNullResult();

        return $result;
    }
    public function get_sugested_users_for_chat(int $current_user_id,int $maxResuts,string $pattern){
        $entityManager = $this->getEntityManager();
        $rsm = new ResultSetMapping();

        $rsm->addScalarResult('id','id');
        $rsm->addScalarResult('last_name','last_name');
        $rsm->addScalarResult('first_name','first_name');
        $rsm->addScalarResult('roles','roles');
        $rsm->addScalarResult('profile_image_path','profile_image_path');
        return   $entityManager->createNativeQuery("
        SELECT user .id,user.profile_image_path,user.roles,user.last_name,user.first_name FROM user WHERE 
        user.id not IN (SELECT receiver_id FROM message WHERE message.sender_id=:id) 
        AND user.id NOT IN (SELECT sender_id FROM message WHERE 
         message.receiver_id=:id) AND user.id<>:id
         AND (user.last_name like :pattern OR user.first_name like :pattern)
           limit :maxResult 
        ",$rsm)->setParameter("id",$current_user_id)
            ->setParameter("maxResult",$maxResuts)
            ->setParameter("pattern","%$pattern%")
            ->getResult();
    }
}
