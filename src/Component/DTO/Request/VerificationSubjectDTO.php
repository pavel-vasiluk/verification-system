<?php

declare(strict_types=1);

namespace App\Component\DTO\Request;

use App\Component\DTO\AbstractDTO;
use App\Enums\ConfirmationTypes;
use App\Enums\ConfirmationTypeValidationGroup;
use JetBrains\PhpStorm\ArrayShape;
use JsonSerializable;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\GroupSequenceProviderInterface;

#[Assert\GroupSequenceProvider]
class VerificationSubjectDTO extends AbstractDTO implements JsonSerializable, GroupSequenceProviderInterface
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

    public function getIdentity(): string
    {
        return $this->identity;
    }

    public function getType(): string
    {
        return $this->type;
    }

    #[ArrayShape(['identity' => 'string', 'type' => 'string'])]
    public function jsonSerialize(): array
    {
        return [
            'identity' => $this->getIdentity(),
            'type' => $this->getType(),
        ];
    }

    public function getGroupSequence(): array
    {
        $sequences = ['VerificationSubjectDTO'];
        $typeValidationGroups = ConfirmationTypeValidationGroup::CONFIRMATION_TYPES_VALIDATION_GROUPS;

        if (is_string($this->type) && array_key_exists($this->type, $typeValidationGroups)) {
            $sequences[] = $typeValidationGroups[$this->type];
        }

        return [$sequences];
    }
}
