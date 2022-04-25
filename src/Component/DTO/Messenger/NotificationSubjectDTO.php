<?php

declare(strict_types=1);

namespace App\Component\DTO\Messenger;

use App\Component\DTO\AbstractDTO;
use App\Enums\ConfirmationTypes;
use App\Enums\ConfirmationTypeValidationGroup;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\GroupSequenceProviderInterface;

#[Assert\GroupSequenceProvider]
class NotificationSubjectDTO extends AbstractDTO implements GroupSequenceProviderInterface
{
    #[Assert\NotNull]
    #[Assert\Type(type: 'string')]
    #[Assert\Email(groups: [ConfirmationTypeValidationGroup::EMAIL_VALIDATION_GROUP])]
    #[Assert\Regex(
        pattern: '/^\\+[1-9]\\d{10,14}$/',
        match: true,
        groups: [ConfirmationTypeValidationGroup::MOBILE_VALIDATION_GROUP]
    )]
    protected $identity;

    #[Assert\NotNull]
    #[Assert\Type(type: 'string')]
    #[Assert\Choice(choices: ConfirmationTypes::CONFIRMATIONS, strict: true)]
    protected $type;

    #[Assert\NotNull]
    #[Assert\Type(type: 'string')]
    #[Assert\Type(type: 'digit')]
    protected $code;

    public function getIdentity(): string
    {
        return $this->identity;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getCode(): string
    {
        return $this->code;
    }

    public function getGroupSequence(): array
    {
        $sequences = ['NotificationSubjectDTO'];
        $typeValidationGroups = ConfirmationTypeValidationGroup::CONFIRMATION_TYPES_VALIDATION_GROUPS;

        if (is_string($this->type) && array_key_exists($this->type, $typeValidationGroups)) {
            $sequences[] = $typeValidationGroups[$this->type];
        }

        return [$sequences];
    }
}
