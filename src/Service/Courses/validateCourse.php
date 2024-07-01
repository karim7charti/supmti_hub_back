<?php


namespace App\Service\Courses;


use App\Entity\Course;
use App\Forms\File;
use App\Repository\TargetClassRepository;
use App\Service\Files\Base64FileUploader;
use App\Service\UserServices\CurrentUser;
use App\Validations\Validator;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class validateCourse
{
    public function __construct(        private ValidatorInterface $validatorInt,
                                        private Validator $validator,
                                        private CurrentUser $user,
                                        private TargetClassRepository $targetClassRepository)
    {
    }

    public function validate($data){
        $course=new Course();
        $course->setTitle($data['title'])->setDescription($data['description']);
        $result=$this->validator->validate($this->validatorInt,$course);
        if($result['faild'])
            return ['status'=>400,'errors'=>$result['errors']];
        else{
            $targetClass=$this->targetClassRepository->find($data['target_id']);
            $course->setTargetClass($targetClass)->setCreatedAt(new \DateTimeImmutable());

            $files=$data['files'];
            $originale_file_names=$data['file_names'];
            $length=count($files);
            $n=4;
            if($length<4)
                $n=$length;
            $errors=['status'=>200,'errors'=>[]];
            $files_arr=[];
            $originale_file_names_arr=[];
            for($i=0;$i<$n;$i++)
            {
                $file=new Base64FileUploader($files[$i]);
                $form_file=new File();
                $form_file->setFile($file);
                $res=$this->validator->validate($this->validatorInt,$form_file);
                if($res['faild'])
                {
                    $errors['status']=400;
                    $errors['errors'][$i]=$res["errors"];
                }
                else
                {
                    $files_arr[]=$file;
                    $originale_file_names_arr[]=$originale_file_names[$i];
                }

            }
            if($errors["status"]==200)
            {
                $course->setUser($this->user->getUser());
                $errors["course"]=$course;
                $errors["files"]=$files_arr;
                $errors["file_names"]=$originale_file_names_arr;
            }

            return $errors;
        }

    }

}