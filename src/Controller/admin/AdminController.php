<?php

namespace App\Controller\admin;

use App\Entity\User;
use App\Repository\PostRepository;
use App\Repository\UserRepository;
use App\Service\AdminServices\AnalyticsService;
use App\Service\AdminServices\HandleUsersService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use App\Validations\Validator;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route("/api/admin")]
class AdminController extends AbstractController
{

    public function __construct(private UserRepository $userRepository,
                                private HandleUsersService $handleUsersService,
                                private AnalyticsService $analyticsService
    )
    {

    }

    #[Route("/register",name:"register_user_from_admin",methods: ["post"])]
    public function registerUser(Request $request,
                                 ValidatorInterface $validatorInt,
                                 UserPasswordHasherInterface $passwordHasher,
                                 Validator $validator):Response{
        $reqParams=json_decode($request->getContent(),true);
        $user=new User();
        $user->setEmail($reqParams['email']);
        $hashedPassword = $passwordHasher->hashPassword(
            $user,
            $reqParams['password']
        );
        $user->setPassword($hashedPassword)
            ->setFirstName($reqParams['first_name'])
            ->setLastName($reqParams['last_name'])
            ->setCreatedAt(new \DateTimeImmutable())
            ->setRoles([$reqParams["role"]]);

        $result=$validator->validate($validatorInt,$user);
        if($result['faild'])
            return $this->json(['status'=>301,'errors'=>$result['errors']]);
        else
        {
            $this->handleUsersService->add_user($user,$reqParams['password']);
            return $this->json("added",200);
        }
    }


    #[Route("/edit/{id}",name:"edit_user_from_admin",methods: ["PUT"])]
    public function editUser(Request $request,$id,Validator $validator,ValidatorInterface $validatorInt):Response{
        $reqParams=json_decode($request->getContent(),true);

        $user=$this->userRepository->find($id);
        $user->setLastName($reqParams['last_name']);
        $user->setEmail($reqParams['email']);
        $user->setFirstName($reqParams['first_name']);

        $result=$validator->validate($validatorInt,$user);

        if($result['faild'])
            return $this->json(['status'=>301,'errors'=>$result['errors']]);
        else
        {
            $this->userRepository->add($user);
            return $this->json("added",200);
        }


    }

    #[Route("/delete/{id}",name:"delete_user_from_admin",methods: ["DELETE"])]
    public function deleteUser($id):Response{
        $user=$this->userRepository->find($id);
        if($user)
        {
            $this->userRepository->remove($user);
            return $this->json("done");
        }
        return $this->json("not found",404);

    }


    #[Route("/isAdmin",name:"is_admin",methods: ["GET"])]
    public function isAdmin():Response{
        return $this->json("yes");
    }

    #[Route('/users/{maxResult}/{firstResult}',methods: ['POST'])]
    public function getUsers($maxResult,$firstResult,Request $request):Response{
        $reqParams=json_decode($request->getContent(),true);
        $lname=$reqParams['lname'];
        $role=$reqParams['role'];
        return $this->json($this->handleUsersService->get_users($lname,$role,$maxResult,$firstResult));
    }

    #[Route('/dashboard-data',name: "dashboard-dat",methods: ["GET"])]
    public function getDashBoardData():Response{

        return $this->json(
            $this->analyticsService->get_dashboard_data()
        );

    }

}
