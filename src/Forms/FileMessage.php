<?php


namespace App\Forms;
use Symfony\Component\HttpFoundation\File\File;


class FileMessage
{
    private $message_file;

    /**
     * @return mixed
     */
    public function getMessageFile()
    {
        return $this->message_file;
    }

    /**
     * @param mixed $message_file
     */
    public function setMessageFile(?File $message_file): void
    {
        $this->message_file = $message_file;
    }


}