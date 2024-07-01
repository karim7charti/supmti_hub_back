<?php


namespace App\Service\Likes;


use App\Entity\Like;
use App\Repository\ActivityRepository;
use App\Repository\LikeRepository;
use App\Service\Notifications\NotificationSevice;
use App\Service\RealtimeServices\RealtimeLikeService;
use App\Service\UserServices\CurrentUser;
use Symfony\Component\Mercure\Update;

class LikeService
{

    public function __construct(private NotificationSevice $notificationSevice,
                                private CurrentUser $user,
                                private RealtimeLikeService $realtimeLikeService,
                                private LikeRepository $likeRepository,
                                private ActivityRepository $activityRepository,
    )
    {
    }

    public function like(int $activityId,int $owner,string $path,$pubId):int{
        $user=$this->user->getUser();
        $already_liked=$this->likeRepository->did_like($user->getId(),$activityId);
        if(!$already_liked)
        {
            $activity=$this->activityRepository->find($activityId);
            if($activity) {
                $like = new Like();
                $like->setActivity($activity)->setUser($user)->setCreatedAt(new \DateTimeImmutable());
                $this->likeRepository->add($like);
                if($user->getId()!=$owner)
                {
                    $type="LIKE";
                    $pub=$activity;
                    if($pubId)
                    {
                        $type="COMMENT_LIKE";
                        $pub=$this->activityRepository->find($pubId);
                    }
                    $this->notificationSevice->notify(
                        $user,
                        targetId: $owner, type: $type, activity: $pub
                        , path: $path
                    );
                }

                $this->realtimeLikeService->push($activityId,1);

                return 201;

            }
            else
                return 404;

        }
        return 201;

    }
    public function dislike(int $activityId):int
    {
        $user=$this->user->getUser();
        $already_liked=$this->likeRepository->did_like($user->getId(),$activityId);
        if($already_liked)
        {
            $activity=$this->activityRepository->find($activityId);
            if($activity)
            {
                $like=$this->likeRepository->findOneBy(['user'=>$user,'activity'=>$activity]);
                $this->likeRepository->remove($like);
                $this->realtimeLikeService->push($activityId,-1);
                return 200;
            }
            else
                return 404;

        }
        return 200;
    }
}