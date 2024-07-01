<?php


namespace App\Validations;


use Symfony\Component\Validator\Validator\ValidatorInterface;

class Validator
{
    public function validate(ValidatorInterface $validator,$obj){
        $errors = $validator->validate($obj);
        if(count($errors)>0)
        {
            $err=[];
            for ($i = 0; $i < $errors->count(); $i++) {
                $err[$errors[$i]->getPropertyPath()] = $errors[$i]->getMessage();
            }




            return ['faild'=>true,'errors'=>$err];
        }

        return ['faild'=>false];
    }
}