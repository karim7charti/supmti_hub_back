<?php

namespace App\Repository;

use App\Entity\Notification;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\ORM\Query\ResultSetMapping;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Notification|null find($id, $lockMode = null, $lockVersion = null)
 * @method Notification|null findOneBy(array $criteria, array $orderBy = null)
 * @method Notification[]    findAll()
 * @method Notification[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class NotificationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Notification::class);
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(Notification $entity, bool $flush = true): void
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
    public function remove(Notification $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    public function getUnreadNotificationssCount(int $target)
    {
        $entityManager = $this->getEntityManager();
        $rsm = new ResultSetMapping();

        $rsm->addScalarResult('countNotifs','countNotifs');

        return $entityManager->createNativeQuery("
                    SELECT COUNT(notification.id) as countNotifs
                        FROM notification WHERE
                  notification.target_id=:target and notification.is_seen=0",$rsm)
            ->setParameter("target",$target)
            ->getScalarResult();

    }
    public function markAllAsSeen(int $myId){
        $this->getEntityManager()
            ->getConnection()
            ->executeQuery("update notification set is_seen=1 where target_id =:id",
                ["id"=>$myId]
            );
    }

    public function getMyNotifications(int $myId,int $maxResults,int $pageNum,string $filter):array{
        $entityManager = $this->getEntityManager();
        $rsm = new ResultSetMapping();

        $rsm->addScalarResult('id','id')
        ->addScalarResult('notif_count','notif_count')
        ->addScalarResult('is_seen','is_seen')
            ->addScalarResult('activity_id','activity_id')
        ->addScalarResult('path','path')
        ->addScalarResult('created_at','created_at')
            ->addScalarResult("label","label")
            ->addScalarResult("first_name","first_name")
            ->addScalarResult("profile_image_path","profile_image_path")
            ->addScalarResult("last_name","last_name");
        $clause="";
        if ($filter=="nonRead")
            $clause="and n.is_seen=0";

        return $entityManager->createNativeQuery("
                   SELECT n.id,n.notif_count,n.is_seen,n.activity_id,n.path,DATE_FORMAT(n.created_at,'%H:%i %d %b') as created_at,
                          nt.label,u.first_name,u.last_name,u.profile_image_path
                   
                   from notification n 
                       INNER JOIN
                       user u on n.notifier_id=u.id 
                       INNER JOIN 
                       notification_type nt on n.type_id=nt.id
                   WHERE n.target_id=:myId $clause
                   order by  n.created_at desc 
                   limit :maxResult offset :pageNum

                  "
            ,$rsm)
            ->setParameter("myId",$myId)
            ->setParameter("maxResult",$maxResults)
            ->setParameter("pageNum",($pageNum-1)*$maxResults)
            ->getResult();
    }

    // /**
    //  * @return Notification[] Returns an array of Notification objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('n')
            ->andWhere('n.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('n.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Notification
    {
        return $this->createQueryBuilder('n')
            ->andWhere('n.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
