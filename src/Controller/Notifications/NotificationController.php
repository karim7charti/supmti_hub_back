<?php

namespace App\Controller\Notifications;

use App\Service\Notifications\NotificationSevice;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


#[Route('/api/Notification')]
class NotificationController extends AbstractController
{
    public function __construct(private NotificationSevice $notificationSevice)
    {
    }

    #[Route("",methods: ['GET'])]
    public function getMyNotifications(Request $request):Response{
        $maxResults=$request->query->get("maxResults");
        $pageNum=$request->query->get("pageNum");
        $filter=$request->query->get("filter");

        return $this->json($this->notificationSevice->getMyNotifications($maxResults,$pageNum,$filter));
    }

    #[Route("/{notifId}",methods: ["PATCH"])]
    public function markAsSeen(int $notifId):Response
    {
        return new Response(status: $this->notificationSevice->markAsSeen($notifId));
    }
    #[Route("",methods: "PUT")]
    public function markAllAsSeen():Response
    {
        $this->notificationSevice->markAllAsSeen();
        return new Response();
    }
}
