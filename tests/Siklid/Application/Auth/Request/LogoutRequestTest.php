<?php

declare(strict_types=1);

namespace App\Tests\Siklid\Application\Auth\Request;

use App\Foundation\Util\Json;
use App\Foundation\Util\RequestUtil;
use App\Foundation\Validation\ValidatorInterface;
use App\Siklid\Application\Auth\Request\LogoutRequest;
use App\Tests\Concern\Util\WithFaker;
use App\Tests\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

class LogoutRequestTest extends TestCase
{
    use WithFaker;

    /**
     * @test
     */
    public function get_refresh_token(): void
    {
        $refreshToken = $this->faker()->md5();
        $internalRequest = new Request([], ['refreshToken' => $refreshToken]);
        $requestStack = new RequestStack();
        $requestStack->push($internalRequest);

        $sut = new LogoutRequest(
            $requestStack,
            new RequestUtil(new Json(), $this->createMock(ValidatorInterface::class))
        );

        $this->assertSame($refreshToken, $sut->refreshToken());
    }

    /**
     * @test
     */
    public function get_access_token(): void
    {
        $accessToken = $this->faker()->md5();
        $internalRequest = new Request([], [], [], [], [], ['HTTP_AUTHORIZATION' => $accessToken]);
        $requestStack = new RequestStack();
        $requestStack->push($internalRequest);

        $sut = new LogoutRequest(
            $requestStack,
            new RequestUtil(new Json(), $this->createMock(ValidatorInterface::class))
        );

        $this->assertSame($accessToken, $sut->getAccessToken());
    }
}
