<?php
declare(strict_types=1);

namespace App\Controller;

use App\DTO\CreateAdvisorDTO;
use App\Entity\Advisor;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class AdvisorController extends AbstractController
{
    private EntityManagerInterface $entityManager;
    private ValidatorInterface $validator;

    public function __construct(EntityManagerInterface $entityManager, ValidatorInterface $validator)
    {
        $this->entityManager = $entityManager;
        $this->validator = $validator;
    }

    public function create(CreateAdvisorDTO $advisorDTO): JsonResponse
    {
        if ($responseWithErrors = $this->performValidation($advisorDTO)) {
            return $responseWithErrors;
        }

        $newAdvisor = Advisor::createFromDto($advisorDTO);
        $this->entityManager->persist($newAdvisor);
        $this->entityManager->flush();

        return new JsonResponse($newAdvisor->toArray());
    }

    private function performValidation(CreateAdvisorDTO $advisorDTO): ?JsonResponse
    {
        $result = [];
        $errors = $this->validator->validate($advisorDTO);
        if (count($errors) > 0) {
            $result[] = (string) $errors;
        }

        $errors = $this->validator->validate($advisorDTO->pricePerMinute);
        if (count($errors) > 0) {
            $result[] = (string) $errors;
        }

        foreach ($advisorDTO->languages as $language) {
            $errors = $this->validator->validate($language);
            if (count($errors) > 0) {
                $result[] = (string) $errors;
            }
        }

        return count($result) > 0 ? new JsonResponse(['errors' => $result], Response::HTTP_BAD_REQUEST) : null;
    }
}
