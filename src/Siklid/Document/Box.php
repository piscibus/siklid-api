<?php

declare(strict_types=1);

namespace App\Siklid\Document;

use App\Foundation\Exception\LogicException;
use App\Foundation\Util\ClockFactory;
use App\Siklid\Application\Contract\Entity\BoxInterface;
use App\Siklid\Application\Contract\Entity\UserInterface;
use App\Siklid\Application\Contract\Type\RepetitionAlgorithm;
use App\Siklid\Repository\BoxRepository;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use StellaMaris\Clock\ClockInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @psalm-suppress MissingConstructor
 * @psalm-suppress PropertyNotSetInConstructor
 */
#[MongoDB\Document(collection: 'boxes', repositoryClass: BoxRepository::class)]
#[MongoDB\HasLifecycleCallbacks]
class Box implements BoxInterface
{
    #[MongoDB\Id]
    #[Groups(['box:read', 'box:delete', 'box:create', 'box:index'])]
    private string $id;

    #[MongoDB\Field(type: 'string')]
    #[Assert\NotBlank]
    #[Groups(['box:read', 'box:create', 'box:index'])]
    private string $name;

    #[MongoDB\Field(type: 'specific')]
    #[Groups(['box:read', 'box:create', 'box:index'])]
    private RepetitionAlgorithm $repetitionAlgorithm = RepetitionAlgorithm::Leitner;

    #[MongoDB\Field(type: 'string', nullable: true)]
    #[Groups(['box:read', 'box:create', 'box:index'])]
    private ?string $description = null;

    #[MongoDB\ReferenceMany(targetDocument: Flashcard::class)]
    #[Groups(['box:read'])]
    private Collection $flashcards;

    #[MongoDB\Field(type: 'collection')]
    #[Groups(['box:read', 'box:create', 'box:index'])]
    private array $hashtags = [];

    #[MongoDB\ReferenceOne(targetDocument: User::class)]
    #[Groups(['box:read', 'box:index'])]
    private UserInterface $user;

    #[MongoDB\Field(type: 'date_immutable')]
    #[Groups(['box:read', 'box:create', 'box:index'])]
    private DateTimeImmutable $createdAt;

    #[MongoDB\Field(type: 'date_immutable')]
    #[Groups(['box:read'])]
    private DateTimeImmutable $updatedAt;

    #[MongoDB\Field(type: 'date_immutable', nullable: true)]
    #[Groups(['box:read', 'box:delete'])]
    private ?DateTimeImmutable $deletedAt = null;

    private ClockInterface $clock;

    public function __construct(?ClockInterface $clock = null)
    {
        $this->clock = $clock ?? ClockFactory::create();

        $this->createdAt = $this->clock->now();
        $this->updatedAt = $this->clock->now();
        $this->flashcards = new ArrayCollection();
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function setId(string $id): BoxInterface
    {
        $this->id = $id;

        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): BoxInterface
    {
        $this->name = $name;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): BoxInterface
    {
        $this->description = $description;

        return $this;
    }

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(DateTimeImmutable $createdAt): BoxInterface
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(DateTimeImmutable $updatedAt): BoxInterface
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function getUser(): UserInterface
    {
        return $this->user;
    }

    public function setUser(UserInterface $user): BoxInterface
    {
        $this->user = $user;

        return $this;
    }

    public function getRepetitionAlgorithm(): RepetitionAlgorithm
    {
        return $this->repetitionAlgorithm;
    }

    public function setRepetitionAlgorithm(RepetitionAlgorithm $repetitionAlgorithm): Box
    {
        $this->repetitionAlgorithm = $repetitionAlgorithm;

        return $this;
    }

    public function getDeletedAt(): ?DateTimeImmutable
    {
        return $this->deletedAt;
    }

    public function setDeletedAt(?DateTimeImmutable $deletedAt): Box
    {
        $this->deletedAt = $deletedAt;

        return $this;
    }

    public function getFlashcards(): Collection
    {
        return $this->flashcards;
    }

    public function setFlashcards(Collection $flashcards): Box
    {
        $this->flashcards = $flashcards;

        return $this;
    }

    public function getHashtags(): Collection
    {
        return new ArrayCollection($this->hashtags);
    }

    public function setHashtags(Collection|array $hashtags): Box
    {
        $this->hashtags = $hashtags instanceof Collection ? $hashtags->toArray() : $hashtags;

        return $this;
    }

    public function delete(): void
    {
        if (null === $this->deletedAt) {
            $this->deletedAt = $this->clock->now();

            return;
        }

        throw new LogicException('Box is already deleted');
    }

    #[MongoDB\PrePersist]
    #[MongoDB\PreUpdate]
    public function touch(): void
    {
        $this->updatedAt = $this->clock->now();
    }

    public function isDeleted(): bool
    {
        return null !== $this->deletedAt;
    }

    #[MongoDB\PostLoad]
    public function setClock(?ClockInterface $clock = null): void
    {
        $this->clock = $clock ?? ClockFactory::create();
    }
}
