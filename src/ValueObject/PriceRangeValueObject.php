<?php
declare(strict_types=1);

namespace App\ValueObject;

class PriceRangeValueObject
{
    private int $min;

    private int $max;

    private string $currency;

    public function __construct(int $min, int $max, string $currency)
    {
        if ($min > $max) {
            $min = $max;
        }

        $this->min = $min;
        $this->max = $max;
        $this->currency = $currency;
    }

    public function getMin(): int
    {
        return $this->min;
    }

    public function getMax(): int
    {
        return $this->max;
    }

    public function getCurrency(): string
    {
        return $this->currency;
    }
}
