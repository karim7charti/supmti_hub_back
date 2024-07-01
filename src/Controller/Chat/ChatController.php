<?php

namespace App\Controller\Chat;

use App\Service\ChatServices\MessageService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


#[Route('/api/Message')]
class ChatController extends AbstractController
{
    public function __construct(private MessageService $messageService,
                                )
    {
    }

    #[Route('',methods: ['POST'])]
    public function send(Request $request):Response{



            return $this->messageService->send($request);




    }
    #[Route('/{id}',methods: ['PATCH'])]
    public function see(int $id):Response{

         return new Response(status:$this->messageService->mark_as_seen($id));

    }





}
