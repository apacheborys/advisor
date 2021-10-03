<?php
declare(strict_types=1);

namespace App\ArgumentResolver;

use App\DTO\UpdateAdvisorDTO;
use Symfony\Component\HttpKernel\Controller\ArgumentValueResolverInterface;

class UpdateAdvisorResolver extends AbstractAdvisorResolver implements ArgumentValueResolverInterface
{
    function getDtoClass(): string
    {
        return UpdateAdvisorDTO::class;
    }

    function getDtoLocalClassName(): string
    {
        return 'UpdateAdvisorDTO';
    }
}