<?php

namespace App\Entity;

use App\Repository\ChatRoomMembersRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ChatRoomMembersRepository::class)]
class ChatRoomMembers
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\ManyToOne(targetEntity: ChatRoom::class)]
    #[ORM\JoinColumn(nullable: false,onDelete: "CASCADE")]
    private $chat_room;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: false,onDelete: "CASCADE")]
    private $member;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getChatRoom(): ?ChatRoom
    {
        return $this->chat_room;
    }

    public function setChatRoom(?ChatRoom $chat_room): self
    {
        $this->chat_room = $chat_room;

        return $this;
    }

    public function getMember(): ?User
    {
        return $this->member;
    }

    public function setMember(?User $member): self
    {
        $this->member = $member;

        return $this;
    }
}
