<?php

declare(strict_types=1);

namespace App\Tests\Feature\Flashcard;

use App\Siklid\Document\Flashcard;
use App\Tests\Concern\Factory\FlashcardFactoryTrait;
use App\Tests\Concern\WebTestCaseTrait;
use App\Tests\TestCase;

class ViewFlashcardTest extends TestCase
{
    use WebTestCaseTrait;
    use FlashcardFactoryTrait;

    /**
     * @test
     */
    public function user_can_show_single_flashcard(): void
    {
        $client = $this->makeClient();
        $user = $this->makeUser();
        $flashcard = $this->makeFlashcard(['front' => 'front', 'back' => 'back', 'user' => $user]);
        $client->loginUser($user);

        $client->request('GET', '/api/v1/flashcard/'.$flashcard->getId());

        $this->assertExists(Flashcard::class, ['id' => $flashcard->getId()]);
    }
}
