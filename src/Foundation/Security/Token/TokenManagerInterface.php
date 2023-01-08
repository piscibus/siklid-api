<?php

declare(strict_types=1);

namespace App\Foundation\Security\Token;

use App\Siklid\Application\Auth\Request\LogoutRequest;
use App\Siklid\Application\Contract\Entity\UserInterface as SiklidUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Token manager interface.
 * All access token managers must implement this interface.
 */
interface TokenManagerInterface
{
    /**
     * Creates a new token for the given user.
     *
     * @param UserInterface $user the user to create a token for
     */
    public function createAccessToken(UserInterface $user): AccessTokenInterface;

    public function revokeAccessToken(SiklidUserInterface $user, string $token): bool;

    public function deleteRefreshToken(LogoutRequest $request): bool;
}
