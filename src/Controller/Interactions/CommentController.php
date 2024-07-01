<?php

namespace App\Controller\Interactions;

use App\Forms\FileComment;
use App\Repository\ActivityRepository;
use App\Service\Comments\CommentService;
use App\Service\Files\FileUploader;
use App\Validations\Validator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;


#[Route("/api/comments")]
class CommentController extends AbstractController
{
    public function __construct(private CommentService $commentService)
    {
    }

    #[Route('/comment/{activity_id}',methods: ["POST"])]
    public function comment(Request $request,int $activity_id,
                            ValidatorInterface $validatorInt,
                             Validator $validator,
                            ActivityRepository $activityRepository,
                            FileUploader $fileUploader):Response{

        $comment_on=$activityRepository->find($activity_id);
        if($comment_on==null)
            return new Response(status: 404);

        $type=$request->request->get("type");
        $on=$request->request->get("on");
        $owner=$request->request->get("owner");
        if($type=="text")
        {
            $body=$request->request->get("body");
            $body=strip_tags($body);
            if($body!="")
            {
                $this->commentService->comment($comment_on,$body,$type,$on,$owner);
            }
        }
        else
        {
            $body=$request->files->get("body");
            $commentFile=new FileComment();
            $commentFile->setCommentFile($body);
            $result=$validator->validate($validatorInt,$commentFile);
            if(!$result['faild'])
            {
                $fileUploader->setTargetDirectory($fileUploader->getTargetDirectory().'/commentFiles');
                $body=$fileUploader->upload($commentFile->getCommentFile());

                $this->commentService->comment($comment_on,$body,$type,$on,$owner);

            }
        }

        return new Response(status: 201);

    }
    #[Route('/comment/{activity_id}',methods: ["DELETE"])]
    public function removeComment(int $activity_id):Response{

        return new Response(status: $this->commentService->deleteComment($activity_id));

    }

    #[Route('/{activity_id}/{maxResult}/{pageNum}',methods: ["GET"])]
    public function getActivityComments(int $activity_id,int $maxResult,int $pageNum):Response{

        return $this->json($this->commentService->getActivtyComments($activity_id,$maxResult,$pageNum));
    }


}
