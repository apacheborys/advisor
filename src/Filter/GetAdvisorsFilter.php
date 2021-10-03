<?php
declare(strict_types=1);

namespace App\Filter;

use App\DTO\LanguageDTO;
use App\ValueObject\PriceRangeValueObject;
use App\ValueObject\SortDirectionValueObject;

class GetAdvisorsFilter
{
    public int $limit = 20;

    public int $offset = 0;

    public SortDirectionValueObject $sortDirection;

    public ?string $name = null;

    /** @var LanguageDTO[] */
    public array $languages = [];

    public ?PriceRangeValueObject $priceRange = null;

    public function __construct()
    {
        $this->sortDirection = new SortDirectionValueObject();
    }
}
