<?php
declare(strict_types=1);

namespace Vendor\DiegoAttribute\Api;

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\StateException;

interface DiegoAttributeManagementInterface
{

    /**
     * GET Diego attribute value
     *
     * @param string $sku
     * @param int|null $store
     * @return string
     */
    public function get(string $sku, int $store = null): string;

    /**
     * Set product diego attribute
     *
     * @param ProductInterface $product
     * @param string $value
     *
     * @return void
     */
    public function set(ProductInterface $product, string $value): void;

    /**
     * Set diego attribute status
     *
     * @param bool $status
     * @param int|null $store
     *
     * @return void
     */
    public function setStatus(bool $status, null|int $store): void;

    /**
     * Get diego attribute status
     *
     * @param int|null $store
     *
     * @return bool
     */
    public function getStatus(null|int $store): bool;
}
