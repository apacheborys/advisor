<?php
declare(strict_types=1);

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class DefaultController extends AbstractController
{
    public function index(): JsonResponse
    {
        return new JsonResponse(['error' => 'No route here'], Response::HTTP_NOT_FOUND);
    }
}
