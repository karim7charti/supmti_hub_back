<?php


namespace App\Validations;


use App\Entity\Poll;
use App\Entity\PollAnswer;
use App\Service\UserServices\CurrentUser;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class PollValidation
{
    public function __construct(
        private ValidatorInterface $validatorInt,
        private Validator $validator,
        private CurrentUser $user
    )
    {
    }

    public function validate($poll_data){
        $poll=new Poll();
        $poll->setQuestion($poll_data['question'])
            ->setCreatedAt(new \DateTimeImmutable());
        $result=$this->validator->validate($this->validatorInt,$poll);


        if($result['faild'])
            return ['status'=>400,'errors'=>$result['errors']];
        else{
            $arr=$poll_data['answers'];
            $errors=['status'=>200,'errors'=>[]];
            $poll_answer_arr=[];

            for($i=0;$i<count($arr);$i++)
            {
                $poll_answer=new PollAnswer();
                $poll_answer->setAnswer($arr[$i]['answer'])->setVotes(0);
                $res=$this->validator->validate($this->validatorInt,$poll_answer);
                if($res['faild'])
                {
                    $errors['status']=400;
                    $errors['errors']['answer'.($i+1)]=$res["errors"];
                }
                else
                    $poll_answer_arr[]=$poll_answer;

            }
            if($errors["status"]==200)
            {
                $poll->setUser($this->user->getUser());
                $errors["poll"]=$poll;
                $errors["poll_aswers"]=$poll_answer_arr;

            }


            return $errors;
        }

    }
}