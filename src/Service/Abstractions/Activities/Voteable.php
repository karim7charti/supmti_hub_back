<?php


namespace App\Service\Abstractions\Activities;


interface Voteable
{
        public function vote(int $answer_id,int $activity_id):void;
}