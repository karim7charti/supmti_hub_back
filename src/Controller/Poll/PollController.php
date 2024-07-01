<?php

namespace App\Controller\Poll;

use App\DTO\ActivityUserDataDto;

use App\Service\Abstractions\Activities\CrudActivityInterface;
use App\Service\Abstractions\Activities\Voteable;
use App\Service\Polls\CrudPollService;
use App\Service\UserServices\CurrentUser;
use App\Service\UserServices\ProfileService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


#[Route("/api/polls")]
class PollController extends AbstractController
{

    public function __construct(
                                private CrudActivityInterface $pollCrud,
                                private ProfileService $profileService)
    {}

    #[Route('/create-poll',methods: ['POST'])]
    public function create_poll(Request $request,

    ):Response{
        $request_body=json_decode($request->getContent(),true);
        $result=$this->pollCrud->create($request_body);
        if($result["status"]==400)
            return $this->json($result);

        return $this->json("good");

    }
    #[Route("/poll/{activity_id}",methods: ['GET'])]
    public function getCourseById(int $activity_id):Response{
        return $this->json($this->pollCrud->getOneActivity($activity_id));
    }
    #[Route('/get-my-polls/{maxResult}/{pageNum}',methods: ['GET'])]
    public function get_my_polls(int $maxResult,int $pageNum,CurrentUser $currentUser):Response{

        $user=$currentUser->getUser();
        $userDto=new ActivityUserDataDto($user);
        $data=[];
        $data[0]=$userDto;
        $data[1]=$this->pollCrud->getById($maxResult,$pageNum,$userDto->getId());
        if(!$data[1])
            $data=[];
        return  $this->json($data);
    }
    #[Route('/get-his-polls/{maxResult}/{pageNum}/{email}/{id}',methods: ['GET'])]
    public function get_his_polls(int $maxResult,int $pageNum,string $email,int $id):Response{

        $data=[];
        $data[0]=$this->profileService->get_profile_data($email);
        $data[1]=$this->pollCrud->getById($maxResult,$pageNum,$id);
        if(!$data[1])
            $data=[];
        return  $this->json($data);
    }

    #[Route('/vote/{id}/{poll_id}',methods: ['GET'])]
    public function vote($id,$poll_id,Voteable $pollVote):Response{

        $pollVote->vote($id,$poll_id);
        return $this->json("goood");

    }
    #[Route('/get-all-polls/{maxResult}/{pageNum}',methods: ['GET'])]
    public function get_all_polls(int $maxResult,int $pageNum):Response{

        return $this->json($this->pollCrud->getAll($maxResult,$pageNum));
    }


}
