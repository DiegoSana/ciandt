<?php
declare(strict_types=1);

namespace Vendor\DiegoAttribute\Block\Product;

use Magento\Catalog\Model\Product;
use Magento\Framework\Registry;
use Magento\Framework\View\Element\Template\Context;
use Magento\Framework\View\Element\Template;

class Form extends Template
{
    /**
     * @var Product|null
     */
    protected ?Product $_product = null;

    /**
     * @var Registry
     */
    protected Registry $_coreRegistry;

    /**
     * @param Context $context
     * @param Registry $registry
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        array $data = []
    ) {
        $this->_coreRegistry = $registry;
        parent::__construct($context, $data);
    }

    /**
     * Get form action url
     *
     * @return string
     */
    public function getFormAction(): string
    {
        return $this->getUrl('diego_attribute/attribute/save', ['_secure' => true]);
    }

    /**
     * Get current product
     *
     * @return Product
     */
    public function getProduct(): Product
    {
        if (!$this->_product) {
            if ($this->_coreRegistry->registry('product')) {
                $this->_product = $this->_coreRegistry->registry('product');
            } else {
                throw new \LogicException('Product is not defined');
            }
        }
        return $this->_product;
    }
}
