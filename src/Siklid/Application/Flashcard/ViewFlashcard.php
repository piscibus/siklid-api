<?php

declare(strict_types=1);

namespace App\Siklid\Application\Flashcard;

use App\Foundation\Action\AbstractAction;
use App\Foundation\Exception\SiklidException;
use App\Siklid\Application\Contract\Entity\FlashCardInterface;

class ViewFlashcard extends AbstractAction
{
    private ?FlashCardInterface $flashCard;

    public function __construct(?FlashCardInterface $flashCard = null)
    {
        $this->flashCard = $flashCard;
    }

    public function execute(): FlashCardInterface
    {
        if (null === $this->flashCard) {
            throw new SiklidException('Flashcard is not set');
        }

        return $this->flashCard;
    }

    public function setFlashcard(FlashCardInterface $flashCard): self
    {
        $this->flashCard = $flashCard;

        return $this;
    }
}
