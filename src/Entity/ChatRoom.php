<?php

namespace App\Entity;

use App\Repository\ChatRoomRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ChatRoomRepository::class)]
class ChatRoom
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\OneToOne(targetEntity: Message::class, cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: true,onDelete: "CASCADE")]
    private $last_message;

    public function getLastMessage(): ?Message
    {
        return $this->last_message;
    }

    public function setLastMessage(?Message $last_message): self
    {
        $this->last_message = $last_message;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id): void
    {
        $this->id = $id;
    }





}
