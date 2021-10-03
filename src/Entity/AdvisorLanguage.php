<?php

declare(strict_types=1);

namespace App\Entity;

use App\DTO\LanguageDTO;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Index;
use Ramsey\Uuid\UuidInterface;

/**
 * @ORM\Entity
 * @ORM\Table(name="advisor_languages", indexes={
 *      @Index(name="advisor_FK_idx", columns={"advisor_id"})
 * })
 */
class AdvisorLanguage
{
    /**
     * @ORM\ManyToOne(targetEntity="Advisor", inversedBy="id")
     */
    private Advisor $advisor;

    /**
     * @var string
     * @ORM\Id
     * @ORM\Column(type="string", length=35)
     */
    private string $locale;

    public static function createFromDto(LanguageDTO $dto, Advisor $advisor): self
    {
        $instance = new self();
        $instance->advisor = $advisor;
        $instance->locale = $dto->locale;

        return $instance;
    }

    public function toArray(): array
    {
        return [$this->locale];
    }
}
