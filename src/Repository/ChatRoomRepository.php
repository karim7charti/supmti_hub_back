<?php

namespace App\Repository;

use App\Entity\ChatRoom;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\ORM\Query\ResultSetMapping;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ChatRoom|null find($id, $lockMode = null, $lockVersion = null)
 * @method ChatRoom|null findOneBy(array $criteria, array $orderBy = null)
 * @method ChatRoom[]    findAll()
 * @method ChatRoom[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ChatRoomRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ChatRoom::class);
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(ChatRoom $entity, bool $flush = true): void
    {
        $this->_em->persist($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    public function getMyChatRooms(int $my_id,int $maxResult,int $pageNum){
        $entityManager = $this->getEntityManager();
        $rsm = new ResultSetMapping();

        $rsm->addScalarResult('message_id','message_id')
            ->addScalarResult('roleR','roleR')
            ->addScalarResult('roleS','roleS')


            ->addScalarResult('idS','idS')
        ->addScalarResult('first_nameS','first_nameS')
        ->addScalarResult('last_nameS','last_nameS')
        ->addScalarResult('profile_image_pathS','profile_image_pathS')

            ->addScalarResult('idR','idR')
            ->addScalarResult('first_nameR','first_nameR')
            ->addScalarResult('last_nameR','last_nameR')
            ->addScalarResult('profile_image_pathR','profile_image_pathR')


            ->addScalarResult('chat_room_id','chat_room_id')
        ->addScalarResult('body','body')
        ->addScalarResult('type','type')
            ->addScalarResult('seen','seen')
            ->addScalarResult("created_at","created_at");

        $result=$entityManager->createNativeQuery('
        SELECT message.seen,
               u1.id idS,u1.first_name first_nameS,u1.last_name last_nameS,u1.roles roleS,u1.profile_image_path profile_image_pathS,
               u2.id idR,u2.first_name first_nameR,u2.last_name last_nameR ,u2.roles roleR,u2.profile_image_path profile_image_pathR,
               message.chat_room_id,message.id message_id,message.body,message.type,DATE_FORMAT(message.created_at,"%d %b") as created_at
        FROM message INNER JOIN chat_room on message.id=chat_room.last_message_id 
            INNER JOIN user u1 ON message.sender_id=u1.id 
            INNER JOIN user u2 ON message.receiver_id=u2.id 
    
        WHERE message.sender_id=:id OR message.receiver_id=:id
        order by message.created_at desc limit :maxResult offset :pageNum
       ',$rsm)
            ->setParameter("id",$my_id)
            ->setParameter("maxResult",$maxResult)
            ->setParameter("pageNum",($pageNum-1)*$maxResult)
            ->getResult();

        return $result;
    }
    public function getMessages(int $chat_room_id,int $maxResult,int $pageNum){
        $entityManager = $this->getEntityManager();
        $rsm = new ResultSetMapping();

        $rsm->addScalarResult('id','id')
            ->addScalarResult('sender_id','sender_id')
            ->addScalarResult('receiver_id','receiver_id')
            ->addScalarResult('body','body')
            ->addScalarResult('type','type')
            ->addScalarResult('created_at','created_at')
            ->addScalarResult('chat_room_id','chat_room_id')
            ->addScalarResult('seen','seen');
        return $entityManager->createNativeQuery('
            SELECT id,sender_id,receiver_id,body,`type`,DATE_FORMAT(created_at,"%H:%i %d %b %Y") created_at,chat_room_id,seen
            from message WHERE message.chat_room_id=:id 
            order  by message.created_at desc limit :maxResult offset :pageNum
            
        ',$rsm)->setParameter("id",$chat_room_id)
            ->setParameter("maxResult",$maxResult)
            ->setParameter("pageNum",($pageNum-1)*$maxResult)
            ->getResult();
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function remove(ChatRoom $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    // /**
    //  * @return ChatRoom[] Returns an array of ChatRoom objects
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
    public function findOneBySomeField($value): ?ChatRoom
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
