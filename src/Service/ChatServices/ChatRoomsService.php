<?php


namespace App\Service\ChatServices;


use App\Repository\ChatRoomRepository;
use App\Service\UserServices\CurrentUser;

class ChatRoomsService
{

    public function __construct(private ChatRoomRepository $chatRoomRepository,
                                private CurrentUser $currentUser)
    {
    }

    public function getMyChatRooms(int $maxResult,int $pageNum){
        $myId=$this->currentUser->getUser()->getId();
        $arr["myId"]=$myId;
        $arr['chatRooms'] =$this->chatRoomRepository->getMyChatRooms($myId,$maxResult,$pageNum);
        return $arr;

    }
    public function getChatroomMessages(int $chat_room_id,int $maxResult,int $pageNum):array{
        return $this->chatRoomRepository->getMessages($chat_room_id,$maxResult,$pageNum);
    }
}