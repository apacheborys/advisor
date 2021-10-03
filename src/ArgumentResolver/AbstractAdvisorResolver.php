<?php
declare(strict_types=1);

namespace App\ArgumentResolver;

use App\DTO\MoneyDTO;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;

abstract class AbstractAdvisorResolver
{
    public function supports(Request $request, ArgumentMetadata $argument): bool
    {
        if ($argument->getType() != $this->getDtoClass()) {
            return false;
        }

        $rawData = json_decode($request->getContent(), true);
        if (!$rawData) {
            return false;
        }

        $itemsQty = 0;
        $reflect = new \ReflectionClass($this->getDtoClass());
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

    public function resolve(Request $request, ArgumentMetadata $argument): iterable
    {
        $rawData = json_decode($request->getContent(), true);

        $reflect = new \ReflectionClass($this->getDtoClass());
        $dto = new ($this->getDtoClass())();

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
                        str_replace($this->getDtoLocalClassName(), substr($result_match[2], 0, -2), $this->getDtoClass()),
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

    abstract function getDtoClass(): string;

    abstract function getDtoLocalClassName(): string;
}