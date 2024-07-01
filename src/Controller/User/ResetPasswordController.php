<?php

namespace App\Controller\User;
use App\Service\UserServices\ResetPasswordService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ResetPasswordController extends AbstractController
{

    public function __construct(private ResetPasswordService $passwordService)
    {
    }

    /**
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \Doctrine\ORM\ORMException
     */
    #[Route('/send-reset-link',name: "send-reset-link",methods: ["POST"])]
    public function sendResetPasswordLink(Request $request):Response{

        $reqParams=json_decode($request->getContent(),true);
        $email=$reqParams['email'];
        $result=$this->passwordService->send_reset_link($email);
        if($result==1)
        {
            return $this->json("link sended");
        }
        return $this->json("user not found",404);
    }

    #[Route('/password/reset/{token}/{email}')]
    public function reset_password($token,$email):Response{

        $is_reseted=$this->passwordService->reset_password($email,$token);
        if($is_reseted=="0")
        {
            return new Response("lien invalide ou expiré",404);
        }
        return new Response("votre compte est récupéré avec succes ,utiliser ce mot de passe 
        pour se connecter la prochaine fois et aprés vous pouvez le changer : $is_reseted");

    }
}
