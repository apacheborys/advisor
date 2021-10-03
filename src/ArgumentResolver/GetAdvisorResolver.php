<?php
declare(strict_types=1);

namespace App\ArgumentResolver;

use App\DTO\LanguageDTO;
use App\Filter\GetAdvisorsFilter;
use App\ValueObject\PriceRangeValueObject;
use App\ValueObject\SortDirectionValueObject;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ArgumentValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;

class GetAdvisorResolver implements ArgumentValueResolverInterface
{
    public function supports(Request $request, ArgumentMetadata $argument): bool
    {
        if ($argument->getType() != GetAdvisorsFilter::class) {
            return false;
        }

        return true;
    }

    public function resolve(Request $request, ArgumentMetadata $argument): iterable
    {
        $filter = new GetAdvisorsFilter();

        if ($request->get('limit')) {
            $filter->limit = (int) $request->get('limit');
        }

        if ($request->get('offset')) {
            $filter->offset = (int) $request->get('offset');
        }

        if ($request->get('sort')) {
            $filter->sortDirection = new SortDirectionValueObject($request->get('sort'));
        }

        if ($request->get('name')) {
            $filter->name = $request->get('name');
        }

        if (is_array($request->get('languages'))) {
            $collection = [];
            foreach ($request->get('languages') as $language) {
                $tempLang = new LanguageDTO();
                $tempLang->locale = $language;

                $collection[] = $tempLang;
            }

            $filter->languages = $collection;
        }

        if (is_array($request->get('price_range'))
            && isset($request->get('price_range')['min'])
            && isset($request->get('price_range')['max'])
            && isset($request->get('price_range')['currency'])
        ) {
            $filter->priceRange = new PriceRangeValueObject(
                (int) $request->get('price_range')['min'],
                (int) $request->get('price_range')['max'],
                $request->get('price_range')['currency']
            );
        }

        yield $filter;
    }
}
