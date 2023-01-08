<?php

declare(strict_types=1);

namespace App\Foundation\Security\Token;

use App\Foundation\Redis\Contract\SetInterface;
use App\Siklid\Application\Auth\Request\LogoutRequest;
use App\Siklid\Application\Contract\Entity\UserInterface as SiklidUserInterface;
use App\Siklid\Document\AccessToken;
use App\Siklid\Document\RefreshToken;
use Doctrine\ODM\MongoDB\DocumentManager;
use Gesdinet\JWTRefreshTokenBundle\Document\RefreshTokenRepository;
use Gesdinet\JWTRefreshTokenBundle\Generator\RefreshTokenGeneratorInterface;
use Gesdinet\JWTRefreshTokenBundle\Model\RefreshTokenManagerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;

use function PHPUnit\Framework\assertNotNull;

use Symfony\Component\Security\Core\User\UserInterface;

/**
 * The default token manager.
 */
class TokenManager implements TokenManagerInterface
{
    private JWTTokenManagerInterface $JWTTokenManager;

    private RefreshTokenGeneratorInterface $refreshTokenGenerator;

    private RefreshTokenManagerInterface $refreshTokenManager;

    private DocumentManager $dm;

    private SetInterface $set;

    public function __construct(
        JWTTokenManagerInterface $JWTTokenManager,
        RefreshTokenGeneratorInterface $refreshTokenGenerator,
        RefreshTokenManagerInterface $refreshTokenManager,
        DocumentManager $dm,
        SetInterface $set,
    ) {
        $this->JWTTokenManager = $JWTTokenManager;
        $this->refreshTokenGenerator = $refreshTokenGenerator;
        $this->refreshTokenManager = $refreshTokenManager;
        $this->dm = $dm;
        $this->set = $set;
    }

    public function createAccessToken(UserInterface $user): AccessTokenInterface
    {
        $token = $this->JWTTokenManager->create($user);
        $accessToken = new AccessToken($token);

        $refreshToken = $this->refreshTokenGenerator->createForUserWithTtl($user, 2592000);
        $this->refreshTokenManager->save($refreshToken);

        $accessToken->setRefreshToken($refreshToken);

        return $accessToken;
    }

    public function revokeAccessToken(SiklidUserInterface $user, string $token): bool
    {
        $userId = $user->getId();

        $setKey = 'user.'.$userId.'.accessToken';
        $this->set->add($setKey, $token);
        $this->set->setTtl($setKey, time() + (60 * 60));

        return true;
    }

    public function deleteRefreshToken(LogoutRequest $request): bool
    {
        $refreshTokenRepository = $this->dm->getRepository(RefreshToken::class);

        assert($refreshTokenRepository instanceof RefreshTokenRepository);

        $refreshTokenVal = (string)$request->get('refreshToken');
        $refreshTokenObject = $refreshTokenRepository->findOneBy(['refreshToken' => $refreshTokenVal]);
        assertNotNull($refreshTokenObject);

        $this->dm->remove($refreshTokenObject);
        $this->dm->flush();

        return true;
    }
}
