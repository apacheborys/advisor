<?php
declare(strict_types=1);

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class BasicController extends AbstractController
{
    private string $projectDir;

    public function __construct(string $projectDir)
    {
        $this->projectDir = $projectDir;
    }

    public function index(): JsonResponse
    {
        return new JsonResponse(['error' => 'No route here'], Response::HTTP_NOT_FOUND);
    }

    public function getSwaggerDoc(): Response
    {
        $address = implode(DIRECTORY_SEPARATOR, [$this->projectDir, 'resources', 'Swagger', 'swagger.json']);

        return new Response(file_get_contents($address));
    }
}
