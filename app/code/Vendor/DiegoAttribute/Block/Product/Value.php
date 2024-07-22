<?php
declare(strict_types=1);

namespace Vendor\DiegoAttribute\Block\Product;

use Magento\Catalog\Model\Product;
use Magento\Framework\Registry;
use Magento\Framework\View\Element\Template\Context;
use Magento\Framework\View\Element\Template;

class Value extends Template
{
    /**
     * @var null|Product
     */
    protected ?Product $_product = null;

    /**
     * @param Context $context
     * @param Registry $registry
     * @param array $data
     */
    public function __construct(
        protected Context $context,
        protected Registry $registry,
        array $data = []
    ) {
        parent::__construct($context, $data);
    }

    /**
     * Get current product
     *
     * @return Product
     */
    public function getProduct(): Product
    {
        if (!$this->_product) {
            if ($this->registry->registry('product')) {
                $this->_product = $this->registry->registry('product');
            } else {
                throw new \LogicException('Product is not defined');
            }
        }
        return $this->_product;
    }
}
