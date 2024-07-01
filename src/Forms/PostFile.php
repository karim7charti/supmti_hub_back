<?php


namespace App\Forms;


use Symfony\Component\HttpFoundation\File\File;

class PostFile
{
    private $file;

    /**
     * @return mixed
     */
    public function getFile()
    {
        return $this->file;
    }

    /**
     * @param mixed $file
     */
    public function setFile(File $file=null): void
    {
        $this->file = $file;
    }

}