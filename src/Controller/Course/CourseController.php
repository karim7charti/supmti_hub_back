<?php

namespace App\Controller\Course;

use App\DTO\ActivityUserDataDto;
use App\Repository\FileRepository;
use App\Repository\TargetClassRepository;

use App\Service\Abstractions\Activities\CrudActivityInterface;

use App\Service\UserServices\CurrentUser;
use App\Service\UserServices\ProfileService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


#[Route("/api/courses")]
class CourseController extends AbstractController
{
    public function __construct(
                                private CrudActivityInterface $courseCrud,
                                private ProfileService $profileService)
    {
    }


    #[Route('/target-classes',methods: ['GET'])]
    public function getTargetClasses(TargetClassRepository $classRepository):Response{

        $classes=$classRepository->findAll();
        return $this->json($classes);

    }

    #[Route("/course/{activity_id}",methods: ['GET'])]
    public function getCourseById(int $activity_id):Response{
        return $this->json($this->courseCrud->getOneActivity($activity_id));
    }

    #[Route('/publish-course',methods: ['POST'])]
    public function publish_course(Request $request):Response{
        $request_body=json_decode($request->getContent(),true);
            return $this->json($this->courseCrud->create($request_body));
    }
    #[Route('/get-my-courses/{maxResult}/{pageNum}',methods: ['GET'])]
    public function get_my_courses(int $maxResult,int $pageNum,CurrentUser $currentUser):Response{
        $user=$currentUser->getUser();
        $userDto=new ActivityUserDataDto($user);
        $data=[];
        $data[0]=$userDto;
        $data[1]=$this->courseCrud->getById($maxResult,$pageNum,$userDto->getId());
        if(!$data[1])
            $data=[];
        return  $this->json($data);
    }
    #[Route('/get-his-courses/{maxResult}/{pageNum}/{email}/{id}',methods: ['GET'])]
    public function get_his_courses(int $maxResult,int $pageNum,string $email,int $id):Response{
        $data=[];
        $data[0]=$this->profileService->get_profile_data($email);
        $data[1]=$this->courseCrud->getById($maxResult,$pageNum,$id);
        if(!$data[1])
            $data=[];
        return  $this->json($data);
    }


    #[Route('/download_file/{file_name}',methods: ['GET'])]
    public function download_file(string $file_name,FileRepository $fileRepository):Response{
        $filePath=$this->getParameter("brochures_directory")."/commun_files/$file_name";
        if(file_exists($filePath))
        {
            $file=$fileRepository->findOneByfileName($file_name);
            return $this->file($filePath,$file->getOriginalFileName());
        }

        return $this->json("not found",404);
    }
    #[Route('/courses/{maxResult}/{pageNum}',methods: ['GET'])]
    public function get_all_coursess(int $maxResult,int $pageNum):Response{

        return $this->json($this->courseCrud->getAll($maxResult,$pageNum));
    }


}
