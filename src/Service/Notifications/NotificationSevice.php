<?php


namespace App\Service\Notifications;


use App\Entity\Activity;
use App\Entity\Notification;
use App\Entity\User;
use App\Repository\NotificationRepository;
use App\Repository\NotificationTypeRepository;
use App\Repository\UserRepository;
use App\Service\RealtimeServices\RealtimeNotificationService;
use App\Service\UserServices\CurrentUser;

class NotificationSevice
{

    public function __construct(private NotificationRepository $notificationRepository,
                                private CurrentUser $currentUser,
                                private UserRepository $userRepository,
                                private RealtimeNotificationService $realtimeNotificationService,
                                private NotificationTypeRepository $notificationTypeRepository
    )
    {
    }
    public function notify(User $notifier,int $targetId,
                           string $type,
                           Activity $activity,string $path):void
    {
        $target=$this->userRepository->find($targetId);
        $notificationType=$this->notificationTypeRepository->findOneByLabel($type);
        $already_notified=$this->notificationRepository->findOneBy([
            "activity"=>$activity,
            "type"=>$notificationType
        ]);
        ;
        if($already_notified)
        {
            $already_notified->setNotifier($notifier)
                ->setNotifCount($already_notified->getNotifCount()+1)
                ->setIsSeen(false)
                ->setCreatedAt(new \DateTimeImmutable());
            $this->notificationRepository->add($already_notified);
            $this->realtimeNotificationService->push($already_notified);
        }
        else
        {
            $notification=new Notification();
            $notification->setNotifier($notifier)
                ->setType($notificationType)
                ->setNotifCount(0)
                ->setPath($path)
                ->setTarget($target)
                ->setActivity($activity)
                ->setIsSeen(false)
                ->setCreatedAt(new \DateTimeImmutable());
            $this->notificationRepository->add($notification);
            $this->realtimeNotificationService->push($notification);
        }


    }


    public function getMyNotifications(int $maxResults,int $pageNum,string $filter):array
    {
        $myId=$this->currentUser->getUser()->getId();
        return $this->notificationRepository->getMyNotifications(myId:$myId,
                                                                  maxResults: $maxResults,
                                                                  filter: $filter,
                                                                  pageNum: $pageNum);
    }

    public function markAsSeen(int $notifId):int{
        $notif=$this->notificationRepository->find($notifId);
        if($notif)
        {
            $notif->setIsSeen(true);
            $this->notificationRepository->add($notif);
            return 200;
        }
        else
            return 404;
    }

    public function markAllAsSeen():void{
        $mydId=$this->currentUser->getUser()->getId();

        $this->notificationRepository->markAllAsSeen($mydId);

    }
}