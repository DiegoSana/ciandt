<?php
declare(strict_types=1);

namespace Vendor\DiegoAttribute\Ui\DataProvider\Product;

use Magento\Framework\Data\Collection;
use Magento\Framework\Exception\LocalizedException;
use Magento\Ui\DataProvider\AddFilterToCollectionInterface;

class AddDiegoAttributeFilterToGrid implements AddFilterToCollectionInterface
{

    /**
     * @inheritdoc
     *
     * @throws LocalizedException
     */
    public function addFilter(Collection $collection, $field, $condition = null): void
    {
        if (isset($condition['eq'])) {
            if ($condition['eq'] === '0') {
                $collection->addFieldToFilter($field, [['null' => true],['']]);
            } else {
                $collection->addFieldToFilter($field, [['notnull' => false],['neq' => '']]);
            }
        }
    }
}
