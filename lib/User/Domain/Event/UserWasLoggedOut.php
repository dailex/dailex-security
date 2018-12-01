<?php

namespace Dlx\Security\User\Domain\Event;

use Daikon\Entity\ValueObject\Timestamp;
use Daikon\Entity\ValueObject\Uuid;
use Daikon\EventSourcing\Aggregate\AggregateId;
use Daikon\EventSourcing\Aggregate\AggregateRevision;
use Daikon\EventSourcing\Aggregate\Command\CommandInterface;
use Daikon\EventSourcing\Aggregate\Event\DomainEvent;
use Daikon\EventSourcing\Aggregate\Event\DomainEventInterface;
use Daikon\MessageBus\MessageInterface;
use Dlx\Security\User\Domain\Command\LogoutUser;
use Dlx\Security\User\Domain\ValueObject\RandomToken;

final class UserWasLoggedOut extends DomainEvent
{
    private $authTokenId;

    private $authToken;

    private $authTokenExpiresAt;

    public static function viaCommand(LogoutUser $logoutUser): self
    {
        return new self(
            $logoutUser->getAggregateId(),
            $logoutUser->getAuthTokenId(),
            $logoutUser->getAuthToken(),
            $logoutUser->getAuthTokenExpiresAt()
        );
    }

    /** @param array $payload */
    public static function fromNative($payload): MessageInterface
    {
        return new self(
            AggregateId::fromNative($payload['aggregateId']),
            Uuid::fromNative($payload['authTokenId']),
            RandomToken::fromNative($payload['authToken']),
            Timestamp::fromNative($payload['authTokenExpiresAt']),
            AggregateRevision::fromNative($payload['aggregateRevision'])
        );
    }

    public function conflictsWith(DomainEventInterface $otherEvent): bool
    {
        return false;
    }

    public function getAuthTokenId(): Uuid
    {
        return $this->authTokenId;
    }

    public function getAuthToken(): RandomToken
    {
        return $this->authToken;
    }

    public function getAuthTokenExpiresAt(): Timestamp
    {
        return $this->authTokenExpiresAt;
    }

    public function toNative(): array
    {
        return array_merge([
            'authTokenId' => $this->authTokenId->toNative(),
            'authToken' => $this->authToken->toNative(),
            'authTokenExpiresAt' => $this->authTokenExpiresAt->toNative()
        ], parent::toNative());
    }

    protected function __construct(
        AggregateId $aggregateId,
        Uuid $authTokenId,
        RandomToken $authToken,
        Timestamp $authTokenExpiresAt,
        AggregateRevision $aggregateRevision = null
    ) {
        parent::__construct($aggregateId, $aggregateRevision);
        $this->authTokenId = $authTokenId;
        $this->authToken = $authToken;
        $this->authTokenExpiresAt = $authTokenExpiresAt;
    }
}
