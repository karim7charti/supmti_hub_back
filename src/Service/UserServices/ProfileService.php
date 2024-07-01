<?php


namespace App\Service\UserServices;


use App\Repository\MessageRepository;
use App\Repository\NotificationRepository;
use App\Repository\UserRepository;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\Security;


class ProfileService
{

    public function __construct(private Security $security,
                                private UserRepository $userRepository,
                                private NotificationRepository $notificationRepository,
                                private MessageRepository $messageRepository,
                                private UserPasswordHasherInterface $hasher,
                                private Filesystem $filesystem,
                                private string $upload_dire
                                 )
    {

    }

    private function get_current_user()
    {
        $email=  $this->security->getUser()->getUserIdentifier();
        $user=$this->userRepository->findOneByEmail($email);
        return $user;
    }

    public function get_profile_data(string $email){

        $user=$this->userRepository->getProfileData($email);
        $user["countMessages"]=(int)$this->messageRepository
            ->getUnreadMessagesCount($user['id'])[0]["countMessages"];
        $user["countNotifs"]=(int)$this->notificationRepository
            ->getUnreadNotificationssCount($user['id'])[0]["countNotifs"];
        return $user;
    }
    public function get_generale_profile_data(int $id)
    {

        return $this->userRepository->getGeneralProfileData($id);
    }

    public function set_profil_image($file_name){

        $user=$this->get_current_user();
        if($user->getProfileImagePath()!=null)
            $this->filesystem->remove($this->upload_dire."/profile_images/".$user->getProfileImagePath());
        $user->setProfileImagePath($file_name);
        $this->userRepository->add($user);
    }

    public function update_password(string $new_password){
        $user=$this->get_current_user();
        $hashedPassword = $this->hasher->hashPassword(
            $user,
            $new_password
        );
        $user->setPassword($hashedPassword);
        $this->userRepository->add($user);

    }

    public function remove_profile_image(){
        $user=$this->get_current_user();
        if($user->getProfileImagePath()!=null)
            $this->filesystem->remove($this->upload_dire."/profile_images/".$user->getProfileImagePath());
        $user->setProfileImagePath(null);
        $this->userRepository->add($user);
    }

}