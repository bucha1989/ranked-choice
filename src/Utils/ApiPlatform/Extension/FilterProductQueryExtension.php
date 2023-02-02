<?php

namespace App\Utils\ApiPlatform\Extension;

use ApiPlatform\Core\Bridge\Doctrine\Orm\Extension\QueryCollectionExtensionInterface;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Extension\QueryItemExtensionInterface;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Util\QueryNameGeneratorInterface as LegacyQueryNameGeneratorInterface;
use App\Entity\Product;
use Doctrine\ORM\QueryBuilder;

class FilterProductQueryExtension implements QueryCollectionExtensionInterface, QueryItemExtensionInterface
{

    public function applyToCollection(QueryBuilder                      $queryBuilder,
                                      LegacyQueryNameGeneratorInterface $queryNameGenerator,
                                      string                            $resourceClass,
                                      string                            $operationName = null)
    {
        $this->andWhere($queryBuilder, $resourceClass);
    }

    public function applyToItem(QueryBuilder                      $queryBuilder,
                                LegacyQueryNameGeneratorInterface $queryNameGenerator,
                                string                            $resourceClass,
                                array                             $identifiers,
                                string                            $operationName = null,
                                array                             $context = [])
    {
        $this->andWhere($queryBuilder, $resourceClass);
    }

    private function andWhere(QueryBuilder $queryBuilder, string $resourceClass)
    {
        if (Product::class !== $resourceClass) {
            return;
        }

        $rootAlias = $queryBuilder->getRootAliases()[0];

        $queryBuilder->andWhere(
            sprintf("%s.isDeleted = '0'", $rootAlias)
        );
    }
}