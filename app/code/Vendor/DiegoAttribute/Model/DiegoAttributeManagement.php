<?php
declare(strict_types=1);

namespace Vendor\DiegoAttribute\Model;

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\StateException;
use Magento\Store\Model\ScopeInterface;
use Vendor\DiegoAttribute\Api\DiegoAttributeManagementInterface;
use Magento\Framework\App\Config\ConfigResource\ConfigInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;

class DiegoAttributeManagement implements DiegoAttributeManagementInterface
{
    protected const ATTRIBUTE_CODE = 'diego_attribute';
    protected const DIEGO_ATTRIBUTE_SETTINGS_ENABLED = 'diego_attribute/settings/enabled';

    /**
     * @param ProductRepositoryInterface $productRepository
     * @param ConfigInterface $config
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        private readonly ProductRepositoryInterface $productRepository,
        private readonly ConfigInterface $config,
        private readonly ScopeConfigInterface $scopeConfig
    ) {
    }

    /**
     * @inheritdoc
     *
     * @throws NoSuchEntityException
     */
    public function get(string $sku, int $store = null): string
    {
        return (string) $this->productRepository->get($sku, false, $store)
            ->getCustomAttribute(self::ATTRIBUTE_CODE)?->getValue();
    }

    /**
     * @inheritdoc
     * @throws CouldNotSaveException
     * @throws InputException
     * @throws StateException
     */
    public function set(ProductInterface $product, string $value): void
    {
        $product->setCustomAttribute(self::ATTRIBUTE_CODE, $value);
        $this->productRepository->save($product);
    }

    /**
     * @inheritdoc
     */
    public function setStatus(bool $status, null|int $store = null): void
    {
        if (!$store) {
            $this->config->saveConfig(self::DIEGO_ATTRIBUTE_SETTINGS_ENABLED, (int) $status);
        } else {
            $this->config->saveConfig(
                self::DIEGO_ATTRIBUTE_SETTINGS_ENABLED,
                (int) $status,
                ScopeInterface::SCOPE_STORES,
                $store
            );
        }
    }

    /**
     * @inheritdoc
     */
    public function getStatus(null|int $store = null): bool
    {
        if (!$store) {
            return !!$this->scopeConfig->getValue(self::DIEGO_ATTRIBUTE_SETTINGS_ENABLED);
        } else {
            return !!$this->scopeConfig->getValue(
                self::DIEGO_ATTRIBUTE_SETTINGS_ENABLED,
                ScopeInterface::SCOPE_STORES,
                $store
            );
        }
    }
}
