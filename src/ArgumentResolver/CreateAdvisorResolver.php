<?php
declare(strict_types=1);

namespace App\ArgumentResolver;

use App\DTO\CreateAdvisorDTO;
use App\DTO\MoneyDTO;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ArgumentValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;

class CreateAdvisorResolver implements ArgumentValueResolverInterface
{
    public function supports(Request $request, ArgumentMetadata $argument)
    {
        $rawData = json_decode($request->getContent(), true);
        if (!$rawData) {
            return false;
        }

        $itemsQty = 0;
        $reflect = new \ReflectionClass(CreateAdvisorDTO::class);
        foreach ($reflect->getProperties() as $property) {
            if (!isset($rawData[$property->getName()]) && !$property->getType()->allowsNull()) {
                return false;
            } elseif (isset($rawData[$property->getName()])) {
                $itemsQty++;
            }
        }

        if ($itemsQty != count($rawData)) {
            return false;
        }

        return true;
    }

    public function resolve(Request $request, ArgumentMetadata $argument)
    {
        $rawData = json_decode($request->getContent(), true);

        $reflect = new \ReflectionClass(CreateAdvisorDTO::class);
        $dto = new CreateAdvisorDTO();

        foreach ($reflect->getProperties() as $property) {
            if (!isset($rawData[$property->getName()])) {
                continue;
            }

            if ($property->getType()->getName() === MoneyDTO::class) {
                $dto->{$property->getName()} = $this->createSubDTO(MoneyDTO::class, $rawData[$property->getName()]);
            } elseif ($property->getType()->getName() === 'array') {
                $result_match = $collection = [];
                preg_match('/(^\/\*\* @var )(.*)( \*\/)/', $property->getDocComment(), $result_match);

                foreach ($rawData[$property->getName()] as $itemOfRawData) {
                    $collection[] = $this->createSubDTO(
                        str_replace('CreateAdvisorDTO', substr($result_match[2], 0, -2), CreateAdvisorDTO::class),
                        $itemOfRawData
                    );
                }

                $dto->{$property->getName()} = $collection;
            } else {
                $dto->{$property->getName()} = $rawData[$property->getName()];
            }
        }

        yield $dto;
    }

    private function createSubDTO(string $class, array $data): object
    {
        $reflect = new \ReflectionClass($class);
        $dto = new $class();

        foreach ($reflect->getProperties() as $property) {
            $dto->{$property->getName()} = $data[$property->getName()];
        }

        return $dto;
    }
}
