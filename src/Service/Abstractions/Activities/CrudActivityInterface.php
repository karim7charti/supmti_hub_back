<?php


namespace App\Service\Abstractions\Activities;


interface CrudActivityInterface
{

    public function create($data):mixed;
    /*
     * get all activities
     * params :max resultes count,page num for paginations
     * returns array of activities
     * */
    public function getAll(int $maxResult,int $pageNum):array;

    /*
     * get specific user  activities
     * params :max resultes count,page num for paginations ,id for user id
     * returns array of activities
     * */
    public function getById(int $maxResult,int $pageNum,int $id):array;

    /*
     * get one  activity by its id
     * params :max resultes count,page num for paginations ,id for activty id
     * returns array of activities
     * */
    public function getOneActivity(int $activity_id);
    //public function delete();
}