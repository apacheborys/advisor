<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Index;
use Ramsey\Uuid\UuidInterface;

/**
 * @ORM\Entity
 * @ORM\Table(name="advisor_languages", indexes={
 *      @Index(name="advisor_FK_idx", columns={"id_advisor"})
 * })
 */
class AdvisorLanguage
{
    /**
     * @var UuidInterface
     * @ORM\Id
     * @ORM\Column(type="uuid", nullable=false)
     */
    private UuidInterface $idAdvisor;

    /**
     * @var string
     * @ORM\Id
     * @ORM\Column(type="string", length=2)
     */
    private string $locale;
}
