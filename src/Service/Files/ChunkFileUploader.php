<?php


namespace App\Service\Files;


use App\Forms\PostFile;
use App\Validations\Validator;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ChunkFileUploader
{
    public function __construct(private ValidatorInterface $validatorInt,
                                private Validator $validator)
    {
    }

    private function validate(string $fileTempPath,$fileReelPath):array
    {
        $fff=new File($fileTempPath);
        $filePost=new PostFile();
        $filePost->setFile($fff);
        $date=new \DateTime();
        $safeFilename = $date->format('Y_m_d_H_i_s');
        $result=$this->validator->validate($this->validatorInt,$filePost);
        if($result['faild'])
        {
            unlink($fileTempPath);
            return ['status'=>400,'errors'=>$result['errors']];
        }

        else{

            $safeFileName=$safeFilename.'-'.uniqid().".".$fff->guessExtension();
            rename($fileTempPath,$fileReelPath.$safeFileName);
            return ['status'=>200,"file_name"=>$safeFileName];
        }

    }
    public function upload_last_chunk(string $fileTempPath,string$fileReelPath,
                                      string $file)
    {

        if(!file_exists($fileTempPath)){
            move_uploaded_file($file,$fileTempPath);
           return $this->validate($fileTempPath,$fileReelPath);


        }else{

            $fileStream=fopen($fileTempPath,"a");
            fwrite($fileStream,file_get_contents($file));
            return $this->validate($fileTempPath,$fileReelPath);

        }

    }

    public function upload_chunk(string $fileTempPath,string $file)
    {
        if(!file_exists($fileTempPath)){
            move_uploaded_file($file,$fileTempPath);
        }else{

            $fileStream=fopen($fileTempPath,"a");
            fwrite($fileStream,file_get_contents($file));
        }
    }
}