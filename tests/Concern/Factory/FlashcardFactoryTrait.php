<?php

declare(strict_types=1);

namespace App\Tests\Concern\Factory;

use App\Siklid\Document\Flashcard;
use App\Siklid\Document\User;

/**
 * This trait is used to create flashcards for testing purposes.
 */
trait FlashcardFactoryTrait
{
    use FactoryConcern;

    protected function makeFlashcard(array $attributes = []): Flashcard
    {
        $flashcard = new Flashcard();

        $flashcard->setFront($attributes['front'] ?? $this->faker()->sentence());
        $flashcard->setBack($attributes['back'] ?? $this->faker()->sentence());
        $flashcard->setUser($attributes['user'] ?? new User());

        $this->touchCollection(Flashcard::class);

        return $flashcard;
    }
}
