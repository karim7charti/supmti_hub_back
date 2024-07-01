<?php


namespace App\Forms;

use Symfony\Component\HttpFoundation\File\File as f;
class File
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
    public function setFile(f $file=null): void
    {
        $this->file = $file;
    }



}