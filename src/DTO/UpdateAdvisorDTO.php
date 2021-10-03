<?php
declare(strict_types=1);

namespace App\DTO;

class UpdateAdvisorDTO
{
    public ?string $name;

    public ?string $description = null;

    public ?bool $availability = null;

    public ?MoneyDTO $pricePerMinute;

    /** @var LanguageDTO[] */
    public ?array $languages;
}