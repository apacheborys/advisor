<?php
declare(strict_types=1);

namespace App\Entity;

use App\DTO\CreateAdvisorDTO;
use Doctrine\ORM\Mapping as ORM;
use Money\Currency;
use Money\Money;
use Ramsey\Uuid\Doctrine\UuidGenerator;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

/**
 * @ORM\Entity
 * @ORM\Table(name="advisor")
 */
class Advisor
{
    /**
     * @var \Ramsey\Uuid\UuidInterface
     *
     * @ORM\Id
     * @ORM\Column(type="uuid", unique=true)
     * @ORM\GeneratedValue(strategy="CUSTOM")
     * @ORM\CustomIdGenerator(class=UuidGenerator::class)
     */
    private UuidInterface $id;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=80, nullable=false)
     */
    private string $name;

    /**
     * @var string|null
     *
     * @ORM\Column(type="string", nullable=true)
     */
    private ?string $description;

    /**
     * @var bool|null
     *
     * @ORM\Column(type="boolean", nullable=true)
     */
    private ?bool $availability;

    /**
     * @var Money
     *
     * @ORM\Embedded(class="\Money\Money")
     */
    private Money $pricePerMinute;

    /**
     * @ORM\OneToMany(targetEntity="AdvisorLanguage", mappedBy="idAdvisor", cascade={"ALL"}, indexBy="locale")
     */
    private $languages;

    /**
     * @return UuidInterface
     */
    public function getId(): UuidInterface
    {
        return $this->id;
    }

    public static function createFromDto(CreateAdvisorDTO $dto): self
    {
        $instance = new self();
        $instance->id = Uuid::uuid4();
        $instance->name = $dto->name;
        $instance->availability = $dto->availability;
        $instance->description = $dto->description;
        $instance->pricePerMinute = new Money($dto->pricePerMinute->amount, new Currency($dto->pricePerMinute->currency));

        foreach ($dto->languages as $languageDTO) {
            $instance->languages[] = AdvisorLanguage::createFromDto($languageDTO, $instance);
        }

        return $instance;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id->toString(),
            'name' => $this->name,
            'description' => $this->description,
            'availability' => $this->availability,
            'pricePerMinute' => [
                'amount' => $this->pricePerMinute->getAmount(),
                'currency' => $this->pricePerMinute->getCurrency()->getCode(),
            ],
            'languages' => array_map(
                static function (AdvisorLanguage $language): array {
                    return $language->toArray();
                },
                $this->languages->toArray()
            )
        ];
    }
}
