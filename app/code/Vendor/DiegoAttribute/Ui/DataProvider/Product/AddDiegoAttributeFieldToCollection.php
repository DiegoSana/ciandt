<?php
declare(strict_types=1);

namespace Vendor\DiegoAttribute\Ui\DataProvider\Product;

use Magento\Framework\Data\Collection;
use Magento\Ui\DataProvider\AddFieldToCollectionInterface;

class AddDiegoAttributeFieldToCollection implements AddFieldToCollectionInterface
{

    /**
     * @inheritdoc
     */
    public function addField(Collection $collection, $field, $alias = null)
    {
        $collection->addFieldToSelect($field, $alias);
    }
}
