<?php


namespace App\Service\AdminServices;


use App\Repository\PostRepository;
use App\Repository\UserRepository;

class AnalyticsService
{
    public function __construct(private UserRepository $userRepository,
                                )
    {
    }

    public function get_dashboard_data():array
    {
        $countStudents=$this->userRepository->getUsersCount("ROLE_ETUDIANT","");
        $countTeachers=$this->userRepository->getUsersCount("ROLE_ENSEIGNANT","");

        return [
            "students"=>$countStudents,
            'teachers'=>$countTeachers
        ];
    }

}