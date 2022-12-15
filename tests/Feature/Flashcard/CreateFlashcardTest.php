<?php

declare(strict_types=1);

namespace App\Tests\Feature\Flashcard;

use App\Siklid\Document\Flashcard;
use App\Tests\Concern\Factory\BoxFactoryTrait;
use App\Tests\Concern\WebTestCaseTrait;
use App\Tests\TestCase;

class CreateFlashcardTest extends TestCase
{
    use WebTestCaseTrait;
    use BoxFactoryTrait;

    /**
     * @test
     */
    public function flashcard_backside_and_boxes_are_required(): void
    {
        $client = $this->makeClient();
        $user = $this->makeUser();
        $box = $this->makeBox(['user' => $user]);
        $this->persistDocument($user);
        $this->persistDocument($box);
        $client->loginUser($user);

        $client->request('POST', '/api/v1/flashcards');

        $this->assertResponseHasValidationError('back', 'This field is missing.');
        $this->assertResponseHasValidationError('boxes', 'This field is missing.');
    }

    /**
     * @test
     */
    public function boxes_field_should_be_an_array(): void
    {
        $client = $this->makeClient();
        $user = $this->makeUser();
        $box = $this->makeBox(['user' => $user]);
        $this->persistDocument($user);
        $this->persistDocument($box);
        $client->loginUser($user);

        $client->request('POST', '/api/v1/flashcards', [
            'boxes' => 'not an array',
        ]);

        $this->assertResponseHasValidationError('boxes', 'This value should be of type array.');
    }

    /**
     * @test
     */
    public function boxes_field_should_contain_at_least_single_box_id(): void
    {
        $client = $this->makeClient();
        $user = $this->makeUser();
        $box = $this->makeBox(['user' => $user]);
        $this->persistDocument($user);
        $this->persistDocument($box);
        $client->loginUser($user);

        $client->request('POST', '/api/v1/flashcards', [
            'boxes' => [],
        ]);

        $this->assertResponseHasValidationError('boxes', 'This collection should contain 1 element or more.');
    }

    /**
     * @test
     */
    public function user_can_create_a_flashcard(): void
    {
        $client = $this->makeClient();
        $user = $this->makeUser();
        $this->persistDocument($user);
        $boxes = [];
        for ($i = 0; $i < 3; ++$i) {
            $box = $this->makeBox(['user' => $user]);
            $this->persistDocument($box);
            $boxes[] = $box;
        }
        $client->loginUser($user);
        $back = $this->faker->sentence();
        $front = $this->faker->sentence();

        $client->request('POST', '/api/v1/flashcards', [
            'back' => $back,
            'front' => $front,
            'boxes' => [
                $boxes[0]->getId(),
                $boxes[1]->getId(),
                $boxes[2]->getId(),
            ],
        ]);

        $this->assertResponseIsCreated();
        $this->assertResponseJsonStructure([
            'data' => [
                'id',
                'back',
                'front',
                'boxes',
                'user',
            ],
        ]);
        $this->assertExists(Flashcard::class, [
            'back' => $back,
            'front' => $front,
            'user' => $user,
        ]);
    }
}