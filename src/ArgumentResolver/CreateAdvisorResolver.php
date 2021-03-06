<?php
declare(strict_types=1);

namespace App\ArgumentResolver;

use App\DTO\CreateAdvisorDTO;
use Symfony\Component\HttpKernel\Controller\ArgumentValueResolverInterface;

class CreateAdvisorResolver extends AbstractAdvisorResolver implements ArgumentValueResolverInterface
{
    function getDtoClass(): string
    {
        return CreateAdvisorDTO::class;
    }

    function getDtoLocalClassName(): string
    {
        return 'CreateAdvisorDTO';
    }
}
