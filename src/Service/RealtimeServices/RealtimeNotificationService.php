<?php


namespace App\Service\RealtimeServices;


use App\Entity\Notification;
use Symfony\Component\Mercure\HubInterface;
use Symfony\Component\Mercure\Update;

class RealtimeNotificationService
{
    private static $base_topic='notification/';
    public function __construct(private HubInterface $hub)
    {

    }
    public function push(Notification $notification):void{
        $data=[
            'id'=>$notification->getId(),
            "notif_count"=>$notification->getNotifCount(),
            "is_seen"=>(string)((int)$notification->getIsSeen()),
            "activity_id"=>$notification->getActivity()->getId(),
            "path"=>$notification->getPath(),
            "created_at"=>$notification->getCreatedAt()->format("H:i d M"),
            "label"=>$notification->getType()->getLabel(),
            "first_name"=>$notification->getNotifier()->getFirstName(),
            "last_name"=>$notification->getNotifier()->getLastName(),
            "profile_image_path"=>$notification->getNotifier()->getProfileImagePath(),
        ];
        $update = new Update(
            $this::$base_topic.$notification->getTarget()->getId(),
            json_encode(['notif' => $data])
        );
        $update1 = new Update(
            $this::$base_topic.'count/'.$notification->getTarget()->getId(),
            json_encode(['count' => 1])
        );
        $this->hub->publish($update1);
        $this->hub->publish($update);

    }
}