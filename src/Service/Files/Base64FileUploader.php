<?php


namespace App\Service\Files;


use Symfony\Component\HttpFoundation\File\UploadedFile;

class Base64FileUploader extends UploadedFile
{

    public function __construct(string $base64File)
    {

        $filePath = tempnam(sys_get_temp_dir(), 'UploadedFile');
        $data=explode(',',$base64File);
        if(count($data)>1)
            $data=$data[1];
        else
            $data=$base64File;

        $data = base64_decode($data);

        file_put_contents($filePath, $data);
        $error = null;
        $mimeType = null;
        $test = true;
        $date=new \DateTime();
        $result = $date->format('Y-m-d H:i:s');
        parent::__construct($filePath, $result, $mimeType, $error, $test);
    }

}