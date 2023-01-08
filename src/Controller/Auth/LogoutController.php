<?php

declare(strict_types=1);

namespace App\Controller\Auth;

use App\Foundation\Http\ApiController;
use App\Siklid\Application\Auth\Logout;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class LogoutController extends ApiController
{
    #[Route('/auth/logout', name: 'auth_logout', methods: ['POST'])]
    public function logout(Logout $action): Response
    {
        $action->execute();

        $data = [
            'message' => 'You have been logged out.',
        ];

        return $this->json($data, Response::HTTP_OK);
    }
}
