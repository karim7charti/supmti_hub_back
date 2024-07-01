<?php


namespace App\Forms;

use Symfony\Component\HttpFoundation\File\File;

class FileComment
{
        private $comment_file;

    /**
     * @return mixed
     */
    public function getCommentFile()
    {
        return $this->comment_file;
    }

    /**
     * @param mixed $comment_file
     */
    public function setCommentFile(File $comment_file): void
    {
        $this->comment_file = $comment_file;
    }


}