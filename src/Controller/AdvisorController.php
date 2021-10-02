<?php
declare(strict_types=1);

namespace App\Controller;

use App\DTO\CreateAdvisorDTO;
use App\Entity\Advisor;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;

class AdvisorController extends AbstractController
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function create(CreateAdvisorDTO $advisorDTO): JsonResponse
    {
        $newAdvisor = Advisor::createFromDTO($advisorDTO);
        $this->entityManager->persist($newAdvisor);
        $this->entityManager->flush();

        var_dump($newAdvisor);

        return new JsonResponse();
    }
}
