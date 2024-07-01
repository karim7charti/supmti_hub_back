<?php


namespace App\Service\UserServices;


use App\Repository\UserRepository;
use Symfony\Component\Security\Core\Security;

class CurrentUser
{
    public function __construct(private Security $security,private UserRepository $userRepository)
    {
    }

    public function getUser(){
        $email=  $this->security->getUser()->getUserIdentifier();
        $user=$this->userRepository->findOneByEmail($email);
        return $user;
    }
}