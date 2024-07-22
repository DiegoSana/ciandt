<?php
declare(strict_types=1);

namespace Vendor\DiegoAttribute\Model\Product\Attribute\Source;

use Magento\Framework\Data\OptionSourceInterface;

class Status implements OptionSourceInterface
{

    /**
     * Return diego product attribute status
     *
     * @return array[]
     */
    public function toOptionArray()
    {
        return [
            [
                'value' => 1,
                'label' => __('Defined')
            ],
            [
                'value' => 0,
                'label' => __('Undefined')
            ]
        ];
    }
}
