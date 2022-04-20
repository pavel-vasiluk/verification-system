<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\VerificationRepository;
use Carbon\Carbon;
use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Doctrine\UuidGenerator;
use Ramsey\Uuid\UuidInterface;

#[ORM\Entity(repositoryClass: VerificationRepository::class)]
class Verification
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: UuidGenerator::class)]
    private ?UuidInterface $id = null;

    #[ORM\Column(type: 'json')]
    private array $subject;

    #[ORM\Column(type: 'boolean')]
    private bool $confirmed = false;

    #[ORM\Column(type: 'string', length: 8)]
    private string $code;

    #[ORM\Column(type: 'json')]
    private array $userInfo;

    #[ORM\Column(type: 'datetime')]
    private DateTime $createdAt;

    public function __construct()
    {
        $this->createdAt = Carbon::now();
    }

    public function getId(): ?UuidInterface
    {
        return $this->id;
    }

    public function getSubject(): array
    {
        return $this->subject;
    }

    public function setSubject(array $subject): self
    {
        $this->subject = $subject;

        return $this;
    }

    public function isConfirmed(): bool
    {
        return $this->confirmed;
    }

    public function setConfirmed(bool $confirmed): self
    {
        $this->confirmed = $confirmed;

        return $this;
    }

    public function getCode(): string
    {
        return $this->code;
    }

    public function setCode(string $code): self
    {
        $this->code = $code;

        return $this;
    }

    public function getUserInfo(): array
    {
        return $this->userInfo;
    }

    public function setUserInfo(array $userInfo): self
    {
        $this->userInfo = $userInfo;

        return $this;
    }

    public function getCreatedAt(): DateTime
    {
        return $this->createdAt;
    }
}
