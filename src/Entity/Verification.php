<?php

declare(strict_types=1);

namespace App\Entity;

use App\Component\DTO\Database\VerificationSubjectDTO;
use App\Component\DTO\Database\VerificationUserInfoDTO;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Doctrine\UuidGenerator;
use Ramsey\Uuid\UuidInterface;

#[ORM\Entity]
class Verification
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: UuidGenerator::class)]
    private ?UuidInterface $id = null;

    #[ORM\Column(type: 'json')]
    private VerificationSubjectDTO $subject;

    #[ORM\Column(type: 'boolean')]
    private bool $confirmed = false;

    #[ORM\Column(type: 'string', length: 8)]
    private string $code;

    #[ORM\Column(type: 'json')]
    private VerificationUserInfoDTO $userInfo;

    public function getId(): ?UuidInterface
    {
        return $this->id;
    }

    public function getSubject(): VerificationSubjectDTO
    {
        return $this->subject;
    }

    public function setSubject(VerificationSubjectDTO $subject): self
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

    public function getUserInfo(): VerificationUserInfoDTO
    {
        return $this->userInfo;
    }

    public function setUserInfo(VerificationUserInfoDTO $userInfo): self
    {
        $this->userInfo = $userInfo;

        return $this;
    }
}
