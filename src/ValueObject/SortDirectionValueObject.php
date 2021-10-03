<?php
declare(strict_types=1);

namespace App\ValueObject;

class SortDirectionValueObject
{
    public const SORT_DIRECTION = [self::ASC, self::DESC];

    public const ASC = 'ASC';
    public const DESC = 'DESC';

    private string $value;

    public function __construct(string $sortDirection = self::ASC)
    {
        if (!in_array($sortDirection, self::SORT_DIRECTION)) {
            $sortDirection = self::ASC;
        }

        $this->value = $sortDirection;
    }

    public function getValue(): string
    {
        return $this->value;
    }
}