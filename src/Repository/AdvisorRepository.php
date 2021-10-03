<?php
declare(strict_types=1);

namespace App\Repository;

use App\Entity\Advisor;
use App\Entity\AdvisorLanguage;
use App\Filter\GetAdvisorsFilter;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Advisor|null find($id, $lockMode = null, $lockVersion = null)
 * @method Advisor[] findAll()
 * @method Advisor[] findBy(array $criteria, ?array $orderBy = null, $limit = null, $offset = null)
 * @method Advisor|null findOneBy(array $criteria, ?array $orderBy = null)
 */
class AdvisorRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Advisor::class);
    }

    /**
     * @return Advisor[]|null
     */
    public function getByFilter(GetAdvisorsFilter $filter): ?array
    {
        $qb = $this->createQueryBuilder('a');

        $conditions = [];
        $parameters = [];
        if ($filter->priceRange) {
            $conditions[] = $qb->expr()->andX(
                $qb->expr()->between('a.pricePerMinute.amount', ':priceMin', ':priceMax'),
                $qb->expr()->eq('a.pricePerMinute.currency.code', ':currency')
            );

            $parameters = array_merge(
                $parameters,
                [
                    'priceMin' => $filter->priceRange->getMin(),
                    'priceMax' => $filter->priceRange->getMax(),
                    'currency' => $filter->priceRange->getCurrency(),
                ]
            );
        }

        if ($filter->languages) {
            $listExpression = [];
            foreach ($filter->languages as $index => $language) {
                $listExpression[] = $qb->expr()->eq('al.locale', ':lang' . $index);
                $parameters['lang' . $index] = $language->locale;
            }
            $conditions[] = $qb->expr()->orX(...$listExpression);
        }

        if ($filter->name) {
            $conditions[] = $qb->expr()->like('a.name', ':name');
            $parameters['name'] = '%' . $filter->name . '%';
        }

        $qb
            ->join(
                AdvisorLanguage::class,
                'al',
                Join::WITH,
                $qb->expr()->eq('a.id', 'al.advisor')
            )
            ->setFirstResult($filter->offset)
            ->setMaxResults($filter->limit)
            ->orderBy('a.name', $filter->sortDirection->getValue())
        ;

        if (!empty($conditions)) {
            $qb
                ->where($qb->expr()->andX(...$conditions))
                ->setParameters($parameters);
        }

        return $qb->getQuery()->execute();
    }
}
