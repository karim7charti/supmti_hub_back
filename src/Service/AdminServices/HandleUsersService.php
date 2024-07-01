<?php


namespace App\Service\AdminServices;


use App\Entity\User;
use App\Repository\UserRepository;
use App\Validations\Validator;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mime\Message;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class HandleUsersService
{

    public function __construct(private UserRepository $userRepository,
                                private MailerInterface $mailer

                                 )
    {
    }

    public function add_user(User $user,string $plein_text_password)
    {
        $this->userRepository->add($user);
        $email = (new Email())
            ->from("ehei_hub@gmail.com")
            ->to($user->getEmail())
            ->subject("welcome aboard {$user->getFirstName()} !")
            ->text('Here is your account password you can use it to login : '.$plein_text_password .'!');


        $this->mailer->send($email);

    }

    public function get_users(string $lname,string $role,$maxResult,$firstResult):array{
        $count=$this->userRepository->getUsersCount($role,$lname);
        $users=$this->userRepository->findByRole($role,$maxResult,$firstResult,$lname);
        return [
            "count"=>$count,
            "users"=>$users
        ];
    }

}