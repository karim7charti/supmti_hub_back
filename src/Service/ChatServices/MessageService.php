<?php


namespace App\Service\ChatServices;


use App\Entity\ChatRoom;
use App\Entity\ChatRoomMembers;
use App\Entity\Message;

use App\Forms\FileMessage;
use App\Repository\ChatRoomMembersRepository;
use App\Repository\ChatRoomRepository;
use App\Repository\MessageRepository;
use App\Repository\UserRepository;
use App\Service\Files\FileUploader;
use App\Service\RealtimeServices\RealtimeMessagesService;
use App\Service\UserServices\CurrentUser;
use App\Validations\Validator;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class MessageService
{

    public function __construct(private CurrentUser $currentUser,
                                private ValidatorInterface $validatorInt,
                                private ChatRoomMembersRepository $chatRoomMembersRepository,
                                private ChatRoomRepository $chatRoomRepository,
                                private FileUploader $fileUploader,
                                private Validator $validator,
                                private RealtimeMessagesService $realtimeMessagesService,
                                private MessageRepository $messageRepository,
                                private UserRepository $userRepository,
    )
    {
    }

    public function send(Request $request):Response{
        $type=$request->request->get("type");
        $receiver_id=$request->request->get("receiver_id");
        $body="";
        if($type=="")
            return new Response("type non trouvé",400);
        else{
            if($type=="text")
            {
                $body=$request->request->get("body");
                if($body!="")
                {
                    $body=strip_tags($body);
                }
                else
                    return new Response("vous pouvez pas envoyé un message vide",400);


            }
            else{
                $message_file=$request->files->get("body");
                $message_file_oject=new FileMessage();
                $message_file_oject->setMessageFile($message_file);
                $result=$this->validator->validate($this->validatorInt,$message_file_oject);
                if(!$result['faild'])
                {
                    $this->fileUploader->setTargetDirectory(
                        $this->fileUploader->getTargetDirectory().'/messages_files');
                    $body=$this->fileUploader->upload($message_file_oject->getMessageFile());

                }
                else
                {
                    return new Response(json_encode($result),status: 400);
                }

            }

            $sender=$this->currentUser->getUser();
            $receiver=$this->userRepository->find($receiver_id);
            if($receiver)
            {

                $members_chat_roorm=$this->chatRoomMembersRepository
                    ->getMembersChatRoom($sender->getId(),$receiver->getId());

              $chat_room=null;
                if($members_chat_roorm==null)
                {
                    $chat_room=new ChatRoom();
                    $this->chatRoomRepository->add($chat_room);
                    $member1=new ChatRoomMembers();
                    $member1->setChatRoom($chat_room);
                    $member1->setMember($receiver);
                    $member2=new ChatRoomMembers();
                    $member2->setChatRoom($chat_room);
                    $member2->setMember($sender);
                    $this->chatRoomMembersRepository->add($member1,false);
                    $this->chatRoomMembersRepository->add($member2);

                }
                else
                    $chat_room=$this->chatRoomRepository->find($members_chat_roorm['id']);


                $message=new Message();
                $message->setBody($body)
                    ->setSeen(false)
                    ->setType($type)->setSender($sender)->setReceiver($receiver)
                    ->setCreatedAt(new \DateTimeImmutable())->setChatRoom($chat_room);
                $this->messageRepository->add($message);
                $chat_room->setLastMessage($message);
                $this->chatRoomRepository->add($chat_room);

                $this->realtimeMessagesService->push($message);
                $creeated_at=$message->getCreatedAt()->format("H:i d M Y");
                return new Response(json_encode($creeated_at),status: 201);
            }
            else
                return new Response("distinataire non trouvé",404);
        }

    }


    public function mark_as_seen(int $message_id):int{
        $message=$this->messageRepository->find($message_id);

        if($message)
        {
            $this->messageRepository->markSeen($message->getReceiver()->getId(),
                                                $message->getSender()->getId()
            );
            return 200;
        }
    }



}