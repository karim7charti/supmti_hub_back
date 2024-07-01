<?php

namespace App\Controller\Interactions;


use App\Service\Likes\LikeService;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route("/api/likes")]
class LikesController extends AbstractController
{

    public function __construct(
                                private LikeService $likeService,
                               )
    {
    }

    #[Route('/like/{activity_id}/{owner}', methods: ['GET'])]
    public function like(Request $request,$activity_id,$owner): Response
    {
        $path=$request->query->get("on");
        $isOnComment=$request->query->get("pubId");


        return new Response(status: $this->likeService->like($activity_id,$owner,$path,$isOnComment));
    }

    #[Route('/dislike/{activity_id}', methods: ['GET'])]
    public function dislike($activity_id): Response
    {

        return new Response(status: $this->likeService->dislike($activity_id));

    }
}
