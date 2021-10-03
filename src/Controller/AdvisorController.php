<?php
declare(strict_types=1);

namespace App\Controller;

use App\DTO\CreateAdvisorDTO;
use App\DTO\UpdateAdvisorDTO;
use App\Entity\Advisor;
use App\Entity\AdvisorLanguage;
use App\Filter\GetAdvisorsFilter;
use App\Repository\AdvisorRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Ramsey\Uuid\Uuid;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class AdvisorController extends AbstractController
{
    private EntityManagerInterface $entityManager;
    private ValidatorInterface $validator;
    private AdvisorRepository $advisorRepository;

    public function __construct(EntityManagerInterface $entityManager, AdvisorRepository $advisorRepository, ValidatorInterface $validator)
    {
        $this->entityManager = $entityManager;
        $this->validator = $validator;
        $this->advisorRepository = $advisorRepository;
    }

    public function create(CreateAdvisorDTO $advisorDTO): JsonResponse
    {
        if ($responseWithErrors = $this->performValidationForCreate($advisorDTO)) {
            return $responseWithErrors;
        }

        $newAdvisor = Advisor::createFromDto($advisorDTO);
        $this->entityManager->persist($newAdvisor);
        $this->entityManager->flush();

        return new JsonResponse($newAdvisor->toArray(), Response::HTTP_CREATED);
    }

    public function delete(string $id): JsonResponse
    {
        $advisor = $this->advisorRepository->find(Uuid::fromString($id));
        if (is_null($advisor)) {
            return new JsonResponse(['error' => sprintf('Advisor with %s id was not found', $id)], Response::HTTP_NOT_FOUND);
        }

        $this->entityManager->remove($advisor);
        $this->entityManager->flush();

        return new JsonResponse(['result' => sprintf('Advisor %s was deleted successfully', $id)]);
    }

    public function getAdvisor(GetAdvisorsFilter $filter, ?string $id)
    {
        if ($id) {
            $advisor = $this->advisorRepository->find(Uuid::fromString($id));
            if (is_null($advisor)) {
                return new JsonResponse(['error' => sprintf('Advisor with %s id was not found', $id)], Response::HTTP_NOT_FOUND);
            }

            return new JsonResponse([$advisor->toArray()]);
        }

        if ($responseWithErrors = $this->performValidationForGet($filter)) {
            return $responseWithErrors;
        }

        $result = $this->advisorRepository->getByFilter($filter);

        return new JsonResponse(array_map(
            static function (Advisor $advisor): array {
                return $advisor->toArray();
            },
            $result
        ));
    }

    public function update(UpdateAdvisorDTO $advisorDTO, string $id): JsonResponse
    {
        $advisor = $this->advisorRepository->find(Uuid::fromString($id));
        if (is_null($advisor)) {
            return new JsonResponse(['error' => sprintf('Advisor with %s id was not found', $id)], Response::HTTP_NOT_FOUND);
        }

        $advisor->updateByDto($advisorDTO);

        if ($advisorDTO->languages) {
            foreach ($advisor->getLanguages() as $language) {
                $this->entityManager->remove($language);
            }
            $this->entityManager->flush();

            $collection = [];
            foreach ($advisorDTO->languages as $languageDTO) {
                $collection[] = AdvisorLanguage::createFromDto($languageDTO, $advisor);
            }

            $advisor->setLanguages(new ArrayCollection($collection));
        }

        $this->entityManager->flush();

        return new JsonResponse($advisor->toArray());
    }

    private function performValidationForCreate(CreateAdvisorDTO $advisorDTO): ?JsonResponse
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

    private function performValidationForGet(GetAdvisorsFilter $filter): ?JsonResponse
    {
        $result = [];
        $errors = $this->validator->validate($filter);
        if (count($errors) > 0) {
            $result[] = (string) $errors;
        }

        foreach ($filter->languages as $language) {
            $errors = $this->validator->validate($language);
            if (count($errors) > 0) {
                $result[] = (string) $errors;
            }
        }

        return count($result) > 0 ? new JsonResponse(['errors' => $result], Response::HTTP_BAD_REQUEST) : null;
    }
}
