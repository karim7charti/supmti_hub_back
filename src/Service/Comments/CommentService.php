<?php


namespace App\Service\Comments;


use App\Entity\Activity;
use App\Entity\Comment;
use App\Repository\ActivityRepository;
use App\Repository\CommentRepository;
use App\Service\Notifications\NotificationSevice;
use App\Service\RealtimeServices\RealtimeCommentsService;
use App\Service\UserServices\CurrentUser;


class CommentService
{
    public function __construct(private CommentRepository $commentRepository,
                                private CurrentUser $currentUser,
                                private  NotificationSevice $notificationSevice,
                                private RealtimeCommentsService $realtimeCommentsService,
                                private ActivityRepository $activityRepository,
    )
    {
    }

    public function comment(Activity $comment_on,string $body,string $type,string $on,int $activityOwner):void{
        $activty=new Activity();
        $this->activityRepository->add($activty);

            $user=$this->currentUser->getUser();
            $comment=new Comment();

            $comment->setUser($user)->setType($type)->setBody($body)->setCreatedAt(new \DateTimeImmutable())
                ->setCommentOn($comment_on)->setActivity($activty);
            $this->commentRepository->add($comment);
            if($user->getId()!=$activityOwner)
                $this->notificationSevice->notify(notifier: $user,
                targetId: $activityOwner,type: "COMMENT",activity: $comment_on,path: $on);

            $arr=[
                "id"=> $comment->getId(),
                "comment_on_id"=> $comment->getCommentOn()->getId(),
                "body"=>$comment->getBody(),
                "type"=> $comment->getType(),
                "activity_id"=> $comment->getActivity()->getId(),
                "user_id"=> $comment->getUser()->getId(),
                "profile_image_path"=>$comment->getUser()->getProfileImagePath(),
                "last_name"=> $comment->getUser()->getLastName(),
                "first_name"=> $comment->getUser()->getFirstName(),
                "liked"=>0,
                "count_likes"=>0
            ];

            $this->realtimeCommentsService->push_activity_comment_count($comment_on->getId());
            $this->realtimeCommentsService->push_comment($arr,$comment_on->getId());

    }

    public function getActivtyComments(int $activity_id,int $maxResult,int $pageNum){
        $user_id=$this->currentUser->getUser()->getId();
        $comments=$this->commentRepository->getActivityComments($activity_id,$maxResult,$pageNum,$user_id);

        return $comments;

    }
    public function deleteComment(int $activity_id):int{
        $activity=$this->activityRepository->find($activity_id);
        if($activity)
        {
            $this->activityRepository->remove($activity);
            return 200;
        }
        return 404;
    }
}