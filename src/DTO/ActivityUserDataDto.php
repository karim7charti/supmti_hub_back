<?php


namespace App\DTO;


use App\Entity\User;

class ActivityUserDataDto
{
    private $id;

    private $email;

    private $roles = [];

    private $first_name;

    private $last_name;

    private $profile_image_path;

    public function __construct(User $user)

    {
        $this->id=$user->getId();
        $this->email=$user->getEmail();
        $this->last_name=$user->getLastName();
        $this->first_name=$user->getFirstName();
        $this->roles=$user->getRoles();
        $this->profile_image_path=$user->getProfileImagePath();
    }

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @param int|null $id
     */
    public function setId(?int $id): void
    {
        $this->id = $id;
    }

    /**
     * @return string|null
     */
    public function getEmail(): ?string
    {
        return $this->email;
    }

    /**
     * @param string|null $email
     */
    public function setEmail(?string $email): void
    {
        $this->email = $email;
    }

    /**
     * @return array
     */
    public function getRoles(): array
    {
        return $this->roles;
    }

    /**
     * @param array $roles
     */
    public function setRoles(array $roles): void
    {
        $this->roles = $roles;
    }

    /**
     * @return string|null
     */
    public function getFirstName(): ?string
    {
        return $this->first_name;
    }

    /**
     * @param string|null $first_name
     */
    public function setFirstName(?string $first_name): void
    {
        $this->first_name = $first_name;
    }

    /**
     * @return string|null
     */
    public function getLastName(): ?string
    {
        return $this->last_name;
    }

    /**
     * @param string|null $last_name
     */
    public function setLastName(?string $last_name): void
    {
        $this->last_name = $last_name;
    }

    /**
     * @return string|null
     */
    public function getProfileImagePath(): ?string
    {
        return $this->profile_image_path;
    }

    /**
     * @param string|null $profile_image_path
     */
    public function setProfileImagePath(?string $profile_image_path): void
    {
        $this->profile_image_path = $profile_image_path;
    }




}