<?php


namespace App\Service\Polls;


use App\Repository\PollAnswerRepository;
use App\Repository\UserPollVotesRepository;
use App\Service\Abstractions\Activities\Voteable;
use App\Service\UserServices\CurrentUser;

class PollVoteUtilityService implements Voteable
{

    public function __construct(private PollAnswerRepository $answerRepository,
                                private CurrentUser $user,
                                private UserPollVotesRepository $userPollVotesRepository,
    )
    {
    }

    public function vote(int $answer_id, int $poll_id):void
    {
        $id=$this->user->getUser()->getId();
        $previos_votes=$this->answerRepository->get_answer_id($id,$poll_id);

        if($previos_votes==null)
        {
            $this->userPollVotesRepository->insert($id,$poll_id,$answer_id);
            $this->answerRepository->increment_votes($answer_id);
        }
        elseif ($previos_votes['answer_id']==$answer_id)
        {
            $this->answerRepository->decrement_votes($answer_id);
            $this->userPollVotesRepository->delete($previos_votes['id']);
        }
        else
        {
            $this->answerRepository->increment_votes($answer_id);
            $this->answerRepository->decrement_votes($previos_votes['answer_id']);
            $this->userPollVotesRepository->update($previos_votes['id'],$answer_id);
        }
    }
}