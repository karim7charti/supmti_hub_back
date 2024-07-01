<?php

namespace App\Repository;

use App\Entity\ChatRoomMembers;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\ORM\Query\ResultSetMapping;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ChatRoomMembers|null find($id, $lockMode = null, $lockVersion = null)
 * @method ChatRoomMembers|null findOneBy(array $criteria, array $orderBy = null)
 * @method ChatRoomMembers[]    findAll()
 * @method ChatRoomMembers[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ChatRoomMembersRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ChatRoomMembers::class);
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(ChatRoomMembers $entity, bool $flush = true): void
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
    public function remove(ChatRoomMembers $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }
    public function getMembersChatRoom(int $user1_id,int $user2_id)
    {
        $entityManager = $this->getEntityManager();
        $rsm = new ResultSetMapping();

        $rsm->addScalarResult('id','id');
        $result=$entityManager->createNativeQuery('
        select chat_room_id id from chat_room_members where 
        member_id=:user1 and chat_room_id
        in(select chat_room_id from chat_room_members where member_id=:user2)',$rsm)
            ->setParameter("user1",$user1_id)
            ->setParameter("user2",$user2_id)
            ->getOneOrNullResult();

        return $result;
    }

    // /**
    //  * @return ChatRoomMembers[] Returns an array of ChatRoomMembers objects
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
    public function findOneBySomeField($value): ?ChatRoomMembers
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
