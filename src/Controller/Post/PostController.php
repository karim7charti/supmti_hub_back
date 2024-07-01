<?php

namespace App\Controller\Post;

use App\DTO\ActivityUserDataDto;
use App\Repository\FileRepository;
use App\Service\Abstractions\Activities\CrudActivityInterface;
use App\Service\Files\ChunkFileUploader;
use App\Service\Posts\CrudPostService;

use App\Service\UserServices\CurrentUser;
use App\Service\UserServices\ProfileService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;



#[Route('/api/post')]
class PostController extends AbstractController
{

    public function __construct(private CrudPostService $postService,
                                private CrudActivityInterface $postCrud,
                                private ProfileService $profileService)
    {
    }

    #[Route("/{activity_id}",methods: ['GET'])]
    public function getCourseById(int $activity_id):Response{
        return $this->json($this->postCrud->getOneActivity($activity_id));
    }
    #[Route('/',methods: ["POST"])]
    public function create_post(Request $request,ChunkFileUploader $chunkFileUploader):Response{



        $f=$request->files->get("file");
        $details=$request->request->get("postDetails");
        $activity_id=$request->request->get("activity_id");
        if($f)
        {
            $file_name=$request->request->get("fileName");
            $done=$request->request->get("eof");
            $fileTempPath=$this->getParameter("brochures_directory")."/temp/$file_name";
            $fileReelPath=$this->getParameter("brochures_directory")."/posts_medias/";

            if($done=='true')
            {
                $result=$chunkFileUploader->upload_last_chunk($fileTempPath,$fileReelPath,$f);
                if($result["status"]==200)
                {
                    $id=$this->postService->bind_file_to_post($result["file_name"],$activity_id,$details);
                    return new Response($id);
                }

                else if($result["status"]==400)
                {
                    return $this->json($result,400);
                }

            }
            else
            {
                $chunkFileUploader->upload_chunk($fileTempPath,$f);
                return  new Response("chunking...");
            }
        }
        else{

            if($details)
                $this->postService->create_post_without_files($details);

            return new Response();
        }
    }

    #[Route("/my-posts/{maxResult}/{pageNum}",methods: ["GET"])]
    public function get_my_posts(int $maxResult,int $pageNum,CurrentUser $currentUser):Response{
        $user=$currentUser->getUser();
        $userDto=new ActivityUserDataDto($user);
        $data=[];
        $data[0]=$userDto;
        $data[1]=$this->postCrud->getById($maxResult,$pageNum,$userDto->getId());
        if(!$data[1])
            $data=[];
        return  $this->json($data);
    }
    #[Route('/all-posts/{maxResult}/{pageNum}',methods: ['GET'])]
    public function get_all_posts(int $maxResult,int $pageNum):Response{


        return $this->json($this->postCrud->getAll($maxResult,$pageNum));
    }

    #[Route('/his-posts/{maxResult}/{pageNum}/{email}/{id}',methods: ['GET'])]
    public function get_his_posts(int $maxResult,int $pageNum,string $email,int $id):Response{
        $data=[];
        $data[0]=$this->profileService->get_profile_data($email);
        $data[1]=$this->postCrud->getById($maxResult,$pageNum,$id);
        if(!$data[1])
            $data=[];
        return  $this->json($data);

    }

    #[Route("/get_post_file/{filename}")]
    public function get_post_media($filename):Response{

        $filePath=$this->getParameter("brochures_directory")."/posts_medias/$filename";
        if(file_exists($filePath))
            return new BinaryFileResponse($filePath);

        return $this->json("not found",404);

    }
}
