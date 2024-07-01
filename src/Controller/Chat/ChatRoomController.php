<?php

namespace App\Controller\Chat;

use App\Service\ChatServices\ChatRoomsService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


#[Route('/api/ChatRoom')]
class ChatRoomController extends AbstractController
{

    public function __construct(private ChatRoomsService $chatRoomsService)
    {
    }

    #[Route('',methods: ['GET'])]
    public function getMyChatRooms(Request $request):Response{

        $maxResult=(int)$request->query->get("maxResults");
        $pageNum=(int)$request->query->get("pageNum");
        return $this->json($this->chatRoomsService->getMyChatRooms($maxResult,$pageNum));

    }

    #[Route('/{chat_room_id}',methods: ['GET'])]
    public function getMessages(Request $request,int $chat_room_id):Response{
        $maxResult=(int)$request->query->get("maxResults");
        $pageNum=(int)$request->query->get("pageNum");
        return $this->json($this->chatRoomsService->getChatroomMessages($chat_room_id,$maxResult,$pageNum));

    }

}
