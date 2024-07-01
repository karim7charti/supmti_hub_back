<?php


namespace App\Service\Polls;


use App\Entity\Activity;
use App\Entity\Poll;
use App\Entity\PollAnswer;
use App\Repository\ActivityRepository;
use App\Repository\LikeRepository;
use App\Repository\PollAnswerRepository;
use App\Repository\PollRepository;
use App\Repository\UserPollVotesRepository;
use App\Service\Abstractions\Activities\CrudActivityInterface;
use App\Service\UserServices\CurrentUser;
use App\Validations\PollValidation;
use App\Validations\Validator;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class CrudPollService implements CrudActivityInterface
{

    public function __construct(private PollValidation $pollValidation,
                                private PollRepository $pollRepository,
                                private PollAnswerRepository $answerRepository,
                                private UserPollVotesRepository $userPollVotesRepository,
                                private ActivityRepository $activityRepository,
                                private LikeRepository $likeRepository,
                                private CurrentUser $user)
    {

    }


    public function create($poll_data):array
    {

        $result=$this->pollValidation->validate($poll_data);

        if($result["status"]==200)
        {
            $poll=$result["poll"];
            $activity =new Activity();
            $this->activityRepository->add($activity);
            $poll->setActivity($activity);
            $this->pollRepository->add($poll);
            $arr=$result["poll_aswers"];
            $count=count($arr);

            for($i=0;$i<$count;$i++)
            {

                $arr[$i]->setPoll($poll);
                if($i==($count-1))
                    $this->answerRepository->add($arr[$i]);
                else
                    $this->answerRepository->add($arr[$i],false);

            }
        }

        return $result;
    }

    public function getAll(int $maxResult,int $pageNum): array
    {
        $polls=$this->pollRepository->getAllPolls($maxResult,$pageNum);
        return $this->combine_polls_answers($polls);

    }
    public function getById(int $maxResult, int $pageNum, int $id): array
    {
        $polls=$this->pollRepository->getUserPolls($id,$maxResult,$pageNum);
        return $this->combine_polls_answers($polls);
    }
    public function getOneActivity(int $activity_id)
    {
        $poll=$this->pollRepository->getOnePollById($activity_id);
        return $this->combine_polls_answers($poll);
    }


    private function combine_polls_answers($polls):array{

        $res=[];
        $current_user_id=$this->user->getUser()->getId();
        for ($i=0;$i<count($polls);$i++)
        {
            $poll_id=$polls[$i]['id'];
            $activity_id=$polls[$i]['activity_id'];

            $poll_answers=$this->answerRepository->get_answer_by_poll($poll_id);
            $voted_answer_id=$this->userPollVotesRepository->get_voted_answer($current_user_id,$poll_id);
            $liked=$this->likeRepository->did_like($current_user_id,$activity_id);
            $res[$i]['activity']=$polls[$i];
            $res[$i]['activity']['liked']=$liked;
            $res[$i]['activity']['type']="poll";
            for($j=0;$j<count($poll_answers);$j++)
            {
                if($voted_answer_id==null)
                    $poll_answers[$j]['voted']=false;
                else if($voted_answer_id['id']==$poll_answers[$j]['id'])
                    $poll_answers[$j]['voted']=true;
                else
                    $poll_answers[$j]['voted']=false;


            }
            $res[$i]['answers']=$poll_answers;

        }

        return $res;

    }



}