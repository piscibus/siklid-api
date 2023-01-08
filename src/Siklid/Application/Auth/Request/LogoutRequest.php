<?php

declare(strict_types=1);

namespace App\Siklid\Application\Auth\Request;

use App\Foundation\Http\Request;
use App\Foundation\Validation\Constraint\Exists;
use App\Siklid\Document\RefreshToken;
use Symfony\Component\Validator\Constraints as Assert;

final class LogoutRequest extends Request
{
    public function refreshToken(): string
    {
        return (string)$this->get('refreshToken');
    }

    public function getAccessToken(): string
    {
        return (string)$this->getHeader('Authorization');
    }

    protected function constraints(): array
    {
        $notBlank = new Assert\NotBlank();
        $exist = new Exists(RefreshToken::class, 'refreshToken');

        return [
            'refreshToken' => [$notBlank, $exist],
        ];
    }
}
