<?php


namespace App\Service\UserServices;


use App\Entity\PasswordResets;
use App\Repository\PasswordResetsRepository;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mime\Message;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use function Symfony\Component\DependencyInjection\Loader\Configurator\env;

class ResetPasswordService
{

    public function __construct(private UserRepository $userRepository,
                                private PasswordResetsRepository $passwordResetsRepository,
                                private MailerInterface $mailer,
                                private UserPasswordHasherInterface $passwordHasher

    )
    {
    }


    public function send_reset_link(string $email):int
    {
        $user=$this->userRepository->findOneByEmail($email);
        if($user)
        {

            $token=sha1(random_bytes(100));
            $password_reset=new PasswordResets();
            $password_reset->setEmail($email);
            $password_reset->setToken($token);
            $this->passwordResetsRepository->add($password_reset);

            $url='http://127.0.0.1:8000/password/reset/'.$token . '/' . urlencode($email);
            $mail = (new Email())
                ->from("ehei_hub@gmail.com")
                ->to($email)
                ->subject("hello there it's time to reset your password !")
                ->text('Your reset link is ready !')
                ->html('<a href="'.$url.'">your link!</a>');

            $this->mailer->send($mail);

            return 1;

        }
        return 404;

    }

    /**
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \Doctrine\ORM\ORMException
     * @throws \Exception
     */
    public function reset_password(string $email, string $token):string
    {
        $is_valid_link=$this->passwordResetsRepository->findOneByEmailAndToken($email,$token);
        if($is_valid_link!==null)
        {

            $user=$this->userRepository->findOneByEmail($email);
            $plainTextPassword=bin2hex(random_bytes(4));
            $hashedPassword = $this->passwordHasher->hashPassword(
                $user,
                $plainTextPassword
            );


            $user->setPassword($hashedPassword);
            $this->userRepository->add($user);
            $this->passwordResetsRepository->remove($is_valid_link);
            return $plainTextPassword;
        }

        return "0";
    }

}