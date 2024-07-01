<?php

namespace App\Entity;

use App\Repository\NotificationRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: NotificationRepository::class)]
class Notification
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: false,onDelete: "CASCADE")]
    private $target;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: false,onDelete: "CASCADE")]
    private $notifier;

    #[ORM\ManyToOne(targetEntity: NotificationType::class)]
    #[ORM\JoinColumn(nullable: false,onDelete: "CASCADE")]
    private $type;

    #[ORM\Column(type: 'string', length: 255)]
    private $path;

    #[ORM\ManyToOne(targetEntity: Activity::class)]
    #[ORM\JoinColumn(nullable: false,onDelete: "CASCADE")]
    private $activity;

    #[ORM\Column(type: 'boolean')]
    private $isSeen;

    #[ORM\Column(type: 'datetime_immutable')]
    private $created_at;

    #[ORM\Column(type: 'integer')]
    private $notifCount;



    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTarget(): ?User
    {
        return $this->target;
    }

    public function setTarget(?User $target): self
    {
        $this->target = $target;

        return $this;
    }

    public function getNotifier(): ?User
    {
        return $this->notifier;
    }

    public function setNotifier(?User $notifier): self
    {
        $this->notifier = $notifier;

        return $this;
    }

    public function getType(): ?NotificationType
    {
        return $this->type;
    }

    public function setType(?NotificationType $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getIsSeen(): ?bool
    {
        return $this->isSeen;
    }

    public function setIsSeen(bool $isSeen): self
    {
        $this->isSeen = $isSeen;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->created_at;
    }

    public function setCreatedAt(\DateTimeImmutable $created_at): self
    {
        $this->created_at = $created_at;

        return $this;
    }

    public function getPath(): ?string
    {
        return $this->path;
    }

    public function setPath(string $path): self
    {
        $this->path = $path;

        return $this;
    }

    public function getActivity(): ?Activity
    {
        return $this->activity;
    }

    public function setActivity(?Activity $activity): self
    {
        $this->activity = $activity;

        return $this;
    }

    public function getNotifCount(): ?int
    {
        return $this->notifCount;
    }

    public function setNotifCount(int $notifCount): self
    {
        $this->notifCount = $notifCount;

        return $this;
    }
}
