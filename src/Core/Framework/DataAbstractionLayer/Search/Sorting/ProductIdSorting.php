<?php declare(strict_types=1);

namespace Vis\VisuallySearchProducts\Core\Framework\DataAbstractionLayer\Search\Sorting;

use Shopware\Core\Framework\DataAbstractionLayer\Search\Sorting\FieldSorting;

class ProductIdSorting extends FieldSorting
{
    private $ids = [];

    public function addId(string $id)
    {
        $this->ids[] = $id;
    }

    public function setIds(array $ids)
    {
        $this->ids = $ids;
    }

    public function getIds()
    {
        return $this->ids;
    }
}