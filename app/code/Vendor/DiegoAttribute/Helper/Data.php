<?php

namespace Vendor\DiegoAttribute\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Store\Model\ScopeInterface;

class Data extends AbstractHelper
{

    public const IS_ENBALED_PATH = 'diego_attribute/settings/enabled';

    public function getIsEnabled($storeId): bool
    {
        return !!$this->scopeConfig->getValue(self::IS_ENBALED_PATH, ScopeInterface::SCOPE_STORE, $storeId);
    }
}