<?php

namespace App\Entity;

use App\Repository\UserPollVotesRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: UserPollVotesRepository::class)]
class UserPollVotes
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: false,onDelete: 'CASCADE')]
    private $user;

    #[ORM\ManyToOne(targetEntity: Poll::class)]
    #[ORM\JoinColumn(nullable: false,onDelete: 'CASCADE')]
    private $Poll;

    #[ORM\ManyToOne(targetEntity: PollAnswer::class)]
    #[ORM\JoinColumn(nullable: false,onDelete: 'CASCADE')]
    private $PollAnswer;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getPoll(): ?Poll
    {
        return $this->Poll;
    }

    public function setPoll(?Poll $Poll): self
    {
        $this->Poll = $Poll;

        return $this;
    }

    public function getPollAnswer(): ?PollAnswer
    {
        return $this->PollAnswer;
    }

    public function setPollAnswer(?PollAnswer $PollAnswer): self
    {
        $this->PollAnswer = $PollAnswer;

        return $this;
    }
}
