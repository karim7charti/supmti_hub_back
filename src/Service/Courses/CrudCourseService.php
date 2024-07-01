<?php


namespace App\Service\Courses;


use App\Entity\Activity;

use App\Entity\File;
use App\Repository\ActivityRepository;

use App\Repository\CourseRepository;
use App\Repository\FileRepository;
use App\Repository\LikeRepository;
use App\Service\Abstractions\Activities\CrudActivityInterface;
use App\Service\Files\FileUploader;
use App\Service\UserServices\CurrentUser;

class CrudCourseService implements CrudActivityInterface
{
    public function __construct(private validateCourse $course,
                                private CourseRepository $courseRepository,
                                private FileRepository $fileRepository,
                                private CurrentUser $user,
                                private LikeRepository $likeRepository,
                                private ActivityRepository $activityRepository,
                                private FileUploader $fileUploader
    ){}

    public function create($data): array
    {
        $result=$this->course->validate($data);

        if($result["status"]==200)
        {
            $course=$result["course"];
            $activity =new Activity();
            $this->activityRepository->add($activity);
            $course->setActivity($activity);

            $this->courseRepository->add($course);
            $arr=$result["files"];
            $originale_file_names=$result["file_names"];
            $count=count($arr);
            $this->fileUploader->setTargetDirectory($this->fileUploader->getTargetDirectory().'/commun_files');

            for($i=0;$i<$count;$i++)
            {
                $file=new File();
                $file_name=$this->fileUploader->upload($arr[$i]);

                $file->setCreatedAt(new \DateTimeImmutable())->setFileName($file_name)
                    ->setActivity($activity)
                    ->setOriginalFileName($originale_file_names[$i]);


                if($i==($count-1))
                    $this->fileRepository->add($file);
                else
                    $this->fileRepository->add($file,false);
            }
        }

        return $result;
    }

    public function getAll(int $maxResult, int $pageNum): array
    {

        $courses=$this->courseRepository->getAllCourses($maxResult,$pageNum);
        return $this->combine_course_files($courses);
    }

    public function getById(int $maxResult, int $pageNum, int $id): array
    {
        $courses=$this->courseRepository->getUserCourses($id,$maxResult,$pageNum);
        return $this->combine_course_files($courses);
    }

    public function getOneActivity(int $activity_id)
    {
        $course=$this->courseRepository->getOneCourseById($activity_id);
        return $this->combine_course_files($course);
    }


    private function combine_course_files($courses):array{
         $res=[];
        $current_user_id=$this->user->getUser()->getId();
         for ($i=0;$i<count($courses);$i++)
         {

             $activity_id=$courses[$i]['activity_id'];

             $files=$this->fileRepository->get_activity_files($activity_id);
             $liked=$this->likeRepository->did_like($current_user_id,$activity_id);
             $res[$i]['activity']=$courses[$i];
             $res[$i]['activity']['liked']=$liked;
             $res[$i]['activity']['type']="course";
             $res[$i]['files']=$files;

         }

        return $res;

    }

}