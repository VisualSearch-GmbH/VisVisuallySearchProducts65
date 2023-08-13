<?php declare(strict_types=1);

namespace Vis\VisuallySearchProducts\Core\Framework\DataAbstractionLayer\Dbal;

use Vis\VisuallySearchProducts\Core\Framework\DataAbstractionLayer\Search\Sorting\ProductIdSorting;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\Dbal\CriteriaQueryBuilder;
use Shopware\Core\Framework\DataAbstractionLayer\Dbal\EntityDefinitionQueryHelper;
use Shopware\Core\Framework\DataAbstractionLayer\Dbal\FieldResolver\CriteriaPartResolver;
use Shopware\Core\Framework\DataAbstractionLayer\Dbal\JoinGroupBuilder;
use Shopware\Core\Framework\DataAbstractionLayer\Dbal\QueryBuilder;
use Shopware\Core\Framework\DataAbstractionLayer\EntityDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\Filter;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Parser\SqlQueryParser;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Term\EntityScoreQueryBuilder;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Term\SearchTermInterpreter;

class CriteriaQueryBuilderDecorator extends CriteriaQueryBuilder
{
    private $decoratedService;

    /***
     * @var EntityDefinitionQueryHelper
     */
    private $helper;

    public function __construct(
        CriteriaQueryBuilder        $decoratedService,
        SqlQueryParser              $parser,
        EntityDefinitionQueryHelper $helper,
        SearchTermInterpreter       $interpreter,
        EntityScoreQueryBuilder     $scoreBuilder,
        JoinGroupBuilder            $joinGrouper,
        CriteriaPartResolver        $criteriaPartResolver
    )
    {
        $this->decoratedService = $decoratedService;
        $this->helper = $helper;

        parent::__construct($parser, $helper, $interpreter, $scoreBuilder, $joinGrouper, $criteriaPartResolver);
    }

    public function getDecorated(): CriteriaQueryBuilder
    {
        return $this->decoratedService;
    }

    public function addSortings(EntityDefinition $definition, Criteria $criteria, array $sortings, QueryBuilder $query, Context $context): void
    {
        foreach ($sortings as $sorting) {
            if ($sorting instanceof ProductIdSorting) {

                $accessor = $this->helper->getFieldAccessor($sorting->getField(), $definition, $definition->getEntityName(), $context);

                $ids = '0x' . implode(',0x', array_reverse($sorting->getIds()));
                if (empty($ids)) {
                    continue;
                }

                $query->addOrderBy('FIELD(' . $accessor . ',' . $ids . ')', 'DESC');
            } else {
                $this->decoratedService->addSortings($definition, $criteria, [$sorting], $query, $context);
            }
        }
    }

    public function build(QueryBuilder $query, EntityDefinition $definition, Criteria $criteria, Context $context, array $paths = []): QueryBuilder
    {
        return parent::build($query, $definition, $criteria, $context, $paths);
    }

    public function addFilter(EntityDefinition $definition, ?Filter $filter, QueryBuilder $query, Context $context): void
    {
        parent::addFilter($definition, $filter, $query, $context);
    }

}