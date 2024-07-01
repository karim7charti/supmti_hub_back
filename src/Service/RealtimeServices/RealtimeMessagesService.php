<?php


namespace App\Service\RealtimeServices;


use App\Entity\Message;
use Symfony\Component\Mercure\HubInterface;
use Symfony\Component\Mercure\Update;

class RealtimeMessagesService
{
    private static $base_topic='message/to/user/';
    public function __construct(private HubInterface $hub)
    {
    }
    public function push(Message $message):void{

        $message_data=[
            "body"=>$message->getBody(),
            "type"=>$message->getType(),
            "created_at"=>$message->getCreatedAt()->format("d M"),
            "chat_room_id"=>$message->getChatRoom()->getId(),
            "first_nameR"=>$message->getReceiver()->getFirstName(),
            "first_nameS"=>$message->getSender()->getFirstName(),
            "last_nameR"=>$message->getReceiver()->getLastName(),
            "last_nameS"=>$message->getSender()->getLastName(),
            "profile_image_pathR"=>$message->getReceiver()->getProfileImagePath(),
            "profile_image_pathS"=>$message->getSender()->getProfileImagePath(),
            "roleR"=>$message->getReceiver()->getRoles(),
            "roleS"=>$message->getSender()->getRoles(),
            "seen"=>"0",
            "idR"=>$message->getSender()->getId(),
            "idS"=>$message->getSender()->getId(),
            "message_id"=>$message->getId()
        ];

        $update = new Update(
            $this::$base_topic.$message->getReceiver()->getId(),
            json_encode(['message' => $message_data]),
            true
        );
        $update1 = new Update(
            $this::$base_topic.'count/'.$message->getReceiver()->getId(),
            json_encode(['count' => 1])
        );
        $this->hub->publish($update1);
        $this->hub->publish($update);
    }

}