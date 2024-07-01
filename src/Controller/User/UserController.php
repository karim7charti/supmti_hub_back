<?php

namespace App\Controller\User;

use App\Entity\User;
use App\Forms\ChangePasswordForm;
use App\Forms\profileImageForm;
use App\Repository\UserRepository;
use App\Service\Files\Base64FileUploader;
use App\Service\Files\FileUploader;
use App\Service\UserServices\CurrentUser;
use App\Service\UserServices\ProfileService;
use App\Service\UserServices\UserService;
use App\Validations\Validator;
use phpDocumentor\Reflection\Types\This;
use Psr\Container\ContainerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\AcceptHeader;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mercure\Authorization;
use Symfony\Component\Mercure\Discovery;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;


#[Route("/api")]
class UserController extends AbstractController
{

    public function __construct(private ProfileService $profileService,private UserService $userService)
    {

    }


    #[Route('/isAuthenticated',name: "is_authenticated",methods: ["GET"])]
    public function isAuth(Request $request,Authorization $authorization,CurrentUser $user,Discovery $discovery){


        $res=$this->json('hello');
        return $res;
    }

    #[Route("/user-profile-data",name: "get-user-profile-data",methods: ["GET"])]
    public function getProfileData(Request $request,CurrentUser $user,Authorization $authorization,Security $security):Response{
        $email=$security->getUser()->getUserIdentifier();
        $authorization->setCookie($request, ['message/to/user/'.$user->getUser()->getId()]);
        $res=$this->json($this->profileService->get_profile_data($email));

        return $res;

    }
    #[Route("/user/{id}",methods: ["GET"])]
    public function getGenralProfileData(int $id,CurrentUser $user,UserRepository $userRepository):Response{
        $curent_id=$user->getUser()->getId();
        if($id==$curent_id)
            return $this->json("same_user");
        else{
            $target_user=$userRepository->find($id);
            if($target_user)
            {
                return $this->json($this->profileService->get_generale_profile_data($id));
            }
            return $this->json("not found",404);
        }



    }

    #[Route('/edit-password',methods: ["PUT"])]
    public function edit_password(Request $request,
                                    ValidatorInterface $validatorInt,
                                    Validator $validator,
                                    ChangePasswordForm $changePasswordForm,


    ):Response{

        $body=json_decode($request->getContent(),true);

        $changePasswordForm->setPassword($body['password'])
            ->setNewpassword($body['newpassword'])
            ->setPasswordConfirmation($body['passwordConfirmation']);

        $result=$validator->validate($validatorInt,$changePasswordForm);
        if($result['faild'])
            return $this->json(['status'=>400,'errors'=>$result['errors']]);
        else
        {
            $this->profileService->update_password($body['newpassword']);
            return new Response("good");
        }


    }

    #[Route('/upload-profile-image',methods: ['POST'])]
    public function upload_profile_image(Request $request,
                                         FileUploader $fileUploader,
                                         ValidatorInterface $validatorInt,
                                         Validator $validator,
                                         profileImageForm $form):Response{
        $data=json_decode($request->getContent(),true);
        $imageBase64=$data['profile_image'];
        $image=new Base64FileUploader($imageBase64);

        $form->setImage($image);


        $result=$validator->validate($validatorInt,$form);
        if($result['faild'])
            return $this->json(['status'=>400,'errors'=>$result['errors']]);
        else
        {
            $fileUploader->setTargetDirectory($fileUploader->getTargetDirectory().'/profile_images');
            $fileName=$fileUploader->upload($image);
            $this->profileService->set_profil_image($fileName);
            return new Response($fileName);
        }
    }
    #[Route("/get_image/{filename}")]
    public function get_profile_image($filename):Response{

        $filePath=$this->getParameter("brochures_directory")."/profile_images/$filename";
        if(file_exists($filePath))
            return new BinaryFileResponse($filePath);

        return $this->json("not found",404);

    }
    #[Route('/delete-profile-image',methods: ['DELETE'])]
    public function remove_profile_image(): Response
    {
            $this->profileService->remove_profile_image();

            return $this->json("good");

    }



    #[Route("/user/chat/suggested",methods: ["GET"])]
    public function get_suggested_users_for_chat(Request $request):Response{
        $maxResult=$request->query->get("maxResult");
        $pattern=$request->query->get("pattern");
        return $this->json($this->userService->get_suggested_users_for_chat($maxResult,$pattern));
    }


}
