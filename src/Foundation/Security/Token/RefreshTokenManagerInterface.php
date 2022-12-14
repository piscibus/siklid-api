<?php

declare(strict_types=1);

namespace App\Foundation\Security\Token;

use Gesdinet\JWTRefreshTokenBundle\Model\RefreshTokenInterface as RefreshToken;
use Symfony\Component\Security\Core\User\UserInterface;

interface RefreshTokenManagerInterface
{
    public const CONFIGURED_TTL = 0;

    public function createForUser(UserInterface $user, int $ttl): RefreshToken;
}
