<?php

namespace App\Repository;

use App\Entity\PollAnswer;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\ORM\Query\ResultSetMapping;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method PollAnswer|null find($id, $lockMode = null, $lockVersion = null)
 * @method PollAnswer|null findOneBy(array $criteria, array $orderBy = null)
 * @method PollAnswer[]    findAll()
 * @method PollAnswer[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PollAnswerRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PollAnswer::class);
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(PollAnswer $entity, bool $flush = true): void
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
    public function remove(PollAnswer $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }


    public function get_answer_by_poll($id)
    {
        $entityManager = $this->getEntityManager();
        $rsm = new ResultSetMapping();

        $rsm->addScalarResult('id','id');
        $rsm->addScalarResult('answer','answer');
        $rsm->addScalarResult('votes','votes');

        $result=$entityManager->createNativeQuery('
        select a.id,a.answer,a.votes from poll_answer a where a.poll_id= :id
        ',$rsm)
            ->setParameter("id",$id)
            ->getResult();

        return $result;

    }


    public function get_answer_id($user_id,$poll_id){
        $entityManager = $this->getEntityManager();
        $rsm = new ResultSetMapping();

        $rsm->addScalarResult('poll_answer_id','answer_id');
        $rsm->addScalarResult('id','id');
        $result=$entityManager->createNativeQuery("
        select id,poll_answer_id from user_poll_votes where user_id=:uid and poll_id=:pid
        ",$rsm)->setParameter("uid",$user_id)
            ->setParameter('pid',$poll_id)
            ->getOneOrNullResult();
        return $result;
    }

    public function increment_votes($id)
    {
        $entityManager = $this->getEntityManager();
        $rsm = new ResultSetMapping();
        $result=$entityManager->createNativeQuery("
        update poll_answer set votes = votes+1 where id =:id
        ",$rsm)->setParameter("id",$id)
            ->execute();

    }

    public function decrement_votes($id)
    {
        $entityManager = $this->getEntityManager();
        $rsm = new ResultSetMapping();
        $entityManager->createNativeQuery("
        update poll_answer set votes = votes-1 where id =:id
        ",$rsm)->setParameter("id",$id)
            ->execute();
    }
}
