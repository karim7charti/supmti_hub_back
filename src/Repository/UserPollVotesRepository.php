<?php

namespace App\Repository;

use App\Entity\UserPollVotes;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\ORM\Query\ResultSetMapping;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method UserPollVotes|null find($id, $lockMode = null, $lockVersion = null)
 * @method UserPollVotes|null findOneBy(array $criteria, array $orderBy = null)
 * @method UserPollVotes[]    findAll()
 * @method UserPollVotes[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserPollVotesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserPollVotes::class);
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(UserPollVotes $entity, bool $flush = true): void
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
    public function remove(UserPollVotes $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    // /**
    //  * @return UserPollVotes[] Returns an array of UserPollVotes objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('u.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?UserPollVotes
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */

    public function get_voted_answer($user_id,$poll_id){
        $entityManager = $this->getEntityManager();
        $rsm = new ResultSetMapping();

        $rsm->addScalarResult('poll_answer_id','id');

        $result=$entityManager->createNativeQuery('
            select poll_answer_id from user_poll_votes where user_id= :user and poll_id =:poll
        ',$rsm)
            ->setParameter("user",$user_id)
            ->setParameter("poll",$poll_id)
            ->getOneOrNullResult();

        return $result;

    }

    public function insert($user_id,$poll_id,$answer_id){
        $entityManager = $this->getEntityManager();
        $rsm = new ResultSetMapping();

        $entityManager->createNativeQuery("
        insert into user_poll_votes value (NULL,:uid,:pid,:aid) 
        ",$rsm)
            ->setParameter("uid",$user_id)
            ->setParameter("pid",$poll_id)
            ->setParameter("aid",$answer_id)
            ->execute();
    }

    public function delete($id)
    {
        $entityManager = $this->getEntityManager();
        $rsm = new ResultSetMapping();

        $entityManager->createNativeQuery("
        delete from user_poll_votes where id =:id
        ",$rsm)
            ->setParameter("id",$id)
            ->execute();
    }
    public function update($id,$answer)
    {
        $entityManager = $this->getEntityManager();
        $rsm = new ResultSetMapping();

        $entityManager->createNativeQuery("
        update user_poll_votes set poll_answer_id=:answer where id =:id
        ",$rsm)
            ->setParameter("id",$id)
            ->setParameter("answer",$answer)
            ->execute();
    }
}
