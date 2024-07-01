<?php


namespace App\Service\UserServices;


use App\Repository\UserRepository;

class UserService
{
    public function __construct(private CurrentUser $currentUser,
                                private UserRepository $userRepository
    )
    {
    }

    public function get_suggested_users_for_chat(int $maxResults,string $pattern):array{
        $id=$this->currentUser->getUser()->getId();

        return ["myId"=>$id,"users"=>$this->userRepository->get_sugested_users_for_chat($id,$maxResults,$pattern)];

    }
}