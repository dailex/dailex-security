<?php

namespace Dlx\Security\User\Domain\Command;

use Daikon\Entity\ValueObject\Email;
use Daikon\Entity\ValueObject\Text;
use Daikon\EventSourcing\Aggregate\AggregateId;
use Daikon\EventSourcing\Aggregate\AggregateIdInterface;
use Daikon\EventSourcing\Aggregate\Command\Command;
use Daikon\MessageBus\MessageInterface;
use Dlx\Security\User\Domain\User;

final class UpdateUser extends Command
{
    private $username;

    private $email;

    private $firstname;

    private $lastname;

    private $locale;

    public static function getAggregateRootClass(): string
    {
        return User::class;
    }

    public static function fromArray(array $nativeValues): MessageInterface
    {
        return new self(
            AggregateId::fromNative($nativeValues['aggregateId']),
            Text::fromNative($nativeValues['username']),
            Email::fromNative($nativeValues['email']),
            Text::fromNative($nativeValues['firstname']),
            Text::fromNative($nativeValues['lastname']),
            Text::fromNative($nativeValues['locale'])
        );
    }

    public function getUsername(): Text
    {
        return $this->username;
    }

    public function getEmail(): Email
    {
        return $this->email;
    }

    public function getFirstname(): Text
    {
        return $this->firstname;
    }

    public function getLastname(): Text
    {
        return $this->lastname;
    }

    public function getLocale(): Text
    {
        return $this->locale;
    }

    public function toArray(): array
    {
        return array_merge(
            [
                'username' => $this->username->toNative(),
                'email' => $this->email->toNative(),
                'firstname' => $this->firstname->toNative(),
                'lastname' => $this->lastname->toNative(),
                'locale' => $this->locale->toNative()
            ],
            parent::toArray()
        );
    }

    protected function __construct(
        AggregateId $aggregateId,
        Text $username,
        Email $email,
        Text $firstname,
        Text $lastname,
        Text $locale
    ) {
        parent::__construct($aggregateId);
        $this->username = $username;
        $this->email = $email;
        $this->firstname = $firstname;
        $this->lastname = $lastname;
        $this->locale = $locale;
    }
}
