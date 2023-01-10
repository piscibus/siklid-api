<?php

declare(strict_types=1);

namespace App\Controller;

use App\Foundation\Http\ApiController;
use App\Siklid\Application\Flashcard\CreateFlashcard;
use App\Siklid\Application\Flashcard\ViewFlashcard;
use App\Siklid\Document\Flashcard;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class FlashcardController extends ApiController
{
    #[Route('/flashcards/{id}', name: 'flashcard_show', methods: ['GET'])]
    public function show(ViewFlashcard $action, Flashcard $flashcard): JsonResponse
    {
        return $this->ok($action->setFlashcard($flashcard)->execute(), ['box:show', 'resource:read']);
    }

    #[Route('/flashcards', name: 'flashcard_store', methods: ['POST'])]
    public function store(CreateFlashcard $action): JsonResponse
    {
        return $this->created($action->execute(), ['flashcard:create', 'resource:read']);
    }
}
