<?php

declare(strict_types=1);

namespace App\Foundation\Exception;

use Symfony\Component\HttpFoundation\Response;

interface RenderableInterface
{
    /**
     * Get the response that should be returned.
     */
    public function render(): Response;
}