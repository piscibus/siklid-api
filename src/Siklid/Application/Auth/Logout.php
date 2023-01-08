<?php

declare(strict_types=1);

namespace App\Siklid\Application\Auth;

use App\Foundation\Action\AbstractAction;
use App\Foundation\Security\Token\TokenManagerInterface as TokenManager;
use App\Siklid\Application\Auth\Request\LogoutRequest;
use App\Siklid\Application\Contract\Entity\UserInterface as SiklidUserInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Security\Authenticator\Token\JWTPostAuthenticationToken;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface as TokenStorage;

final class Logout extends AbstractAction
{
    private TokenManager $tokenManager;

    private TokenStorage $token;

    private LogoutRequest $request;

    public function __construct(TokenManager $tokenManager, TokenStorage $token, LogoutRequest $request)
    {
        $this->tokenManager = $tokenManager;
        $this->token = $token;
        $this->request = $request;
    }

    /**
     * Executes action.
     */
    public function execute(): bool
    {
        $this->tokenManager->deleteRefreshToken($this->request);

        $tokenWithBearer = (string)$this->request->request()->headers->get('Authorization');

        $userToken = $this->token->getToken();

        assert($userToken instanceof JWTPostAuthenticationToken);

        $user = $userToken->getUser();
        assert($user instanceof SiklidUserInterface);
        $this->tokenManager->revokeAccessToken($user, $tokenWithBearer);

        return true;
    }
}
