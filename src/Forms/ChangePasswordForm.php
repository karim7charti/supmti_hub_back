<?php


namespace App\Forms;


use Symfony\Component\HttpFoundation\File\File;

class ChangePasswordForm
{

    private $password;
    private $newpassword;
    private $passwordConfirmation;



    /**
     * @param mixed $newpassword
     */
    public function setNewpassword($newpassword): ChangePasswordForm
    {
        $this->newpassword = $newpassword;
        return $this;
    }

    /**
     * @param mixed $passwordConfirmation
     */
    public function setPasswordConfirmation($passwordConfirmation): ChangePasswordForm
    {
        $this->passwordConfirmation = $passwordConfirmation;
        return $this;
    }

    /**
     * @param mixed $password
     */
    public function setPassword($password): ChangePasswordForm
    {
        $this->password = $password;
        return $this;
    }


    /**
     * @return mixed
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @return mixed
     */
    public function getNewpassword()
    {
        return $this->newpassword;
    }

    /**
     * @return mixed
     */
    public function getPasswordConfirmation()
    {
        return $this->passwordConfirmation;
    }





}