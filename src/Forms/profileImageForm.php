<?php


namespace App\Forms;


use Symfony\Component\HttpFoundation\File\File;

class profileImageForm
{

    private $image;

    /**
     * @return mixed
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * @param mixed $image
     */
    public function setImage(File $image=null): void
    {
        $this->image = $image;
    }

    /**
     * @return mixed
     */




}