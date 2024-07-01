<?php


namespace App\Service\Posts;


use App\Entity\Activity;
use App\Entity\File;
use App\Entity\Post;
use App\Repository\ActivityRepository;
use App\Repository\FileRepository;
use App\Repository\LikeRepository;
use App\Repository\PostRepository;
use App\Service\Abstractions\Activities\CrudActivityInterface;
use App\Service\UserServices\CurrentUser;

class CrudPostService implements CrudActivityInterface
{

    public function __construct(private PostRepository $postRepository,
                                private CurrentUser $currentUser,
                                private FileRepository $fileRepository,
                                private LikeRepository $likeRepository,
                                private ActivityRepository $activityRepository,

    )
    {
    }
    public function create($data): string
    {

    }


    public function create_post_without_files(string $postDetails)
    {
        $activity=new Activity();
        $this->activityRepository->add($activity);
        $user=$this->currentUser->getUser();
        $post=new Post();
        $post->setUser($user);
        $post->setCreatedAt(new \DateTimeImmutable());
        $post->setActivity($activity);
        $post->setDetails($postDetails);
        $this->postRepository->add($post);
        return $activity;
    }



    public function bind_file_to_post(string $filename,$activity_id,string $postDetails)
    {
        if($activity_id=="no")
        {
             $act=$this->create_post_without_files($postDetails);
             $file=new File();
             $file->setActivity($act)->setCreatedAt(new \DateTimeImmutable())
                 ->setFileName($filename)->setOriginalFileName($filename);
             $this->fileRepository->add($file);

             return $act->getId();
        }
        else{
            $activity=$this->activityRepository->find($activity_id);
            $file=new File();
            $file->setActivity($activity)->setCreatedAt(new \DateTimeImmutable())
                ->setFileName($filename)->setOriginalFileName($filename);
            $this->fileRepository->add($file);
            return $activity->getId();
        }
    }

    public function getOneActivity(int $activity_id)
    {
        $post=$this->postRepository->getOnePostById($activity_id);
        return $this->combine_post_files($post);
    }
    public function getAll(int $maxResult, int $pageNum): array
    {
        $posts=$this->postRepository->getAllPosts($maxResult,$pageNum);
        return $this->combine_post_files($posts);
    }

    public function getById(int $maxResult, int $pageNum, int $id): array
    {
        $posts=$this->postRepository->getUserPostes($id,$maxResult,$pageNum);
        return $this->combine_post_files($posts);
    }


    private function combine_post_files($posts):array{

        $res=[];

        $current_user_id=$this->currentUser->getUser()->getId();
        for ($i=0;$i<count($posts);$i++)
        {

            $activity_id=$posts[$i]['activity_id'];
            $files=$this->fileRepository->get_activity_files($activity_id);
            $liked=$this->likeRepository->did_like($current_user_id,$activity_id);
            $res[$i]['activity']=$posts[$i];
            $res[$i]['activity']['liked']=$liked;
            $res[$i]['activity']['type']="post";
            $res[$i]['files']=$files;
        }

        return $res;


    }
}