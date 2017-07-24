<?php

namespace Dlx\Security\User\Domain\Event;

use Assert\Assertion;
use Daikon\Entity\ValueObject\Timestamp;
use Daikon\Entity\ValueObject\Uuid;
use Daikon\EventSourcing\Aggregate\AggregateId;
use Daikon\EventSourcing\Aggregate\AggregateIdInterface;
use Daikon\EventSourcing\Aggregate\AggregateRevision;
use Daikon\EventSourcing\Aggregate\Command\CommandInterface;
use Daikon\EventSourcing\Aggregate\Event\DomainEvent;
use Daikon\EventSourcing\Aggregate\Event\DomainEventInterface;
use Daikon\MessageBus\MessageInterface;
use Dlx\Security\User\Domain\Command\RegisterUser;
use Dlx\Security\User\Domain\User;
use Dlx\Security\User\Domain\ValueObject\RandomToken;

final class AuthTokenWasAdded extends DomainEvent
{
    private $id;

    private $token;

    private $expiresAt;

    public static function viaCommand(CommandInterface $registerUser): self
    {
        Assertion::isInstanceOf($registerUser, RegisterUser::class);

        return new self(
            Uuid::generate(),
            $registerUser->getAggregateId(),
            RandomToken::generate(),
            Timestamp::fromNative(gmdate(Timestamp::NATIVE_FORMAT, strtotime('+1 month')))
        );
    }

    public static function fromArray(array $nativeArray): MessageInterface
    {
        return new self(
            Uuid::fromNative($nativeArray['id']),
            AggregateId::fromNative($nativeArray['aggregateId']),
            AuthToken::fromNative($nativeArray['token']),
            Timestamp::fromNative($nativeArray['expires_at'])
        );
    }

    public static function getAggregateRootClass(): string
    {
        return User::class;
    }

    public function conflictsWith(DomainEventInterface $otherEvent): bool
    {
        return false;
    }

    public function getId(): Uuid
    {
        return $this->id;
    }

    public function getToken(): RandomToken
    {
        return $this->token;
    }

    public function getExpiresAt(): Timestamp
    {
        return $this->expiresAt;
    }

    public function toArray(): array
    {
        return array_merge([
            'id' => $this->id->toNative(),
            'token' => $this->token->toNative(),
            'expires_at' => $this->expiresAt->toNative()
        ], parent::toArray());
    }

    protected function __construct(
        Uuid $id,
        AggregateIdInterface $aggregateId,
        RandomToken $token,
        Timestamp $expiresAt,
        AggregateRevision $aggregateRevision = null
    ) {
        parent::__construct($aggregateId, $aggregateRevision);
        $this->id = $id;
        $this->token = $token;
        $this->expiresAt = $expiresAt;
    }
}
